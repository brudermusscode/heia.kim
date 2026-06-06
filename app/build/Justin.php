<?php

namespace Heiakim;

use Heiakim\Application\Application;
use Heiakim\Database\Redis;
use Heiakim\File\JSON;
use Heiakim\Trait\Translation as TraitTranslation;
use Heiakim\Trait\ProcessesRequests;
use Illuminate\Database\Eloquent\Model;

class Justin extends Model
{
  use TraitTranslation;
  use ProcessesRequests;

  /**
   * @var array
   */
  public $dd;

  public function __construct()
  {
    $this->dd = Application::get_default_dialogues();
  }

  /**
   * @var string
   */
  private static $oauth_credentials_file = "/config/security/oauth_credentials.json";

  /**
   * @return \Redis
   */
  public function redis()
  {
    return new \Heiakim\Database\RedisManager()
      ->connection();
  }

  /**
   * @return ?object
   */
  public function data()
  {
    return $this->exists ? (object) $this->getAttributes() : null;
  }

  /**
   * @return string
   */
  public function current_timestamp()
  {
    return date("Y-m-d H:i:s", time());
  }

  /**
   * Path has to be appended with a trailing slash /.
   *
   * @param string $path
   * @return string
   */
  public function template(string $path)
  {
    return ROOT . "/app/templates$path";
  }

  /**
   * Fetches the credentials for OAuth from the JSON file in configs.
   *
   * @param string $oauth_app The app to fetch for.
   * @return object The credentials.
   */
  public static function get_oauth_credentials(?string $oauth_app = null)
  {
    $path = ROOT . self::$oauth_credentials_file;
    $credentials = $oauth_app ? JSON::read($path)->$oauth_app : JSON::read($path);

    return $credentials;
  }

  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,, DATABASE INTERACTIONS ,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_transaction()
  {
    return $this->getConnection()
      ->beginTransaction();
  }

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_commit()
  {
    return $this->getConnection()
      ->commit();
  }

  /**
   * @return void
   * @throws \Throwable
   */
  public function db_rollback()
  {
    return $this->getConnection()
      ->rollBack();
  }

  /**
   * @return
   */
  // TODO: Make this static & non-static
  public static function findOrReturn(mixed $id = null, ?string $die_message = null)
  {
    return static::find($id)
      ?? die((new self)->error($die_message ?? "<strong>Model Instance not found.</strong>"));
  }
}
