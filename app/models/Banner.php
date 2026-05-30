<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;

class Banner extends Justin
{
  // /**
  //  * @var string
  //  */
  // protected $table = "";

  /**
   * @var array
   */
  protected $fillable = [
    "image",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {
    return $this->success("<strong>Successfully created!</strong>");
  }
}
