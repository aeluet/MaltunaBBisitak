<?php

return [

    'title' => '¡Bienvenidos a Miguel Altuna!',

    'scan' => [
        'help' => '¿Tienes un código QR? Pásalo por el lector y se imprimirá tu etiqueta.',
        'placeholder' => 'O escribe tu código',
        'submit' => 'Entrar',
    ],

    'meetings' => [
        'heading' => 'Reuniones de hoy',
    ],

    'groups' => [
        'heading' => '¿Venís en grupo?',
        'help' => 'Elige tu grupo y regístrate.',
        'people' => '{1} 1 persona|[2,*] :count personas',
    ],

    'register' => [
        'heading' => 'Regístrate',
        'submit' => 'Registrarme e imprimir la etiqueta',
        'scan_phone' => 'O escanea el código QR con el móvil y rellena allí tus datos.',
        'back' => 'Volver al inicio',
    ],

    'fields' => [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido(s)',
        'origin' => '¿De dónde vienes? (centro, empresa…)',
        'email' => 'Correo electrónico',
    ],

    'done' => [
        'welcome' => '¡Te damos la bienvenida, :name!',
        'meeting' => '¡Hola, :name! Te has registrado en: :meeting.',
        'label' => 'Recoge tu etiqueta de la impresora.',
        'label_retry' => 'Si la etiqueta no se ha impreso, pulsa aquí.',
    ],

    'errors' => [
        'unknown_code' => 'No hemos encontrado ese código. Pregunta en recepción, por favor.',
        'not_approved' => 'Esta visita no está confirmada. Pregunta en recepción, por favor.',
        'not_today' => 'Tu visita es otro día: :date.',
        'finished' => 'Esta visita ha terminado. Pregunta en recepción, por favor.',
    ],

    'mobile' => [
        'visit_heading' => 'Regístrate en la visita',
        'meeting_heading' => 'Regístrate en la reunión',
        'closed' => 'Lo sentimos, el registro ya está cerrado.',
        'submit' => 'Registrarme',
        'thanks' => '¡Gracias, :name!',
        'visit_done' => 'Al llegar, pasa este código QR por el lector del pedestal para recoger tu etiqueta.',
        'meeting_done' => 'Te has registrado en: :meeting.',
    ],

];
