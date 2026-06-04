<?php

namespace Heiakim\Database;

/**
 * Using the Laravel Eloquernt ORM for easy m8.
 */

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
  public function __construct(?string $connection = null)
  {

    $config = require ROOT . "/config/global.php";

    $capsule = new Capsule;
    $capsule->addConnection($config["database"][$connection ?? "default"]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
  }
}
