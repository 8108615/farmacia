<?php

namespace App\Services;

use App\Models\User;

class AccesoCaja
{
    public readonly ?int $sucursal;
    public readonly bool $puedeOperar;
    private readonly bool $soloPropios;

    public function __construct(private readonly User $usuario)
    {
        if ($usuario->hasRole('SUPER ADMIN')) {
            $this->sucursal = null;
            $this->puedeOperar = true;
            $this->soloPropios = false;

            return;
        }

        $consultaSucursal = $usuario->hasAnyRole(['ADMINISTRADOR', 'CONTADOR']);
        $vendedor = $usuario->hasRole('VENDEDOR');
        abort_unless($consultaSucursal || $vendedor, 403);

        $empleado = $usuario->empleado;
        abort_unless($empleado?->estado === 'activo' && $empleado->sucursal_id, 403,
            'Tu cuenta necesita una ficha de empleado activa con una sucursal asignada.');

        $this->sucursal = (int) $empleado->sucursal_id;
        $this->puedeOperar = $vendedor;
        $this->soloPropios = !$consultaSucursal;
    }

    public function limitarSucursal($consulta, string $columna)
    {
        if ($this->sucursal !== null) {
            $consulta->where($columna, $this->sucursal);
        }

        return $consulta;
    }

    public function limitarArqueos($consulta)
    {
        $this->limitarSucursal($consulta, 'arqueos.sucursal_id');
        if ($this->soloPropios) {
            $consulta->where('arqueos.usuario_id', $this->usuario->id);
        }

        return $consulta;
    }

    public function puedeOperarEn(int $sucursal): bool
    {
        return $this->puedeOperar && ($this->sucursal === null || $this->sucursal === $sucursal);
    }
}
