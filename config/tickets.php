<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Roles habilitados para ser responsables de tickets
    |--------------------------------------------------------------------------
    |
    | Lista de roles que se considerarán responsables a la hora de listar
    | usuarios para asignar tickets. Puedes ajustarla según las políticas
    | de tu organización.
    |
    */
    'responsable_roles' => [
        'supervisor_control',
        'coordinador_ti',
        'validador',
        'gerencia',
        'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | TTL de cache para el listado de responsables (en segundos)
    |--------------------------------------------------------------------------
    |
    | Ajusta el tiempo que permanecerá en cache el listado de usuarios
    | responsables. Usa 0 para deshabilitar el cacheo.
    |
    */
    'responsables_cache_ttl' => 600,
];
