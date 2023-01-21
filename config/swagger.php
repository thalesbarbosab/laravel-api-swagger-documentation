<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Swagger OpenApi Config
    |--------------------------------------------------------------------------
    |
    | This config can change some data about swagger doc api.
    | By default all sources is avaible in 'public/api-documentation' or in http source '/api-documentation'
    |
    */

    // Sources path to find a swagger anottation
    'sources' => [
        'app/Http/Controllers/Api/',
        //
    ]

];
