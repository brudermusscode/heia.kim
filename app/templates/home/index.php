<?php

use Heiakim\Model\ApiOsu;
use Heiakim\Registry\ApiRegistry;

$Redis = new \Heiakim\Database\RedisManager()->connection();
$Connection = (object) [
  "provider_user_id" => 2675255,
  "is_legit" => 0,
];

foreach (new ApiOsu()->rulesets as $ruleset) {
  $user_ids = $Redis->sMembers(ApiRegistry::$redis_map["osu!"]["ranking"] . ":$ruleset");
  foreach ($user_ids as $user) {
    if ((int) $user === $Connection->provider_user_id) {
      $Connection->is_legit = 1;
      break;
    }
  }
}

if (LOGGED)
  include_once __DIR__ . "/_feed.php";
else
  include_once __DIR__ . "/_landing.php";
