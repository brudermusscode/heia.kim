<?php

namespace Heiakim;

use Heiakim\Application\Setting;
use Heiakim\Database\Redis;

class Job
{
  /**
   * @var object
   */
  protected $App;

  /**
   * @var string
   */
  protected $last_executed_pattern = '/\/\/ L::(.*?)#/s';

  /**
   * @return \Predis\Client
   */
  public function redis()
  {
    return Redis::connect();
  }

  /**
   * @return object
   */
  public function App()
  {
    return (object) Setting::first()->getAttributes();
  }

  /**
   * @return ?string|void
   */
  public function last_executed($FILE)
  {
    $file_content = file_get_contents($FILE);
    preg_match_all($this->last_executed_pattern, $file_content, $matches);

    /**
     * Timestamp doesn't exist?
     */
    if (!isset($matches[1][0]))
      return $this->set_last_executed($FILE);

    return $matches[1][0];
  }

  /**
   * @return bool
   */
  public function update_last_executed($FILE)
  {
    $file_content = file_get_contents($FILE);
    $search = $this->last_executed_pattern;
    $replace = "// L::" . date("Y-m-d H:i:s") . "#";

    /**
     * @var array $matches
     *
     * NOTE: preg_match_all will always generate an array with
     * keys 0 and 1 which are set, but can be empty.
     */
    preg_match_all($this->last_executed_pattern, $file_content, $matches);

    /**
     * Timestamp doesn't exist?
     */
    if (!$matches[1])
      return $this->set_last_executed($FILE);

    $updated_file_content = preg_replace($search, $replace, $file_content);
    file_put_contents($FILE, $updated_file_content);

    return true;
  }

  /**
   * @return void
   */
  public function set_last_executed($FILE)
  {
    $file_content = file_get_contents($FILE);
    $search = "/<\?php/";
    $replace = "<?php\n\n" . "// L::" . date("Y-m-d H:i:s") . "#";

    $updated_file_content = preg_replace($search, $replace, $file_content);
    file_put_contents($FILE, $updated_file_content);
  }
}
