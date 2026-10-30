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

];
