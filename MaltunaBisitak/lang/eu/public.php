<?php

return [

    'school' => 'Miguel Altuna',
    'language' => 'Hizkuntza',

    'title' => 'Eskatu bisita bat',
    'intro' => 'Miguel Altuna ezagutu nahi duzu? Aukeratu eguna, esaguzu nor zareten eta bidali eskaera. Berrikusi ondoren, emailez jasoko duzu erantzuna.',
    'required_hint' => 'Izartxoa (*) duten eremuak derrigorrezkoak dira.',

    'sections' => [
        'types' => 'Zer ezagutu nahi duzue?',
        'date' => 'Zein egunetan?',
        'group' => 'Nor zarete?',
        'visitors' => 'Bisitariak',
        'notes' => 'Beste ezer?',
    ],

    'fields' => [
        'types' => 'Bisita mota',
        'other_type' => 'Beste zerbait (zehaztu)',
        'date' => 'Data',
        'origin' => 'Ikastetxea, enpresa edo erakundea',
        'visit_kind' => 'Nola zatozte?',
        'individual' => 'Bakarka',
        'group' => 'Taldean',
        'group_size' => 'Taldeko pertsona kopurua',
        'visitors' => 'Bisitariak',
        'first_name' => 'Izena',
        'last_name' => 'Abizena(k)',
        'email' => 'Posta elektronikoa',
        'country' => 'Herrialdea',
        'notes' => 'Oharrak',
    ],

    'calendar' => [
        'month_format' => 'YYYY[ko] MMMM',
        'available' => 'Libre',
        'full' => 'Beteta',
        'no_dates' => 'Une honetan ez dago bisitetarako egun librerik. Saiatu berriro aurrerago.',
    ],

    'visitors' => [
        'contact' => 'Harremanetarako pertsona',
        'contact_hint' => 'Eskaeraren erantzuna helbide honetara bidaliko dugu.',
        'other' => 'Beste bisitari bat',
        'add' => '+ Gehitu beste bisitari bat',
        'remove' => 'Kendu',
        'group_hint' => 'Taldean bazatozte, nahikoa da arduradunaren datuak ematea.',
    ],

    'privacy' => 'Onartzen dut nire datuak bisita hau kudeatzeko bakarrik erabiltzea.',
    'submit' => 'Bidali eskaera',

    'validation' => [
        'date_required' => 'Aukeratu egun libre bat egutegian.',
        'date_unavailable' => 'Egun hori ez dago bisitetarako erabilgarri. Aukeratu beste bat.',
        'date_full' => 'Egun hori bete egin da. Aukeratu beste bat.',
        'type_required' => 'Aukeratu bisita mota bat gutxienez, edo idatzi zer ezagutu nahi duzuen.',
        'contact_email_required' => 'Harremanetarako pertsonaren posta elektronikoa derrigorrezkoa da.',
        'privacy' => 'Datuen erabilera onartu behar duzu eskaera bidaltzeko.',
    ],

    'thanks' => [
        'title' => 'Eskerrik asko!',
        'message' => 'Zure eskaera jaso dugu. Berrikusi ondoren, emailez jasoko duzu erantzuna.',
        'date' => 'Eskatutako eguna: :date',
        'back' => 'Beste eskaera bat egin',
    ],

    'mail' => [
        'received' => [
            'subject' => 'Bisita eskaera jaso dugu',
            'greeting' => 'Kaixo, :name!',
            'intro' => 'Eskerrik asko Miguel Altuna bisitatzeko interesagatik. Hauek dira jaso ditugun datuak:',
            'next_steps' => 'Eskaera berrikusi ondoren, beste email bat bidaliko dizugu erantzunarekin.',
        ],
        'regards' => 'Agur bero bat,',
        'signature' => 'Miguel Altuna',
    ],

];
