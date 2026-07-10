<?php

namespace App\Enums;

use App\Models\Status;

enum StatusEnum: string
{
    case NUEVO      = 'Nuevo';
    case ASIGNADO   = 'Asignado';
    case EN_PROCESO = 'En Proceso';
    case RESUELTO   = 'Resuelto';
    case CANCELADO  = 'Cancelado';

    /**
     * Resuelve el ID real del estatus desde la base de datos por nombre.
     * Usa cache de por request para no hacer múltiples queries.
     */
    public function id(): int
    {
        static $cache = [];

        if (!isset($cache[$this->value])) {
            $cache[$this->value] = Status::where('name', $this->value)->value('id');
        }

        return $cache[$this->value];
    }

    /**
     * IDs que se consideran "abiertos" (requieren atención).
     */
    public static function openIds(): array
    {
        return [
            self::NUEVO->id(),
            self::ASIGNADO->id(),
            self::EN_PROCESO->id(),
        ];
    }

    /**
     * IDs que se consideran "cerrados".
     */
    public static function closedIds(): array
    {
        return [
            self::RESUELTO->id(),
            self::CANCELADO->id(),
        ];
    }
}
