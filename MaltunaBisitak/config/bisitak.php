<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Barne-sareak
    |--------------------------------------------------------------------------
    |
    | Langileen eremura (saio hasiera eta panela) sar daitezkeen IPak edo CIDR
    | tarteak. Zerrenda hutsik badago, ez da murrizketarik aplikatzen.
    |
    */

    'internal_networks' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('BISITAK_INTERNAL_NETWORKS', '')),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Eskaera berrien jakinarazpenak
    |--------------------------------------------------------------------------
    |
    | Web orri publikotik bisita eskaera bat jasotzean, helbide honetara
    | bidaltzen da abisua. Hutsik badago, administratzaile aktibo guztiei.
    |
    */

    'notification_email' => env('BISITAK_NOTIFICATION_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Kanpo langileentzako dokumentuak
    |--------------------------------------------------------------------------
    |
    | Kanpo langileak irakurri eta onartu behar dituen dokumentuak, sinatu
    | aurretik. Bideak public/ karpetarekiko dira.
    |
    */

    'contractor_documents' => [
        [
            'title' => 'Kontratatutako enpresentzako segurtasun neurriak',
            'description' => 'Sartu-irtenak, seinaleztapena, altuerako lanak, makineria eta arrisku orokorrak.',
            'path' => 'dokumentuak/kontratatutako-enpresentzako-segurtasun-neurriak.pdf',
        ],
        [
            'title' => 'Larrialdi eta ebakuazio argibideak',
            'description' => 'Larrialdi baten aurrean nola jokatu, nola ebakuatu eta elkarguneak.',
            'path' => 'dokumentuak/larrialdi-eta-ebakuazio-argibideak.pdf',
        ],
    ],

];
