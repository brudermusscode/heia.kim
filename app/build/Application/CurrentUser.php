<?php

namespace Heiakim\Application;

use Heiakim\Model\User;
use Heiakim\Model\Session;
use Heiakim\Application\Session as ApplicationSession;

class CurrentUser
{

  /**
   * @return User
   */
  public static function get()
  {



    return self::is_authenticated()
      ? (object) User::find(
        Session::get("user")["user_id"]
      )
      : User::guest();
  }

  /**
   * @return User
   */
  public static function guest()
  {
    return User::guest();
  }

  /**
   * Checks, if a valid session with persistent user cookies that
   * match a session in the database is existent.
   *
   * @return bool
   */
  public static function is_authenticated()
  {
    $cookies_to_exist_count = count(CurrentUser::$persistent_cookies);
    $cookies_exist_count = 0;

    /**
     * Increase the existing cookie count for each cookie that is
     * set in the browser.
     */
    foreach (CurrentUser::$persistent_cookies as $cookie)
      if (Cookie::get($cookie))
        $cookies_exist_count++;

    /**
     * Check the existing cookies count against the necessary
     * count of existing cookies and return false if it's mismatching.
     */
    if ($cookies_exist_count < $cookies_to_exist_count) {
      self::logout();

      return false;
    }

    /**
     * @var array
     */
    $session = ApplicationSession::get("session") ?? null;

    /**
     * Session cookie contains user entry with user_id?
     */
    if (!$session) {
      self::logout();

      return false;
    }

    /**
     * @var ?int
     */
    $session_user_id = $session->user_id ?? null;

    /**
     * Session's user id doesn't match the cookies user id?
     */
    if ($session_user_id !== (int) Cookie::get(CurrentUser::$persistent_cookies[0])) {
      self::logout();

      return false;
    }

    /**
     * @var ?Session
     */
    $Session = Session::where([
      "user_id" => Cookie::get(CurrentUser::$persistent_cookies[0]),
      "token" => Cookie::get(CurrentUser::$persistent_cookies[1]),
    ])
      ->first();

    /**
     * Session exists?
     */
    if (!$Session) {
      self::logout();

      return false;
    }

    return true;
  }

  /**
   * @return void
   */
  public static function logout()
  {
    ApplicationSession::remove("session");

    foreach (self::$persistent_cookies as $cookie)
      Cookie::set($cookie, "", "-1 minute");
  }
}
