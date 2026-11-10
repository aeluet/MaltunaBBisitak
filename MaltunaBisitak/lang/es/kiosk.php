<?php

return [

    'title' => '¡Bienvenidos a Miguel Altuna!',

    'home' => [
        'meetings' => 'Reunión',
        'meetings_help' => 'Regístrate en una reunión de hoy',
        'visits' => 'Visita',
        'visits_help' => '¿Venís en grupo? Regístrate o crea una visita nueva',
        'today' => '{0} Hoy no hay|{1} Hoy: 1|[2,*] Hoy: :count',
    ],

    'scan' => [
        'help' => '¿Tienes un código QR? Pásalo por el lector y se imprimirá tu etiqueta.',
        'placeholder' => 'O escribe tu código',
        'submit' => 'Entrar',
    ],

    'meetings' => [
        'heading' => 'Reuniones de hoy',
        'empty' => 'Hoy no hay reuniones.',
    ],

    'groups' => [
        'heading' => '¿Venís en grupo?',
        'help' => 'Elige tu grupo y regístrate.',
        'people' => '{1} 1 persona|[2,*] :count personas',
        'registered' => '{0} Todavía no se ha registrado nadie|{1} Se ha registrado 1 persona|[2,*] Se han registrado :count personas',
        'empty' => 'Hoy no hay visitas de grupo.',
        'new_heading' => 'Visita nueva',
        'new_help' => 'Si vuestro grupo no está en la lista, cread una visita nueva y registraos.',
        'new_origin' => 'Nombre del grupo / De dónde (centro, empresa…)',
        'new_submit' => 'Crear',
        'attendees_heading' => 'Personas registradas',
        'attendees_empty' => 'Todavía no se ha registrado nadie.',
        'status_inside' => 'Dentro',
        'status_waiting' => 'Por llegar',
        'status_left' => 'Ha salido',
        'refresh' => 'Actualizar',
        'print_all' => 'Imprimir todas las etiquetas',
        'print_all_help' => 'Se registrará la entrada de quienes figuran como pendientes de llegar.',
        'printed' => '{1} Imprimiendo 1 etiqueta…|[2,*] Imprimiendo :count etiquetas…',
        'nothing_to_print' => 'No hay etiquetas que imprimir.',
    ],

    'register' => [
        'heading' => 'Regístrate',
        'submit' => 'Registrarme e imprimir la etiqueta',
        'scan_phone' => 'O escanea el código QR con el móvil y rellena allí tus datos.',
        'back' => 'Volver al inicio',
        'back_list' => 'Volver',
    ],

    'fields' => [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido(s)',
        'origin' => '¿De dónde vienes? (centro, empresa…)',
        'email' => 'Correo electrónico',
        'country' => 'País',
        'country_placeholder' => 'Elige el país (opcional)',
    ],

    'privacy' => [
        'notice' => 'Al registrarte, aceptas las normas de protección de datos.',
        'link' => 'Información sobre protección de datos',
    ],

    'done' => [
        'welcome' => '¡Te damos la bienvenida, :name!',
        'meeting' => '¡Hola, :name! Te has registrado en: :meeting.',
        'label' => 'Recoge tu etiqueta de la impresora.',
        'label_retry' => 'Si la etiqueta no se ha impreso, pulsa aquí.',
        'labels' => 'Recoged las etiquetas de la impresora.',
        'labels_retry' => 'Si las etiquetas no se han impreso, pulsa aquí.',
    ],

    'keyboard' => [
        'close' => 'Cerrar',
        'space' => 'Espacio',
        'enter' => 'Siguiente',
        'accents' => 'áé',
        'letters' => 'abc',
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
