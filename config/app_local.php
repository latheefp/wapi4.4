<?php

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */



if (env('ENVIRONMENT') == "DEV") {
    $defaultDB = [
        'timezone' => 'UTC',
        'host' => env('DB_HOST'), // Replace 'db' with the name of the environment variable
        'username' => env('DB_USERNAME'), // Replace 'root' with the name of the environment variable
        'password' => env('DB_PASS'), // Replace 'rootPassword' with the name of the environment variable
        'database' => env('DB_DATABASE'), // Replace 'wapi' with the name of the environment variable
        'encoding' => 'utf8mb4',
        'cacheMetadata' => true,
    ];
}elseif(env('ENVIRONMENT') == "PROD") {
    $defaultDB = [
        'timezone' => 'UTC',
        'host' => env('DB_HOST'), // Replace 'db' with the name of the environment variable
        'username' => env('DB_USERNAME'), // Replace 'root' with the name of the environment variable
        'password' => env('DB_PASS'), // Replace 'rootPassword' with the name of the environment variable
        'database' => env('DB_DATABASE'), // Replace 'wapi' with the name of the environment variable
        'encoding' => 'utf8mb4',
        'cacheMetadata' => true,
    ];
}


debug($defaultDB);




//if (env('ENVIRONMENT') == "DEV") {
//    $defaultDB = [
//        'timezone' => 'UTC',
//        'host' => 'db', // Replace 'db' with the name of the environment variable
//        'username' => 'root', // Replace 'root' with the name of the environment variable
//        'password' => 'rootPassword', // Replace 'rootPassword' with the name of the environment variable
//        'database' => 'waapi', // Replace 'wapi' with the name of the environment variable
//        'encoding' => 'utf8mb4',
//        'cacheMetadata' => true,
//    ];
//}elseif(env('ENVIRONMENT') == "PROD") {
//    $defaultDB = [
//        'timezone' => 'UTC',
//        'host' => '217.79.255.175', // Replace 'db' with the name of the environment variable
//        'username' => 'wapi', // Replace 'root' with the name of the environment variable
//        'password' => 'uqd79NjB!TQGag8F', // Replace 'rootPassword' with the name of the environment variable
//        'database' => 'wapi', // Replace 'wapi' with the name of the environment variable
//        'encoding' => 'utf8mb4',
//        'cacheMetadata' => true,
//    ];
//}








$configuration= [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', 'a8deaefe26549912296afce4863fd4a2637785eb032f885a1afd5bbcc5f4ec960'),
    ],
    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => $defaultDB

    ],
//    'Caches' => [
//        'redis' => [
//            'className' => 'Redis',
//            'duration' => '+48 hours', // Adjust the caching duration as needed
//            'prefix' => 'cake_redis_', // Prefix for cache keys
//            'host' => 'redis', // Redis server host
//            'port' => 6379, // Redis server port
//            'fallback' => 'default', // Fallback cache configuration
//        ],
//    ],
//    'Redis' => [
//        'default' => [
//            'className' => 'Redis',
//            'scheme' => 'tcp',
//            'host' => 'redis',
//            'port' => 6379,
////            'password' => 'root123',
//            'database' => 0,
//        ],
//    ],
    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],
];

//debug($configuration);
return $configuration;

