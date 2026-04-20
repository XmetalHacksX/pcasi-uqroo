<?php

namespace App\Enums;

enum RolesEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case RESPONSABLE_SGC = 'responsable_sgc';
    case RESPONSABLE_GENERO = 'responsable_genero';
    case RESPONSABLE_INFRAESTRUCTURA = 'responsable_infraestructura';
}
