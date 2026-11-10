<?php

return [

    'title' => 'Welcome to Miguel Altuna!',

    'home' => [
        'meetings' => 'Meeting',
        'meetings_help' => "Sign in to one of today's meetings",
        'visits' => 'Visit',
        'visits_help' => 'Coming as a group? Sign in or create a new visit',
        'today' => '{0} None today|{1} Today: 1|[2,*] Today: :count',
    ],

    'scan' => [
        'help' => 'Do you have a QR code? Scan it at the reader and your label will be printed.',
        'placeholder' => 'Or type your code',
        'submit' => 'Enter',
    ],

    'meetings' => [
        'heading' => "Today's meetings",
        'empty' => 'There are no meetings today.',
    ],

    'groups' => [
        'heading' => 'Coming as a group?',
        'help' => 'Choose your group and sign in.',
        'people' => '{1} 1 person|[2,*] :count people',
        'registered' => '{0} Nobody has signed in yet|{1} 1 person has signed in|[2,*] :count people have signed in',
        'empty' => 'There are no group visits today.',
        'new_heading' => 'New visit',
        'new_help' => 'If your group is not on the list, create a new visit and sign in.',
        'new_origin' => 'Group name / Where you are from (school, company…)',
        'new_submit' => 'Create',
        'attendees_heading' => 'Signed in',
        'attendees_empty' => 'Nobody has signed in yet.',
        'status_inside' => 'Inside',
        'status_waiting' => 'Not arrived',
        'status_left' => 'Left',
        'refresh' => 'Refresh',
        'print_all' => 'Print all labels',
        'print_all_help' => 'Everyone marked as not arrived will be checked in.',
        'printed' => '{1} Printing 1 label…|[2,*] Printing :count labels…',
        'nothing_to_print' => 'There are no labels to print.',
    ],

    'register' => [
        'heading' => 'Sign in',
        'submit' => 'Sign in and print my label',
        'scan_phone' => 'Or scan the QR code with your phone and fill in your details there.',
        'back' => 'Back to the start',
        'back_list' => 'Back',
    ],

    'fields' => [
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'origin' => 'Where are you from? (school, company…)',
        'email' => 'Email',
        'country' => 'Country',
        'country_placeholder' => 'Choose your country (optional)',
    ],

    'privacy' => [
        'notice' => 'By signing in, you accept the data protection rules.',
        'link' => 'Data protection information',
    ],

    'done' => [
        'welcome' => 'Welcome, :name!',
        'meeting' => 'Welcome, :name! You are registered for: :meeting.',
        'label' => 'Please collect your label from the printer.',
        'label_retry' => 'If your label was not printed, tap here.',
        'labels' => 'Please collect the labels from the printer.',
        'labels_retry' => 'If the labels were not printed, tap here.',
    ],

    'keyboard' => [
        'close' => 'Close',
        'space' => 'Space',
        'enter' => 'Next',
        'accents' => 'áé',
        'letters' => 'abc',
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
