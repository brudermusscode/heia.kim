<?php

// TODO: Parse .env file once only.

return [
  "database" => [
    "default" => [
      'driver' => 'mysql',
      'host' => _env("MYSQL_HOST"),
      'port' => _env("MYSQL_PORT"),
      'database' => _env("MYSQL_DATABASE"),
      'username' => _env("MYSQL_USER"),
      'password' => _env("MYSQL_PASSWORD"),
      'charset' => _env("MYSQL_CHARSET"),
      'collation' => _env("MYSQL_COLLATION"),
      'prefix' => '',
    ],
    "CLI" => [
      'driver' => 'mysql',
      'host' => _env("MYSQL_HOST_CLI"),
      'port' => _env("MYSQL_PORT"),
      'database' => _env("MYSQL_DATABASE"),
      'username' => _env("MYSQL_USER"),
      'password' => _env("MYSQL_PASSWORD"),
      'charset' => _env("MYSQL_CHARSET"),
      'collation' => _env("MYSQL_COLLATION"),
      'prefix' => '',
    ]
  ],
];
