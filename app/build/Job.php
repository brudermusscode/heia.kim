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
   * @var object
   */
  protected Setting $App;

  /**
   * @var string
   */
  protected $last_executed_pattern = '/\/\/ L::(.*?)#/s';

  /**
   * @return \Heiakim\Database\RedisManager
   */
  public function redis()
  {
    return new \Heiakim\Database\RedisManager;
  }

  /**
   * @return object
   */
  public function App()
  {
    return (object) Setting::first()->getAttributes();
  }

  /**
   * @param string $timeinterval
   * @return void
   *
   * NOTE: Will die on error.
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

  /**
   * @param string $file
   * @return ?string|void
   */
  public function last_executed(string $file)
  {

    $file_content = file_get_contents($file);

    preg_match_all($this->last_executed_pattern, $file_content, $matches);

    # Set the timestamp to now, if none is set by now.
    if (!isset($matches[1][0]))
      return $this->set_last_executed($file);

    return $matches[1][0];
  }

  /**
   * @param string $file
   * @return void
   */
  public function update_last_executed(string $file)
  {

    $search = $this->last_executed_pattern;
    $replace = $this->current_time_string();
    $file_content = file_get_contents($file);

    /**
     * @var array $matches
     *
     * NOTE: preg_match_all will always generate an array with
     * keys 0 and 1 which are set, but can be empty.
     */
    preg_match_all($this->last_executed_pattern, $file_content, $matches);

    # Set the timestamp to now, if none exists by now.
    if (!$matches[1])
      return $this->set_last_executed($file);

    # Update the content.
    $updated_file_content = preg_replace($search, $replace, $file_content);

    # Write new content to file.
    file_put_contents($file, $updated_file_content);
  }

  /**
   * Writes the current timestamp in format Y-m-d H:i:s to the
   * Job file as a top comment.
   *
   * @param string $file
   * @return string
   * @see Heiakim\Job::current_time_string()
   */
  public function set_last_executed(string $file)
  {

    $search = "/<\?php/";
    $replace = "<?php\n\n" . $this->current_time_string();
    $file_content = file_get_contents($file);
    $updated_file_content = preg_replace($search, $replace, $file_content);

    # Write to file.
    file_put_contents($file, $updated_file_content);

    return CURRENT_TIMESTAMP;
  }

  /**
   * @return string
   */
  public function current_time_string()
  {
    return "// L::" . CURRENT_TIMESTAMP . "#";
  }
}
