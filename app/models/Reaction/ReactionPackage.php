<?php

namespace Heiakim\Model\Reaction;

use Heiakim\Justin;

class ReactionPackage extends Justin
{
  /**
   * @var string
   */
  protected $table = "reaction_packages";

  /**
   * @var array
   */
  protected $fillable = [
    "package_name",
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
   * @return ReactionPackageEmoji
   */
  public function emojis()
  {
    return $this->hasMany(ReactionPackageEmoji::class);
  }
}
