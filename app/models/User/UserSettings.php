<?php

namespace Heiakim\Model\User;

use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\EncodedImageInterface;
use DateTime;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageManagerInterface;

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
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    # ? Profile Picture
    if (!empty($params->files["image"]["tmp_name"]))
      return $this->upload_profile_picture($params->files["image"]);

    # Remove profile picture.
    if (isset($params->remove_current_profile_picture))
      return $this->remove_current_profile_picture();

    # ? Check Notifications
    if (isset($params->checked_notifications_at))
      $this->checked_notifications_at = date("Y-m-d H:i:s", time());

    # ? Birthday
    if (isset($params->day, $params->month, $params->year) && !$this->birthday) {
      $day = str_pad($params->day, 2, '0', STR_PAD_LEFT);
      $month = str_pad($params->month, 2, '0', STR_PAD_LEFT);
      $date_string = "{$params->year}-{$month}-{$day}";

      $date_pattern = "/^\d{4}-\d{2}-\d{2}$/";

      # Date pattern is valid?
      if (!preg_match($date_pattern, $date_string))
        return $this->error("<strong>Your birthday is of invalid format!</strong>");

      $datetime = DateTime::createFromFormat("Y-m-d", $date_string);
      $min_date = new DateTime("1960-01-01");
      $max_date = new DateTime("2022-01-01");

      # Too old for osu!?
      if ($datetime < $min_date)
        return $this->error("<strong>You are too old for osu!</strong> Spent the rest of your precious life with something else.");

      # Too young for osu!?
      if ($datetime > $max_date)
        return $this->error("<strong>You are too young for osu!</strong> Go touch some grass.");

      unset($params->day, $params->month, $params->year);

      $this->birthday = $datetime->format("Y-m-d");
    }

    # Begin a transaction! Nothing to leave behind.
    $this->db_transaction();

    try {

      # Save and commit!
      $this->save();
      $this->db_commit();

      return success("<strong>Your account has been updated!</strong>");
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return error();
    }
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @param array $files
   * @return object
   *
   * NOTE: Will die on error.
   */
  public function upload_profile_picture(array $files)
  {

    # Catch any file error and die immediately.
    \Heiakim\File\Upload::error($files);

    /**
     * @var User
     */
    $User = $this->user;

    $token = Utils::random_alpha_token(18);

    try {

      /**
       * @var ImageManagerInterface
       */
      $ImageManager = ImageManager::usingDriver(GdDriver::class);
      $Image = $ImageManager->decodePath($files["tmp_name"]);
      $ClonedImage = clone $Image;

      # Scale Image to 180px.
      $Image->scale(height: 180);

      # Ensure it's a JPEG.
      $EncodedImage = $Image->encodeUsingFormat(Format::JPEG, quality: 100);

      $file_type = $EncodedImage->mediaType();
      $file_name = $User->id . ".jpg";
      $file_name_history = "ss-$token.webp";

      # Let only Premium members upload gifs. Sorry my friends 🙂
      if (!$User->is_premium() && str_contains(strtolower($file_type), "gif"))
        die($this->error("<strong>GIFs can only be uploaded as a " . PREMIUM_NAME . " member.</strong> " . "!PREMIUM_UNLOCK_NOW"));

      # Save Image!
      $EncodedImage->save(ENV->AVATAR_DIR . "/$file_name");

      # If User has enabled Image History, save the Image and a Thumb to their histo-
      # ry.
      if ($this->user->privacy->image_history) {

        # Save original size.
        $ClonedImage->encodeUsingFormat(Format::WEBP, quality: 100);
        $ClonedImage->save(ENV->AVATAR_HISTORY_DIR . "/$file_name_history");

        # Save thumbnail.
        $ClonedImage->scale(height: 350);
        $ClonedImage->save(ENV->AVATAR_HISTORY_DIR . "/thumbs/$file_name_history");

        # Create a new Image for this User.
        $User->images()->create([
          "type" => "__user__/image",
          "url" => $file_name_history,
        ]);
      }

      return success(
        "<strong>Profile image updated!</strong> Press &nbsp; <span tag text smol bold>CTRL + F5</span> &nbsp; to show it immediately. Otherwise it might take some time."
      );
    } catch (\Throwable $e) {
      Logger::to_file($e);
      die(error());
    }
  }

  /**
   * @return string
   */
  public function remove_current_profile_picture()
  {

    $all_files = glob(ENV->AVATAR_DIR . "/" . $this->user->id . '.*');

    # User has no profile picture set?
    if (count($all_files) < 1)
      return $this->error("<strong>You have no profile picture set!</strong> You may want to upload one, so you can delete it.");

    # Unlink all files.
    foreach ($all_files as $file)
      if (is_file($file))
        unlink($file);

    return success(
      "<strong>It's gone!</strong> Hope your friends will recognize you still!",
      data: ["image" => ENV->AVATAR_URL . "/default"],
    );
  }
}
