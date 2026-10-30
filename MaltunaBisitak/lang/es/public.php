<?php

return [

    'school' => 'Miguel Altuna',
    'language' => 'Idioma',

    'title' => 'Solicitar una visita',
    'intro' => '¿Quieres conocer Miguel Altuna? Elige el día, cuéntanos quiénes sois y envía la solicitud. Tras revisarla, recibirás la respuesta por email.',
    'required_hint' => 'Los campos con asterisco (*) son obligatorios.',

    'sections' => [
        'types' => '¿Qué queréis conocer?',
        'date' => '¿Qué día?',
        'group' => '¿Quiénes sois?',
        'visitors' => 'Visitantes',
        'notes' => '¿Algo más?',
    ],

    'fields' => [
        'types' => 'Tipo de visita',
        'other_type' => 'Otra cosa (especificar)',
        'date' => 'Fecha',
        'origin' => 'Centro educativo, empresa u organización',
        'visit_kind' => '¿Cómo venís?',
        'individual' => 'Individualmente',
        'group' => 'En grupo',
        'group_size' => 'Número de personas del grupo',
        'visitors' => 'Visitantes',
        'first_name' => 'Nombre',
        'last_name' => 'Apellido(s)',
        'email' => 'Correo electrónico',
        'country' => 'País',
        'notes' => 'Observaciones',
    ],

    'calendar' => [
        'month_format' => 'MMMM [de] YYYY',
        'available' => 'Libre',
        'full' => 'Completo',
        'no_dates' => 'En este momento no hay días disponibles para visitas. Vuelve a intentarlo más adelante.',
    ],

    'visitors' => [
        'contact' => 'Persona de contacto',
        'contact_hint' => 'Enviaremos la respuesta de la solicitud a esta dirección.',
        'other' => 'Otro visitante',
        'add' => '+ Añadir otro visitante',
        'remove' => 'Quitar',
        'group_hint' => 'Si venís en grupo, basta con los datos de la persona responsable.',
    ],

    'privacy' => 'Acepto que mis datos se utilicen únicamente para gestionar esta visita.',
    'submit' => 'Enviar solicitud',

    'validation' => [
        'date_required' => 'Elige un día libre en el calendario.',
        'date_unavailable' => 'Ese día no está disponible para visitas. Elige otro.',
        'date_full' => 'Ese día ya está completo. Elige otro.',
        'type_required' => 'Elige al menos un tipo de visita o escribe qué queréis conocer.',
        'contact_email_required' => 'El correo electrónico de la persona de contacto es obligatorio.',
        'privacy' => 'Debes aceptar el uso de tus datos para enviar la solicitud.',
    ],

    'thanks' => [
        'title' => '¡Muchas gracias!',
        'message' => 'Hemos recibido tu solicitud. Tras revisarla, recibirás la respuesta por email.',
        'date' => 'Día solicitado: :date',
        'back' => 'Hacer otra solicitud',
    ],

    'mail' => [
        'received' => [
            'subject' => 'Hemos recibido tu solicitud de visita',
            'greeting' => '¡Hola, :name!',
            'intro' => 'Gracias por tu interés en visitar Miguel Altuna. Estos son los datos que hemos recibido:',
            'next_steps' => 'Después de revisar la solicitud, te enviaremos otro email con la respuesta.',
        ],
        'regards' => 'Un cordial saludo,',
        'signature' => 'Miguel Altuna',
    ],

];
