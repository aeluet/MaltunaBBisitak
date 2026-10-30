<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación
    |--------------------------------------------------------------------------
    |
    | Solo las reglas que usa el formulario público. Las demás caen al inglés
    | (fallback_locale).
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'array' => 'El campo :attribute debe ser una lista.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'exists' => 'El :attribute seleccionado no es válido.',
    'in' => 'El :attribute seleccionado no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'prohibited' => 'El campo :attribute no está permitido.',
    'required' => 'El campo :attribute es obligatorio.',
    'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'string' => 'El campo :attribute debe ser un texto.',

    'attributes' => [
        'types' => 'tipo de visita',
        'other_type' => 'otro tipo de visita',
        'visit_date' => 'fecha de la visita',
        'origin' => 'centro, empresa u organización',
        'is_group' => 'modalidad',
        'group_size' => 'número de personas',
        'visitors' => 'visitantes',
        'visitors.*.first_name' => 'nombre',
        'visitors.*.last_name' => 'apellido',
        'visitors.*.email' => 'correo electrónico',
        'visitors.*.country' => 'país',
        'notes' => 'observaciones',
        'privacy' => 'uso de datos',
    ],

];
