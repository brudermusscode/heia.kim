<?php

namespace Bruder\Heiakim\APIGateway\Score;

use Bruder\Heiakim\APIGateway\Count\BeatmapsGateway;
use Bruder\Heiakim\APIGateway\User\UsersGateway;
use Bruder\Http\Request;
use Bruder\Gateway;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Score;

class ScoresGateway extends Gateway
{
  /**
   * @var array
   */
  public static $public_columns = [
    "id",
    "map_md5",
    "score",
    "pp",
    "acc",
    "max_combo",
    "mods",
    "n300",
    "n100",
    "n50",
    "nmiss",
    "ngeki",
    "nkatu",
    "grade",
    "status",
    "mode",
    "time_elapsed",
    "nmiss",
    "userid",
    "perfect",
  ];

  /**
   * @var ?Score
   */
  private $Score = null;

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
     * @var ?Score
     */
    $this->Score = Score::select(self::$public_columns);

    /**
     * Switch through the model.
     */
    switch ($this->params->model) {
      case "score":
        return $this->one();
        break;

      case "scores":
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
     * @var ?string
     */
    $with = filter_var($this->params->with ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $with = explode(",", $with);

    /**
     * @var ?User
     */
    $return_data = $this->Score
      ->when(in_array("user", $with), function ($q) {
        $q->with("user", function ($subq) {
          $subq->select(UsersGateway::$public_columns);
        });
      })
      ->when(in_array("beatmap", $with), function ($q) {
        $q->with("beatmap", function ($subq) {
          $subq->select(BeatmapsGateway::$public_columns);
        });
      })
      ->find($this->params->id);

    /**
     * Model data existent?
     */
    if (!$return_data)
      return $this->error("No Score found");

    return $this->success(data: $return_data);
  }

  /**
   * @return object
   */
  public function many()
  {
    header("X-Title: API: Scores");

    $mode    = filter_var($this->params->id ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $mod     = filter_var($this->params->model2 ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $section = filter_var($this->params->section ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $gumode  = Gamemode::convert_mode_mod_to_gumode_array($mode, $mod);

    /**
     * Get the extra information requested and make an array of it.
     */
    $with    = filter_var($this->params->with ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $with = explode(",", $with);

    /**
     * Valid order?
     */
    if (!in_array($this->order, self::$public_columns))
      $this->order = "id";

    /**
     * @var ?Score
     */
    $return_data =
      $this->Score
      ->when($gumode !== null, function ($q) use ($gumode) {
        if (is_array($gumode))
          $q->whereIn("mode", $gumode);
        else
          $q->where("mode", $gumode);
      })
      ->when(in_array("users", $with), function ($q) {
        $q->with("user", function ($subq) {
          $subq->select(UsersGateway::$public_columns);
        });
      })
      ->when(in_array("beatmaps", $with), function ($q) {
        $q->with("beatmap", function ($subq) {
          $subq->select(BeatmapsGateway::$public_columns);
        });
      })
      ->orderBy($this->order, $this->sort)
      ->limit($this->limit)
      ->offset($this->offset)
      ->get();

    /**
     * Any Scores exist?
     */
    if (!$return_data)
      return $this->error("No Scores found");

    return $this->success(data: $return_data);
  }
}
