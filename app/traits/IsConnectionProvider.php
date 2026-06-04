<?php

namespace Heiakim\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Heiakim\Model\User;

trait IsConnectionProvider
{

  /**
   * @var string
   */
  protected $table = "connections";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "provider",
    "is_legit",
    "provider_user_id",
    "provider_user_email",
    "provider_user_nickname",
    "access_token",
    "refresh_token",
    "token_id",
    "scope",
    "expires_at",
    "deleted_at",
    "updated_at",
  ];

  /**
   * Traits do not inherit the Eloquent Model base class, so there will be squ-
   * iggles when using belongsTo() or any other function on $this. This function
   * lives to prevent this.
   *
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function model()
  {
    return $this;
  }

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->model()->belongsTo(User::class);
  }
}
