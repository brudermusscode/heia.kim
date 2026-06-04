<?php

namespace Heiakim\Trait;

trait IsApiProvider
{

  /**
   * @var string
   */
  protected $table = "api_providers";

  /**
   * @var array
   */
  protected $fillable = [
    "provider",
    "access_token",
    "refresh_token",
    "expires_at",
    "deleted_at",
    "updated_at",
  ];
}
