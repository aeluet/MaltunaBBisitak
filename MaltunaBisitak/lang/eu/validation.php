<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Balidazio mezuak
    |--------------------------------------------------------------------------
    |
    | Aplikazioan erabiltzen diren arauen mezuak. Hemen ez dagoen arau batek
    | ingelesezko mezua erakutsiko du (fallback_locale).
    |
    */

    'accepted' => ':attribute eremua onartu behar da.',
    'after' => ':attribute eremuak :date ondorengo data izan behar du.',
    'after_or_equal' => ':attribute eremuak :date edo ondorengo data izan behar du.',
    'array' => ':attribute eremuak zerrenda bat izan behar du.',
    'before' => ':attribute eremuak :date aurreko data izan behar du.',
    'before_or_equal' => ':attribute eremuak :date edo aurreko data izan behar du.',
    'between' => [
        'numeric' => ':attribute eremuak :min eta :max artean egon behar du.',
        'string' => ':attribute eremuak :min eta :max karaktere artean izan behar ditu.',
        'array' => ':attribute eremuak :min eta :max elementu artean izan behar ditu.',
    ],
    'boolean' => ':attribute eremuak egia edo gezurra izan behar du.',
    'confirmed' => ':attribute eremuaren berrespena ez dator bat.',
    'date' => ':attribute eremuak data baliodun bat izan behar du.',
    'date_format' => ':attribute eremuak :format formatua izan behar du.',
    'different' => ':attribute eta :other desberdinak izan behar dira.',
    'digits' => ':attribute eremuak :digits digitu izan behar ditu.',
    'email' => ':attribute eremuak posta elektroniko baliodun bat izan behar du.',
    'exists' => 'Hautatutako :attribute ez da baliozkoa.',
    'image' => ':attribute eremuak irudi bat izan behar du.',
    'in' => 'Hautatutako :attribute ez da baliozkoa.',
    'integer' => ':attribute eremuak zenbaki oso bat izan behar du.',
    'max' => [
        'numeric' => ':attribute eremua ezin da :max baino handiagoa izan.',
        'string' => ':attribute eremuak ezin ditu :max karaktere baino gehiago izan.',
        'file' => ':attribute fitxategiak ezin ditu :max kilobyte baino gehiago izan.',
        'array' => ':attribute eremuak ezin ditu :max elementu baino gehiago izan.',
    ],
    'min' => [
        'numeric' => ':attribute eremuak gutxienez :min izan behar du.',
        'string' => ':attribute eremuak gutxienez :min karaktere izan behar ditu.',
        'file' => ':attribute fitxategiak gutxienez :min kilobyte izan behar ditu.',
        'array' => ':attribute eremuak gutxienez :min elementu izan behar ditu.',
    ],
    'numeric' => ':attribute eremuak zenbaki bat izan behar du.',
    'prohibited' => ':attribute eremua ez dago baimenduta.',
    'password' => [
        'letters' => ':attribute eremuak gutxienez letra bat izan behar du.',
        'mixed' => ':attribute eremuak gutxienez letra larri bat eta letra xehe bat izan behar ditu.',
        'numbers' => ':attribute eremuak gutxienez zenbaki bat izan behar du.',
        'symbols' => ':attribute eremuak gutxienez ikur bat izan behar du.',
        'uncompromised' => 'Emandako :attribute datu-ihes batean agertu da. Aukeratu beste bat.',
    ],
    'regex' => ':attribute eremuaren formatua ez da baliozkoa.',
    'required' => ':attribute eremua derrigorrezkoa da.',
    'required_if' => ':attribute eremua derrigorrezkoa da :other :value denean.',
    'required_without' => ':attribute eremua derrigorrezkoa da :values ez dagoenean.',
    'same' => ':attribute eta :other bat etorri behar dira.',
    'string' => ':attribute eremuak testu bat izan behar du.',
    'unique' => ':attribute hori erabilita dago jada.',
    'uuid' => ':attribute eremuak UUID baliodun bat izan behar du.',

    'attributes' => [
        'name' => 'izena',
        'email' => 'posta elektronikoa',
        'password' => 'pasahitza',
        'role' => 'rola',
        'first_name' => 'izena',
        'last_name' => 'abizena',
        'phone' => 'telefonoa',
        'origin' => 'jatorria',
        'country' => 'herrialdea',
        'document_number' => 'NAN zenbakia',
        'visit_date' => 'bisitaren data',
        'group_size' => 'taldearen tamaina',
        'title' => 'izenburua',
        'location' => 'lekua',
        'date' => 'data',
        'starts_at' => 'hasiera ordua',
        'company' => 'enpresa',
        'checked_in_at' => 'sarrera ordua',
        'signature' => 'sinadura',
        'max_visits' => 'bisita kopuru maximoa',
        'notes' => 'oharrak',
        'types' => 'bisita mota',
        'other_type' => 'beste bisita mota',
        'is_group' => 'bisita modua',
        'visitors' => 'bisitariak',
        'visitors.*.first_name' => 'izena',
        'visitors.*.last_name' => 'abizena',
        'visitors.*.email' => 'posta elektronikoa',
        'visitors.*.country' => 'herrialdea',
        'privacy' => 'datuen erabilera',
    ],

];
