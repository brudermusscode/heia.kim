<?php

namespace Heiakim\Database;

use Exception;
use Ehann\RedisRaw\PredisAdapter;
use Predis\Client;

class Redis
{
  /**
   * Gets the credentials for redis from config file
   * @return array
   */
  private static function connection_credentials(): array
  {
    $env = _env();

    return [
      "scheme" => $env->REDIS_SCHEME,
      "host" => $env->REDIS_HOST,
      "port" => $env->REDIS_PORT,
      "password" => $env->REDIS_PASSWORD
    ];
  }

  /**
   * Build up a connection for redis service with data from connection.{env}.json
   *
   * @return object|string
   */
  public static function connect()
  {
    try {
      return new Client(self::connection_credentials());
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  /**
   * Open a new client for RediSearch 2
   * @return object
   */
  public static function search(): object
  {
    $conf = (object) self::connection_credentials();

    /**
     * Uses $hostname, $port, $db, $password
     */
    return (new PredisAdapter())->connect(
      $conf->host,
      $conf->port,
      0,
      $conf->password,
    );
  }

  /**
   * Validate and format a query for using correct redis syntax
   * If there is a syntax error with redis, it will return the normal
   * string value
   * @return array|string
   */
  public static function query(string $query): array|string
  {
    $Redis = self::connect();
    $query = str_replace(["\n", "\r"], " ", $query);
    $query = explode(", ", $query);

    foreach ($query as $key => $q) {
      $query[$key] = trim($q);
    }

    return $Redis->executeRaw($query);
  }
}
