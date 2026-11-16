<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | School Lookup Data Sources
    |--------------------------------------------------------------------------
    |
    | Ordered list of endpoints used to resolve school data by NPSN or NSM.
    | The service tries each source sequentially until one returns a result.
    |
    */

    'npsn_sources' => [
        // Primary — nationwide coverage from official Dapodik / belajar.id
        [
            'url' => env('SCHOOL_LOOKUP_API', 'https://api.fazriansyah.eu.org/v1/sekolah'),
            'type' => 'dapodik_fazrian',
            'timeout' => 8,
        ],

        // Fallback — public community API (limited to DKI Jakarta region)
        [
            'url' => 'https://api-sekolah-indonesia.vercel.app/sekolah',
            'type' => 'dapodik_vercel',
            'timeout' => 8,
        ],

        // Tertiary — official Kemendikdasmen reference page (unreliable, mostly dead)
        [
            'url' => 'https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/:npsn',
            'type' => 'kemendikdasmen_ref',
            'timeout' => 8,
        ],
    ],

    'nsm_sources' => [
        // EMIS Kemenag — madrasah data via NSM lookup
        [
            'url' => 'https://appmadrasah.kemenag.go.id/web/profile',
            'type' => 'emis_kemenag',
            'timeout' => 8,
        ],
    ],

];
