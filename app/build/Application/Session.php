<?php

namespace Heiakim\Application;

use Heiakim\Http\Domain;

class Session
{
  /**
   * @var array
   */
  public static $localhost_domains = [
    "localhost",
    "127.0.0.1",
  ];

  /**
   * @var string
   */
  private static $session_name = "__sess__";

  /**
   * @param ?string $cacheExpire
   * @param ?string $cacheLimiter
   * @return void
   */
  public function __construct(
    ?string $cacheExpire = null,
    ?string $cacheLimiter = null
  ) {
    self::begin($cacheExpire, $cacheLimiter);
  }

  /**
   * @param string $key
   * @param string $snd_key
   * @return mixed
   */
  public static function get(?string $key = null, ?string $snd_key = null)
  {

    /**
     * If nothing is set, return the whole Session object.
     */
    if (!$key)
      return $_SESSION;

    if (!$snd_key) {
      return $_SESSION[$key] ?? null;
    } else {

      /**
       * Array key exists
       */
      if (array_key_exists($snd_key, $_SESSION[$key])) {
        /**
         * Second key is @object
         */
        if (is_object($_SESSION[$key])) {
          if (!empty($_SESSION[$key]->$snd_key))
            return $_SESSION[$key]->$snd_key;
          else
            return null;
        }

        /**
         * Second key is @array
         */
        else if (is_array($_SESSION[$key])) {
          if (array_key_exists($snd_key, $_SESSION[$key]))
            return $_SESSION[$key][$snd_key];
          else
            return null;
        }

        /**
         * Second key is string/int
         */
        else return $_SESSION;
      }

      /**
       * Second array key doesn't exist
       */
      else return null;
    }

    return null;
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public static function set(string $key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * @param string $key
   * @return void
   */
  public static function remove(string $key)
  {
    if (isset($_SESSION[$key]))
      unset($_SESSION[$key]);
  }

  /**
   * @return void
   */
  public static function clear()
  {
    session_unset();
  }

  /**
   * @param string $key
   * @return bool
   */
  public static function has(string $key)
  {
    return array_key_exists($key, $_SESSION);
  }

  /**
   * Begins a new session.
   *
   * @param string $cacheExpire
   * @param string $cacheLimiter
   * @return void
   */
  public static function begin(?string $cacheExpire = null, ?string $cacheLimiter = null)
  {

    if (session_status() !== PHP_SESSION_NONE)
      return;

    if ($cacheLimiter)
      session_cache_limiter($cacheLimiter);

    if ($cacheExpire)
      session_cache_expire($cacheExpire);

    session_set_cookie_params([
      'lifetime' => 438000 * 60,
      'path' => '/',
      'domain' => Domain::clean($_SERVER['HTTP_HOST']),

      // TODO: Implement better validation for localhost
      'secure' => current_env() === "dev" ? false : true,

      /**
       * httponly flag is for restricting access to the cookie.
       * Setting it to true will prevent JavaScript from being
       * able to access it. Good for session cookies.
       */
      'httponly' => true,

      /**
       * Lax will send cookies when coming or going to another,
       * external site. Strict will prevent this and offers the
       * highest security standart.
       */
      'samesite' => "Lax",
    ]);

    session_name(self::$session_name);

    session_start();
  }
}
