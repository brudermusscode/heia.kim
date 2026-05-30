<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Heiakim\Trait\HasDefaultUser;
use Bruder\Heiakim\Trait\DeletableBy;

class Image extends Justin
{
  use HasDefaultUser;
  use DeletableBy;

  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "reference_id",
    "url",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $types = [
    "__user__/image",
    "__user__/headline",
    "__squad__/logo",
    "__squad__/headline",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    $this->delete();

    return request_success("<strong>Deleted!</strong>");
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "reference_id", "id");
  }

  /**
   * @return User|Squad|null
   */
  public function reference()
  {
    $Reference = match ($this->type) {
      "__squad__/logo",
      "__squad__/headline" => Squad::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }

  /**
   * @return string
   */
  public function type()
  {
    return match (explode("/", $this->type)[0]) {
      "__user__" => "user",
      "__squad__" => "squad",
      default => "__user__",
    };
  }
}
