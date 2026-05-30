<?php

namespace Bruder\Heiakim\Model\Reaction;

use Bruder\Justin;

class ReactionPackageEmoji extends Justin
{
  /**
   * @var string
   */
  protected $table = "reaction_package_emojis";

  /**
   * @var array
   */
  protected $fillable = [
    "reaction",
    "emoji",
    "updated_at",
  ];

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ReactionPackage
   */
  public function reaction_package()
  {
    return $this->belongsTo(ReactionPackage::class);
  }
}
