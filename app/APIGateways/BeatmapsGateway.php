<?php

namespace Heiakim\APIGateway\Count;

use Heiakim\Gateway;
use Heiakim\Http\Request;
use Heiakim\APIGateway\Score\ScoresGateway;
use Heiakim\APIGateway\User\UsersGateway;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Score;
use Heiakim\Model\User;

class BeatmapsGateway extends Gateway
{
  /**
   * @var array
   */
  public static $public_columns = [
    "id",
    "md5",
    "set_id",
    "status",
    "mode",
    "artist",
    "title",
    "version",
    "creator",
    "total_length",
    "max_combo",
    "frozen",
    "passes",
    "bpm",
    "cs",
    "ar",
    "od",
    "hp",
    "diff",
    "last_update",
  ];

  /**
   * @var ?Beatmap
   */
  private $Beatmap = null;

  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  /**
   * @return object
   */
  public function get()
  {
    /**
     * Params valid?
     */
    if (!$this->params)
      return $this->error("Invalid set of arguments");

    /**
     * @var ?Beatmap
     */
    $this->Beatmap = Beatmap::select(self::$public_columns);

    /**
     * Switch through the model.
     */
    switch ($this->params->model) {
      case "beatmap":
        return $this->one();
        break;

      case "beatmaps":
        return $this->many();
        break;

      /**
       * Model valid?
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
      return $this->error("No ID sepcified");

    /**
     * @var ?Beatmap
     */
    $return_data = $this->Beatmap->find($this->params->id);

    /**
     * Model data existent?
     */
    if (!$return_data)
      return $this->error("No Beatmap found");

    /**
     * Model 2 specified?
     */
    if (empty($this->params->model2)) {
      header("X-Title: API: Beatmap");
      return $this->success(data: $return_data);
    }

    /**
     * @var Beatmap
     */
    $this->return->relation
      = $this->relation
      = $return_data;

    /**
     * Switch therough the seconf requested model.
     */
    switch ($this->params->model2) {
      case "scores":
        header("X-Title: API: Beatmap, Scores");
        return $this->scores();

      default:
        $return_data = null;
        return $this->error("Invalid Model: " . $this->params->model2);
    }
  }

  /**
   * @return object
   */
  public function many()
  {
    header("X-Title: API: Beatmaps");

    /**
     * Valid order?
     */
    if (!in_array($this->order, self::$public_columns))
      $this->order = "id";

    /**
     * @var ?User
     */
    $return_data =
      $this->Beatmap
      ->orderBy($this->order, $this->sort)
      ->limit($this->limit)
      ->offset($this->offset)
      ->get();

    /**
     * Any Users exist?
     */
    if (!$return_data)
      $this->error("No Beatmaps found");

    return $this->success(data: $return_data);
  }



  /**
   * @return object
   */
  public function scores()
  {
    $mode    = filter_var($this->params->id2 ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $mod     = filter_var($this->params->id3 ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $gumode  = Gamemode::convert_mode_mod_to_gumode_array($mode, $mod);
    $with    = filter_var($this->params->with ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

    /**
     * Valid order?
     */
    if (!in_array($this->order, ScoresGateway::$public_columns))
      $this->order = "id";

    /**
     * @var ?Score
     */
    $return_data = $this->relation
      ->scores()
      ->select(ScoresGateway::$public_columns)
      ->when($gumode !== null, function ($q) use ($gumode) {
        if (is_array($gumode))
          $q->whereIn("mode", $gumode);
        else
          $q->where("mode", $gumode);
      })
      ->when($with === "users", function ($q) {
        $q->with("user", function ($subq) {
          $subq->select(UsersGateway::$public_columns);
        });
      })
      // ->whereIn("scores.status", [2])
      ->limit($this->limit)
      ->offset($this->offset)
      ->orderBy($this->order, $this->sort)
      ->get();

    /**
     * Any Scores exist?
     */
    if (!$return_data)
      return $this->error("No Scores found");

    return $this->success(data: $return_data);
  }
}
