<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | If set to true, the parser will throw exceptions for missing required
    | attributes or invalid structures according to the GPX 1.1 schema.
    |
    */
    'strict_mode' => true,

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone to use when parsing dates if not specified.
    | GPX times are usually in UTC (Z).
    |
    */
    'timezone' => 'UTC',
];
