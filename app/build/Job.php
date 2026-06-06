<?php

namespace Heiakim;

use Heiakim\Application\Setting;
use Heiakim\Time\Time;
use Illuminate\Support\Carbon;
use RuntimeException;

class Job extends Justin
{

  /**
   * @var string
   */
  protected $table = "jobs";

  /**
   * @var array
   */
  protected $fillable = [
    "section",
    "class_name",
    "name",
    "description",
  ];

  /**
   * @param string $timeinterval
   * @return true|void
   */
  public static function has_run_before(string $timeinterval)
  {
    $class_name = class_basename(static::class);
    $JobClass = static::where("class_name", $class_name)->first();

    # Throw an exception if the Job class is not filled into the database.
    if (!$JobClass)
      throw new RuntimeException("No job found for class $class_name");

    /**
     * @var Carbon
     */
    $next_run = $JobClass->updated_at?->copy()
      ->modify($timeinterval);

    # If the unix time of the next run is still larger than the current time,
    # the time has not yet come my friend.
    if ($next_run?->isFuture()) {
      echo class_basename(static::class)
        . ": next run in "
        . Time::left($next_run, exact_hours: true, full: false)
        . "\n";

      return true;
    }

    # Set the new last run time.
    $JobClass->touch();

    echo class_basename(static::class) . ": started … ";
  }
}
