<?php

namespace Bruder\Heiakim\Model\User;

use Bruder\Justin;
use Bruder\Application\Logger;
use Bruder\Heiakim\Model\User;
use Bruder\Utils\Utils;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\EncodedImageInterface;
use DateTime;

class UserSettings extends Justin
{

  /**
   * @var string
   */
  protected $table = "user_settings";

  /**
   * @var string
   */
  protected $primaryKey = "user_id";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "is_legit",
    "birthday",
    "checked_notifications_at",
    "name_changes_left",
    "birthday_changes_left",
    "account_wipes_left",
    "account_wiped_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected $attributes = [
    "id" => 0,
    "user_id" => 0,
    "is_legit" => 0,
    "birthday" => 0,
    "checked_notifications_at" => null,
    "name_changes_left" => 0,
    "birthday_changes_left" => 0,
    "account_wipes_left" => 0,
    "account_wiped_at" => null,
    "updated_at" => null,
  ];

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    /**
     * ? PICTURE
     */
    if (isset($params->image))
      return $this->upload_profile_picture($params);

    /**
     * ? REMOVE PICTURE
     */
    if (isset($params->remove_current_profile_picture))
      return $this->remove_current_profile_picture();

    /**
     * ? NOTIFICATIONS
     */
    if (isset($params->checked_notifications_at))
      $this->checked_notifications_at = date("Y-m-d H:i:s", time());

    /**
     * ? BIRTHDAY
     */
    if (isset($params->day, $params->month, $params->year) && !$this->birthday) {
      $day = str_pad($params->day, 2, '0', STR_PAD_LEFT);
      $month = str_pad($params->month, 2, '0', STR_PAD_LEFT);
      $date_string = "{$params->year}-{$month}-{$day}";

      /**
       * Validate date pattern.
       */
      $date_pattern = "/^\d{4}-\d{2}-\d{2}$/";
      if (!preg_match($date_pattern, $date_string))
        return $this->error("<strong>Your birthday is of invalid format!</strong>");

      /**
       * Create DateTime object.
       */
      $datetime = DateTime::createFromFormat("Y-m-d", $date_string);

      /**
       * Validate that date is in range of possible.
       */
      $min_date = new DateTime("1960-01-01");
      $max_date = new DateTime("2022-01-01");

      if ($datetime < $min_date)
        return $this->error("<strong>You are too old for osu!</strong> Spent the rest of your precious life with something else.");

      if ($datetime > $max_date)
        return $this->error("<strong>You are too young for osu!</strong> Go touch some grass.");

      unset($params->day, $params->month, $params->year);

      /**
       * Set the new birthday!
       */
      $this->birthday = $datetime->format("Y-m-d");
    }

    /**
     * Begin transaction.
     */
    $this->db_transaction();
    try {

      /**
       * Save & commit!
       */
      $this->save();
      $this->db_commit();

      return request_success("<strong>Your account has been updated!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return request_error();
    }
  }

  /**
   * @param object $params
   * @return object
   */
  public function upload_profile_picture(object $params)
  {

    /**
     * Catch any upload error in advance.
     */
    if (isset($params->files["error"]) && $params->files["error"] > 0)
      die($this->error(\Bruder\File\Upload::error($params->files["error"])));

    /**
     * Temporary image is not available?
     */
    if (!isset($params->files["tmp_name"]) || !$params->files["tmp_name"])
      die($this->error("<strong>No image has been added.</strong>"));

    /**
     * Check if file exists.
     */
    if (!file_exists($params->files["tmp_name"]))
      die("<strong>Temporary image file doesn't exist.</strong>");

    /**
     * @var User
     */
    $User = $this->user;

    /**
     * @var string
     */
    $token = Utils::random_alpha_token(18);

    /**
     * Begin processing.
     */
    try {

      /**
       * @var ImageInterface
       */
      $handle = ImageManager::gd()
        ->read($params->files["tmp_name"]);

      /**
       * @var EncodedImageInterface
       */
      $encoded_image = $handle->encode();
      $file_type = $encoded_image->mediaType();
      $file_name = "$User->id.jpg";
      $file_name_history = "ss-$token.webp";

      /**
       * GIF only for Premium+ members.
       */
      if (!$User->is_premium() && str_contains(strtolower($file_type), "gif"))
        die($this->error("<strong>GIFs can only be uploaded as a " . PREMIUM_NAME . " member.</strong> " . "!PREMIUM_UNLOCK_NOW"));

      /**
       * Save basic avatar as jpeg.
       */
      // TODO: Find out if osu! client can handle webp images.
      $handle
        ->scale(180)
        ->toJpeg()
        ->save(ENV->AVATAR_DIR . "/$file_name");

      /**
       * If the user has enabled their images uploaded to save
       * into a history, do that in the following.
       */
      if ($this->user->privacy->image_history) {

        /**
         * @var ImageInterface
         */
        $handle = ImageManager::gd()
          ->read($params->files["tmp_name"]);

        /**
         * Save image in history as webp.
         */
        $handle
          ->toWebp()
          ->save(ENV->AVATAR_HISTORY_DIR . "/$file_name_history");

        /**
         * Save image thumb in history as webp.
         */
        $handle
          ->scale(350)
          ->toWebp()
          ->save(ENV->AVATAR_HISTORY_DIR . "/thumbs/$file_name_history");

        /**
         * Create new image.
         */
        $User->images()
          ->create([
            "type" => "__user__/image",
            "url" => $file_name_history,
          ]);
      }

      return $this->success(
        "<strong>Profile image updated!</strong> It can take some time for the image to show up. Pressing &nbsp;
      <span tag text smol bold>CTRL + F5</span> &nbsp; refreshes the cache of your browser. This way your image
      will be updated immediately."
      );
    } catch (\Exception $e) {

      Logger::to_file($e);

      die($this->error());
    }
  }

  /**
   * @return object Default return object.
   */
  public function remove_current_profile_picture()
  {

    /**
     * @var string[]
     */
    $all_files = glob(ENV->AVATAR_DIR . "/" . $this->user->id . '.*');

    /**
     * No files there?
     */
    if (count($all_files) < 1)
      return $this->error("<strong>You have no profile picture set!</strong> You may want to upload one, so you can delete it.");

    /**
     * Remove all files from game server's avatar directory
     */
    foreach ($all_files as $file)
      if (is_file($file))
        unlink($file);

    return $this->success(
      "<strong>It's gone!</strong> Hope your friends will recognize you still!",
      data: ["image" => ENV->AVATAR_URL . "/default"],
    );
  }
}
