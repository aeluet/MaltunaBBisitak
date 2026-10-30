<?php

return [

    'school' => 'Miguel Altuna',
    'language' => 'Language',

    'title' => 'Request a visit',
    'intro' => 'Would you like to visit Miguel Altuna? Choose a day, tell us who you are and send your request. Once we have reviewed it, we will reply by email.',
    'required_hint' => 'Fields marked with an asterisk (*) are required.',

    'sections' => [
        'types' => 'What would you like to see?',
        'date' => 'Which day?',
        'group' => 'Who are you?',
        'visitors' => 'Visitors',
        'notes' => 'Anything else?',
    ],

    'fields' => [
        'types' => 'Type of visit',
        'other_type' => 'Something else (please specify)',
        'date' => 'Date',
        'origin' => 'School, company or organisation',
        'visit_kind' => 'How are you coming?',
        'individual' => 'Individually',
        'group' => 'As a group',
        'group_size' => 'Number of people in the group',
        'visitors' => 'Visitors',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'email' => 'Email',
        'country' => 'Country',
        'notes' => 'Comments',
    ],

    'calendar' => [
        'month_format' => 'MMMM YYYY',
        'available' => 'Available',
        'full' => 'Full',
        'no_dates' => 'There are no days available for visits at the moment. Please try again later.',
    ],

    'visitors' => [
        'contact' => 'Contact person',
        'contact_hint' => 'We will send our reply to this address.',
        'other' => 'Another visitor',
        'add' => '+ Add another visitor',
        'remove' => 'Remove',
        'group_hint' => 'If you are coming as a group, the details of the person in charge are enough.',
    ],

    'privacy' => 'I agree that my data will only be used to manage this visit.',
    'submit' => 'Send request',

    'validation' => [
        'date_required' => 'Please choose an available day in the calendar.',
        'date_unavailable' => 'That day is not available for visits. Please choose another one.',
        'date_full' => 'That day is already fully booked. Please choose another one.',
        'type_required' => 'Please choose at least one type of visit or tell us what you would like to see.',
        'contact_email_required' => "The contact person's email is required.",
        'privacy' => 'You must accept the use of your data to send the request.',
    ],

    'thanks' => [
        'title' => 'Thank you!',
        'message' => 'We have received your request. Once we have reviewed it, we will reply by email.',
        'date' => 'Requested day: :date',
        'back' => 'Make another request',
    ],

    'mail' => [
        'received' => [
            'subject' => 'We have received your visit request',
            'greeting' => 'Hello :name,',
            'intro' => 'Thank you for your interest in visiting Miguel Altuna. These are the details we received:',
            'next_steps' => 'Once we have reviewed your request, we will send you another email with our reply.',
        ],
        'regards' => 'Kind regards,',
        'signature' => 'Miguel Altuna',
    ],

];
