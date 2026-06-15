<?php

namespace Heiakim\APIGateway\User;

use Heiakim\Http\Request;
use Heiakim\Gateway;
use Heiakim\Model\Gamemode;
use Heiakim\Model\User;
use Heiakim\APIGateway\Score\ScoresGateway;

class UsersGateway extends Gateway
{
  /**
   * @var array
   */
  public static $public_columns = [
    "id",
    "name",
    "safe_name",
    "priv",
    "country",
    "silence_end",
    "donor_end",
    "latest_activity",
    "preferred_mode",
    "play_style",
    "created_at"
  ];

  /**
   * @var ?User
   */
  private $User = null;

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
     * @var ?User
     */
    $this->User = User::select(self::$public_columns)
      ->whereHas("privacy", function ($q) {
        $q->where([
          "accepts_policies" => 1
        ]);
      });

    /**
     * Switch through the model.
     */
    switch ($this->params->model) {
      case "user":
        return $this->one();
        break;

      case "users":
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
      return $this->error("No ID Specified");

    /**
     * @var ?array
     */
    $with = isset($this->params->with) ? explode(",", $this->params->with) : [];

    /**
     * @var ?User
     */
    $return_data
      = $this->User
      = $this->User

      /**
       * With: Stats
       */
      ->when(in_array("stats", $with), function ($q) {
        $q->with("stats");
      })

      /**
       * With: Scores
       */
      ->when(in_array("scores", $with), function ($q) {
        $q->with("scores", function ($subq) {
          $subq->orderBy("pp", "DESC");
        });
      })

      /**
       * With: Premium
       */
      ->when(in_array("premium", $with), function ($q) {
        $q->with("premium");
      })
      ->find($this->params->id);

    /**
     * Model data existent?
     */
    if (!$return_data)
      return $this->error("No User found");

    /**
     * With: Rankings & Development
     */
    if (in_array("ranking", $with))
      $return_data->ranking = $this->User->get_all_rankings();

    /**
     * With: Top Score
     */
    if (in_array("topscores", $with))
      $return_data->top_scores = $this->User->top_scores_of_all_gumodes(in_api: true);

    /**
     * Model 2 specified?
     */
    if (empty($this->params->model2)) {
      header("X-Title: API: User");
      return $this->success(data: $return_data);
    }

    /**
     * @var User
     */
    $this->return->relation
      = $this->relation
      = $return_data;

    /**
     * Switch therough the seconf requested model.
     */
    switch ($this->params->model2) {
      case "scores":
        header("X-Title: API: User, Scores");
        return $this->scores();

      case "premium":
        header("X-Title: API: User, Premium");
        return $this->premium();
        break;
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
    header("X-Title: API: Users");

    /**
     * Valid order?
     */
    if (!in_array($this->order, self::$public_columns))
      $this->order = "id";

    /**
     * @var ?string
     */
    $query_name = $this->params->name ?? null;

    /**
     * @var ?array
     */
    $query_with = isset($this->params->with) ? explode(",", $this->params->with) : [];

    /**
     * @var ?User
     */
    $data = $this->User
      ->orderBy($this->order, $this->sort)
      /**
       * Name
       */
      ->when($query_name, function ($q) use ($query_name) {
        $q->where("name", $query_name);
      })
      ->limit($this->limit)
      ->offset($this->offset);

    /**
     * Either get one or many results.
     */
    $return_data = $query_name
      ? $data
      /**
       * With: stats
       */
      ->when(in_array("stats", $query_with), function ($q) {
        $q->with("stats");
      })
      ->first()
      : $data->get();

    /**
     * Any Users exist?
     */
    if (!$return_data)
      return $this->error("No Users found");

    return $this->success(data: $return_data);
  }

  /**
   * @return object
   */
  public function scores()
  {
    $mode    = filter_var($this->params->id2 ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $mod     = filter_var($this->params->id3 ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $section = filter_var($this->params->section ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
    $gumode  = Gamemode::find_gumode($mode, $mod, array: true);

    /**
     * Valid order?
     */
    if (!in_array($this->order, ScoresGateway::$public_columns))
      $this->order = "id";

    /**
     * @var ?Score
     */
    $return_data = match ($section) {
      default => $this->relation
        ->scores()
        ->select(ScoresGateway::$public_columns)
        ->when($gumode !== null, function ($q) use ($gumode) {
          if (is_array($gumode))
            $q->whereIn("mode", $gumode);
          else
            $q->where("mode", $gumode);
        })
        ->limit($this->limit)
        ->offset($this->offset)
        ->orderBy($this->order, $this->sort)
        ->get(),
      "firsts" => $this->relation->first_place_scores(
        gumode: $gumode,
        limit: $this->limit,
        order: $this->order,
        sort: $this->sort,
        offset: $this->offset,
        from_api: true
      ),
    };

    /**
     * Any Scores exist?
     */
    if (!$return_data)
      return $this->error("No Scores found");

    return $this->success(data: $return_data);
  }

  /**
   * @return object
   */
  public function premium()
  {

    /**
     * @var array
     */
    $public_columns = [
      "headline",
      "premium_name_style",
      "created_at"
    ];

    /**
     * @var ?object
     */
    $return_data =
      $this->relation
      ->premium()
      ->select($public_columns)
      ->first();

    /**
     * User has already purchased Premium+?
     */
    if (!$return_data)
      return $this->error("This User has not yet purchased Premium+");

    return $this->success(data: $return_data);
  }
}
