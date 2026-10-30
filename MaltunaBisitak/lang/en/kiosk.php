<?php

return [

    'title' => 'Welcome to Miguel Altuna!',

    'scan' => [
        'help' => 'Do you have a QR code? Scan it at the reader and your label will be printed.',
        'placeholder' => 'Or type your code',
        'submit' => 'Enter',
    ],

    'meetings' => [
        'heading' => "Today's meetings",
    ],

    'groups' => [
        'heading' => 'Coming as a group?',
        'help' => 'Choose your group and sign in.',
        'people' => '{1} 1 person|[2,*] :count people',
    ],

    'register' => [
        'heading' => 'Sign in',
        'submit' => 'Sign in and print my label',
        'scan_phone' => 'Or scan the QR code with your phone and fill in your details there.',
        'back' => 'Back to the start',
    ],

    'fields' => [
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'origin' => 'Where are you from? (school, company…)',
        'email' => 'Email',
    ],

    'done' => [
        'welcome' => 'Welcome, :name!',
        'meeting' => 'Welcome, :name! You are registered for: :meeting.',
        'label' => 'Please collect your label from the printer.',
        'label_retry' => 'If your label was not printed, tap here.',
    ],

    'errors' => [
        'unknown_code' => 'We could not find that code. Please ask at reception.',
        'not_approved' => 'This visit is not confirmed. Please ask at reception.',
        'not_today' => 'Your visit is on another day: :date.',
        'finished' => 'This visit has ended. Please ask at reception.',
    ],

    'mobile' => [
        'visit_heading' => 'Register for the visit',
        'meeting_heading' => 'Register for the meeting',
        'closed' => 'Sorry, registration is closed.',
        'submit' => 'Register',
        'thanks' => 'Thank you, :name!',
        'visit_done' => 'When you arrive, scan this QR code at the kiosk reader to get your label.',
        'meeting_done' => 'You are registered for: :meeting.',
    ],

];
