<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Caja;
use App\Models\Sucursal;
use App\Services\AccesoCaja;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CierreCajaReporteController extends Controller
{
    public function index(Request $request)
    {
        $acceso = new AccesoCaja($request->user());
        $exportar = $request->validate(['exportar' => ['nullable', 'in:xlsx']])['exportar'] ?? null;
        $filtros = $request->validate([
            'desde' => ['nullable', 'required_with:hasta', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'required_with:desde', 'date_format:Y-m-d', 'after_or_equal:desde'],
            'sucursal' => ['nullable', 'integer', 'exists:sucursals,id'],
            'caja' => ['nullable', 'integer', 'exists:cajas,id'],
        ]);

        $hoy = today();
        $desde = $filtros['desde'] ?? $hoy->copy()->startOfMonth()->toDateString();
        $hasta = $filtros['hasta'] ?? $hoy->toDateString();
        $limite = Carbon::parse($hasta)->addDay()->toDateString();
        $divisa = Ajuste::query()->value('divisa') ?: 'Bs';

        $consulta = $acceso->limitarArqueos(DB::table('arqueos'))
            ->where('arqueos.estado', 'cerrado')
            ->where('arqueos.fecha_cierre', '>=', $desde)
            ->where('arqueos.fecha_cierre', '<', $limite)
            ->when($filtros['sucursal'] ?? null, fn ($q, $id) => $q->where('arqueos.sucursal_id', $id))
            ->when($filtros['caja'] ?? null, fn ($q, $id) => $q->where('arqueos.caja_id', $id));

        $resumen = (clone $consulta)->selectRaw(
            'COUNT(*) as turnos,
             COALESCE(SUM(CASE WHEN diferencia > 0 THEN diferencia ELSE 0 END), 0) as sobrantes,
             COALESCE(SUM(CASE WHEN diferencia < 0 THEN -diferencia ELSE 0 END), 0) as faltantes'
        )->first();

        $consulta
            ->join('cajas', 'cajas.id', '=', 'arqueos.caja_id')
            ->join('sucursals', 'sucursals.id', '=', 'arqueos.sucursal_id')
            ->join('users', 'users.id', '=', 'arqueos.usuario_id')
            ->select('arqueos.id', 'arqueos.fecha_cierre', 'arqueos.monto_contado', 'arqueos.diferencia',
                'cajas.nombre as caja', 'sucursals.nombre as sucursal', 'users.name as responsable')
            ->selectRaw('(arqueos.monto_inicial + arqueos.total_ingresos - arqueos.total_egresos) as esperado')
            ->orderByDesc('arqueos.fecha_cierre')->orderByDesc('arqueos.id');

        if ($exportar === 'xlsx') {
            return $this->exportarExcel($consulta, $desde, $hasta, $divisa, $resumen);
        }

        $cierres = $consulta->paginate(15)->appends($filtros + ['desde' => $desde, 'hasta' => $hasta]);
        $sucursales = $acceso->limitarSucursal(Sucursal::query(), 'id')->orderBy('nombre')->get(['id', 'nombre']);
        $cajas = $acceso->limitarSucursal(Caja::with('sucursal'), 'sucursal_id')->orderBy('nombre')->get(['id', 'nombre', 'sucursal_id']);

        return view('admin.arqueos.reporte-cierres', compact(
            'desde', 'hasta', 'filtros', 'sucursales', 'cajas', 'divisa', 'resumen', 'cierres',
        ));
    }

    private function exportarExcel(Builder $consulta, string $desde, string $hasta, string $divisa, object $resumen)
    {
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle('Cierres de caja');
        $hoja->fromArray([
            ['Reporte de cierres de caja'],
            ['Cierres del ' . $desde . ' al ' . $hasta],
            ['Turno', 'Caja', 'Sucursal', 'Responsable', 'Fecha de cierre',
                "Esperado ($divisa)", "Contado ($divisa)", "Diferencia ($divisa)"],
        ]);
        $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(15);
        $hoja->getStyle('A3:H3')->getFont()->setBold(true);
        $hoja->freezePane('A4');

        $fila = 4;
        foreach ($consulta->cursor() as $cierre) {
            $hoja->setCellValue("A{$fila}", (int) $cierre->id);
            foreach (['B' => $cierre->caja, 'C' => $cierre->sucursal, 'D' => $cierre->responsable] as $columna => $valor) {
                $hoja->setCellValueExplicit($columna . $fila, $valor, DataType::TYPE_STRING);
            }
            $hoja->setCellValue("E{$fila}", Date::PHPToExcel(Carbon::parse($cierre->fecha_cierre)));
            $hoja->fromArray([(float) $cierre->esperado, (float) $cierre->monto_contado, (float) $cierre->diferencia], null, "F{$fila}", true);
            $fila++;
        }

        if ($fila > 4) {
            $ultima = $fila - 1;
            $hoja->getStyle("E4:E{$ultima}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');
            $hoja->getStyle("F4:H{$ultima}")->getNumberFormat()->setFormatCode('#,##0.00');
        } else {
            $hoja->setCellValue('A4', 'No hay turnos cerrados para estos filtros.');
            $fila++;
        }

        $fila++;
        $hoja->fromArray([
            ['Turnos cerrados', (int) $resumen->turnos],
            ["Sobrantes ($divisa)", (float) $resumen->sobrantes],
            ["Faltantes ($divisa)", (float) $resumen->faltantes],
        ], null, "A{$fila}", true);
        $hoja->getStyle('B' . ($fila + 1) . ':B' . ($fila + 2))->getNumberFormat()->setFormatCode('#,##0.00');
        foreach (['A' => 26, 'B' => 27, 'C' => 27, 'D' => 35, 'E' => 23, 'F' => 20, 'G' => 20, 'H' => 21] as $columna => $ancho) {
            $hoja->getColumnDimension($columna)->setWidth($ancho);
        }

        return response()->streamDownload(function () use ($libro) {
            try {
                (new Xlsx($libro))->save('php://output');
            } finally {
                $libro->disconnectWorksheets();
            }
        }, "cierres-caja-{$desde}-{$hasta}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
