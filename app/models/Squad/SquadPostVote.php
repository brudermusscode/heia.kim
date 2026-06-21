<?php

namespace Heiakim\Model\Squad;

use Heiakim\Justin;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquadPostVote extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "post_id",
    "type",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @return BelongsTo<SquadPost>
   */
  public function post()
  {
    return $this->belongsTo(SquadPost::class, "post_id", "id");
  }

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return Squad
   */
  public function squad()
  {
    return $this->post->squad;
  }
}
