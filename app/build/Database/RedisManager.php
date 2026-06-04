<?php

namespace Heiakim\Database;

use Heiakim\Exception\DatabaseException;

class RedisManager
{

  private ?\Redis $connection = null;

  public function __construct()
  {

    if (!$this->connection) {
      $env = _env();

      $Redis = new \Redis();
      $Redis->connect($env->REDIS_HOST, $env->REDIS_PORT);
      $Redis->auth($env->REDIS_PASSWORD);

      $this->connection = $Redis;
    }
  }

  /**
   * @return ?\Redis
   */
  public function connection()
  {
    return $this->connection
      ?? throw new DatabaseException("Failed connecting to Redis");;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @param int $ttl
   * @return bool
   */
  public function set(string $key, mixed $value, int $ttl = 0)
  {
    $value = serialize($value);

    return $ttl
      ? $this->connection()->setex($key, $ttl, $value)
      : $this->connection()->set($key, $value);
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get(string $key)
  {
    $value = $this->connection()->get($key);

    return $value === false
      ? null
      : unserialize($value);
  }

  /**
   * @param string $key
   * @return bool
   */
  public function delete(string $key)
  {
    return $this->connection()->del($key) > 0;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function exists(string $key)
  {
    return $this->connection()->exists($key);
  }

  /**
   * @return bool
   */
  public function flush()
  {
    return $this->connection()->flushAll(sync: false);
  }
}
