<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/init.php";

use Heiakim\APIGateway\Count\BeatmapsGateway;
use Heiakim\Http\Request;
use Heiakim\APIGateway\User\UsersGateway;
use Heiakim\APIGateway\Score\ScoresGateway;
use Heiakim\APIGateway\Count\CountsGateway;
use Heiakim\APIGateway\User\LogsGateway;

/**
 * APIs are JSON!
 */
header(JSON_RESPONSE);

/**
 * @var Request
 */
$Request = new Request;

/**
 * @var string
 */
$model  = filter_input(INPUT_GET, "model", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Model specified?
 * ! Error
 */
if (!$model)
  exit($Request->error("No model specified."));

/**
 * @var object
 */
$Data = match ($model) {
  /**
   * ? Users
   */
  "users",
  "user" => (new UsersGateway(params: $_GET))->get(),

  /**
   * ? Scores
   */
  "scores",
  "score" => (new ScoresGateway(params: $_GET))->get(),

  /**
   * ? Beatmaps
   */
  "beatmaps",
  "beatmap" => (new BeatmapsGateway(params: $_GET))->get(),

  /**
   * ? Just counts
   */
  "counts" => (new CountsGateway(params: $_GET))->get(),

  /**
   * ? Log files
   */
  "log",
  "logs" => (new LogsGateway(params: $_GET))->get(),

  /**
   * Unknown model.
   * ! Error
   */
  default => $Request->error("Invalid model: $model."),
};

/**
 * * Success
 */
echo json_encode($Data);
