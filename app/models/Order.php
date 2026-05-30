<?php

namespace Bruder\Heiakim\Model;

use Bruder\Http\Request;
use Bruder\Justin;
use Bruder\Heiakim\Model\Order\OrderPaypal;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Trait\HasDefaultUser;

class Order extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "reference_id",
    "order_id",
    "api_user_id",
    "order_status",
    "order_amount",
    "order_pieces",
    "currency",
    "deleted_at",
    "updated_at",
  ];

  /**
   * CREATE
   *
   * @param object $params
   * @return object
   */
  public function new(object $params) {}

  /**
   * UPDATE
   *
   * @param object $params
   * @return object
   */
  public function edit(object $params) {}

  /**
   * DELETE
   *
   * @param object $params
   * @return object
   */
  public function remove(object $params) {}

  /**
   * @return ?User
   */
  public function reference()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?OrderPaypal
   */
  public function paypal()
  {
    return $this->hasOne(OrderPaypal::class);
  }
}
