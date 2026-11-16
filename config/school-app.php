<?php

return [
    'path' => base_path(env('SCHOOL_APP_PATH', 'school-app')),

    /*
     * When true (default), a failing migration or .sql statement aborts the
     * whole app install so the failure is visible in the admin upload form.
     * Set SCHOOL_APP_SQL_STRICT=false to install anyway and only report the
     * errors as warnings.
     */
    'strict_schema' => env('SCHOOL_APP_SQL_STRICT', true),
];
