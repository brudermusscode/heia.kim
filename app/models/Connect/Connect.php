<?php

namespace Heiakim\Model\Connect;

use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Model\User;

class Connect extends Justin
{

  /**
   * @var string
   */
  protected $table = "connect_credentials";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "type",
    "is_legit",
    "vendor_id",
    "vendor_email",
    "access_token",
    "refresh_token",
    "token_type",
    "token_id",
    "scope",
    "deleted_at",
    "updated_at",
  ];

  protected static array $types = [
    "discord",
    "google",
    "osu",
  ];

  /**
   * @param object $params
   * @return string
   */
  public function auth(object $params)
  {

    /**
     * Service is invalid?
     */
    if (!in_array($params->type, static::$types))
      return request_error("<strong>This service is not yet supported!</strong> Maybe soon ~");

    $service_name = "Heiakim\\Model\\Vendor\\" . ucfirst($params->type);

    /**
     * Class doesn't exist?
     */
    if (!class_exists($service_name)) {
      Logger::to_file(new \Exception("Missing class: " . $service_name));
      return request_error("?????????");
    }

    return (new $service_name)->get_auth_uri($params);
  }

  /**
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {

    /**
     * Service is invalid?
     */
    if (!in_array($params->type, static::$types))
      return request_error("<strong>This service is not yet supported!</strong> Maybe soon ~", return_json_string: false);

    $service_name =  "Heiakim\\Model\\Connect\\" . ("Connect" . ucfirst($params->type));

    /**
     * Class doesn't exist?
     */
    if (!class_exists($service_name)) {
      Logger::to_file(new \Exception("Missing class: " . $service_name));
      return request_error("?????????", return_json_string: false);
    }

    return (new $service_name)->new($params);
  }

  /**
   * @return ?User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
