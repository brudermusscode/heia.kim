<?php

namespace Heiakim;

use Heiakim\Justin;
use Heiakim\Trait\Serialization;

class Gateway extends Justin
{
  use Serialization;

  /**
   * @var ?object
   */
  protected $params = null;

  /**
   * @var ?string
   */
  protected $model = null;

  /**
   * @var int
   */
  protected $page = 1;

  /**
   * @var ?string
   */
  protected $order = null;

  /**
   * @var ?string
   */
  protected $sort = null;

  /**
   * @var int
   */
  protected $limit = 60;

  /**
   * @var int
   */
  protected $offset = 0;

  /**
   * @var array
   */
  private static $valid_params = [
    "model",
    "id",
    "model2",
    "id2",
    "id3",
    "limit",
    "page",
    "section",
    "order",
    "sort",
    "with",
    "name",
    "token",
  ];

  /**
   * @var null|User|Score
   */
  protected $relation = null;

  public function __construct(array $params)
  {
    parent::__construct();

    $this->params = $this->serialize_request_params(["model"], $params, self::$valid_params);

    /**
     * Pagination
     */
    $this->limit =
      !empty($this->params->limit)
      && (int) $this->params->limit > 0
      && (int) $this->params->limit < $this->limit
      ? $this->params->limit
      : $this->limit;
    $this->page = !empty($this->params->page) && (int) $this->params->page > 0
      ? $this->params->page
      : 1;
    $this->offset = $this->page > 1 ? $this->limit * $this->page : 0;
    $this->order = $this->params->order ?? null;
    $this->sort = in_array($this->params->sort ?? "", ["desc", "asc"])
      ? strtoupper($this->params->sort)
      : "DESC";
  }
}
