<?php

namespace Bruder\Heiakim\APIGateway\User;

use Bruder\Gateway;
use Bruder\Http\Http;

class LogsGateway extends Gateway
{

  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  /**
   * @return object
   */
  public function get()
  {
    $authorization_in_request = Http::authorization("Bearer", $_SERVER);
    $tokens = include _root() . "/config/security/tokens.php";

    /**
     * Request authorized && valid?
     */
    if (!$authorization_in_request || $authorization_in_request !== $tokens["api"]["logs"])
      return $this->error("Unauthorized request");

    /**
     * Params valid?
     */
    if (!$this->params)
      return $this->error("Invalid set of arguments");

    /**
     * Switch through the model.
     */
    switch ($this->params->model) {
      case "log":
        return $this->one();
        break;

      case "logs":
        return $this->many();
        break;

      /**
         * Model invalid?
         */
      default:
        return $this->error("Invalid Model: " . $this->params->model);
        break;
    }
  }

  /**
   * @return object
   */
  public function one()
  {
    /**
     * ID specified?
     */
    if (!isset($this->params->id))
      return $this->error("No Second Model specified");

    /**
     * @var string|die
     */
    $return_data = match ($this->params->id) {
      "phperrors" => file_get_contents(_root() . "/storage/logs/php_errors.log"),
      "cron" => file_get_contents(_root() . "/storage/logs/cron.log"),
      default => die($this->error("Invalid Second Model: " . $this->params->id)),
    };

    return $this->success(data: $return_data);
  }

  /**
   * @return object
   */
  public function many()
  {
    $return_data = [
      "phperrors" => file_get_contents(_root() . "/storage/logs/php_errors.log"),
      "cron" => file_get_contents(_root() . "/storage/logs/cron.log"),
    ];

    return $this->success(data: $return_data);
  }
}
