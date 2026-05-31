<?php

namespace Heiakim\Validate;

class Validate
{

  /**
   * Checks a mails validity by chars and build.
   *
   * @param string $mail The mail to check for
   * @return bool Whether or not it was successful
   */
  public static function mail(string $mail)
  {
    return filter_var($mail, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
   * Checks a string length.
   *
   * @param int $min
   * @param int $max
   * @param string $str
   * @return bool
   */
  public static function string_length(int $min, int $max, string $str)
  {

    /**
     * Trim any whitespace from the beginning and end of the string.
     */
    $str = trim($str);

    return strlen($str) >= $min && strlen($str) <= $max;
  }

  /**
   * Checks a string matches a pattern.
   *
   * @param string $pattern
   * @param string $str
   * @return bool
   */
  public static function string_matches(string $pattern, string $str)
  {
    return preg_match($pattern, $str);
  }
}
