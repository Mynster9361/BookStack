<?php

/**
 * Database configuration options.
 *
 * Changes to these config files are not supported by BookStack and may break upon updates.
 * Configuration should be altered via the `.env` file or environment variables.
 * Do not edit this file unless you're happy to maintain any changes yourself.
 */

// REDIS
// Split out configuration into an array
if (env('REDIS_SERVERS', false)) {
    $redisDefaults = ['host' => '127.0.0.1', 'port' => '6379', 'database' => '0', 'password' => null];
    $redisServers = explode(',', trim(env('REDIS_SERVERS', '127.0.0.1:6379:0'), ','));
    $redisConfig = ['client' => 'predis'];
    $cluster = count($redisServers) > 1;

    if ($cluster) {
        $redisConfig['clusters'] = ['default' => []];
    }

    foreach ($redisServers as $index => $redisServer) {
        $redisServerDetails = explode(':', $redisServer);

        $serverConfig = [];
        $configIndex = 0;
        foreach ($redisDefaults as $configKey => $configDefault) {
            $serverConfig[$configKey] = ($redisServerDetails[$configIndex] ?? $configDefault);
            $configIndex++;
        }

        if ($cluster) {
            $redisConfig['clusters']['default'][] = $serverConfig;
        } else {
            $redisConfig['default'] = $serverConfig;
        }
    }
}

// MYSQL
// Split out port from host if set
$mysql_host = env('DB_HOST', 'localhost');
$mysql_host_exploded = explode(':', $mysql_host);
$mysql_port = env('DB_PORT', 3306);
if (count($mysql_host_exploded) > 1) {
    $mysql_host = $mysql_host_exploded[0];
    $mysql_port = intval($mysql_host_exploded[1]);
}


return [

    // Default database connection name.
    // Options: mysql, mysql_testing
    'default' => env('DB_CONNECTION', 'mysql'),

    // Available database connections
    // Many of those shown here are unsupported by BookStack.
    'connections' => [

        'mysql' => [
            'driver'    => 'mysql',
            'url' => env('DATABASE_URL'),
            'host'      => $mysql_host,
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'port'      => $mysql_port,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'prefix_indexes' => true,
            'strict'    => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mysql_testing' => [
            'driver'    => 'mysql',
            'url' => env('TEST_DATABASE_URL'),
            'host'      => $mysql_host,
            'database'  => 'bookstack-test',
            'username'  => env('MYSQL_USER', 'bookstack-test'),
            'password'  => env('MYSQL_PASSWORD', 'bookstack-test'),
            'port'      => $mysql_port,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'prefix_indexes' => true,
            'strict'    => false,
        ],

    ],

    // Migration Repository Table
    // This table keeps track of all the migrations that have already run for
    // your application. Using this information, we can determine which of
    // the migrations on disk haven't actually been run in the database.
    'migrations' => 'migrations',

    // Redis configuration to use if set
    'redis' => env('REDIS_SERVERS', false) ? $redisConfig : [],

];
