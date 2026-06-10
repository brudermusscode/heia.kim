<?php

namespace Heiakim\Validate;

use DateTime;

class Validate
{

  /**
   * Validate a given string to be a valid date for a birthday Y-m-d.
   *
   * @param string $day
   * @param string $month
   * @param string $year
   * @return ?DateTime|false
   */
  public static function birthday(string $day, string $month, string $year)
  {
    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $date_string = "{$year}-{$month}-{$day}";

    $date_pattern = "/^\d{4}-\d{2}-\d{2}$/";

    # Date pattern is invalid?
    if (!preg_match($date_pattern, $date_string))
      return null;

    $datetime = DateTime::createFromFormat("Y-m-d", $date_string);
    $min_date = new DateTime("1960-01-01");
    $max_date = new DateTime("2022-01-01");

    # Too old for osu!?
    if ($datetime < $min_date)
      return null;

    # Too young for osu!?
    if ($datetime > $max_date)
      return null;

    return $datetime;
  }

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
