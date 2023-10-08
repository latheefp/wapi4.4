<?php

use Cake\Datasource\ConnectionManager;

// Include CakePHP's autoload file (assuming CakePHP is properly installed)
require 'vendor/autoload.php';

// Load CakePHP configuration
$config = [
    'Datasources' => [
        'default' => [
            'timezone' => 'UTC',
            'host' => getenv('DB_HOST', ''), // Replace with the name of the environment variable
            'username' => getenv('DB_USERNAME', ''), // Replace with the name of the environment variable
            'password' => getenv('DB_PASSWORD', ''), // Replace with the name of the environment variable
            'database' => getenv('DB_DATABASE', ''), // Replace with the name of the environment variable
            'encoding' => 'utf8mb4',
            'cacheMetadata' => true,
        ],
    ],
];

// Initialize CakePHP's database connection
ConnectionManager::setConfig($config);

try {
    // Attempt to establish a database connection
    $connection = ConnectionManager::get('default');
    $connected = $connection->connect();

    if ($connected) {
        echo 'Connected to the database successfully.';
    } else {
        echo 'Failed to connect to the database.';
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
