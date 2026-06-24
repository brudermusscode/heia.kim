<?php

namespace Heiakim\Model\User;

use Heiakim\Application\Cookie;
use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Heiakim\Validate\Validate;

class UserSettings extends Justin
{

  protected $table = "user_settings";

  protected $primaryKey = "user_id";

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
   * @return static
   */
  public function edit(object $params)
  {

    # ? Profile Picture
    # Upload: Handled through Profiles.

    # Remove profile picture.
    if (isset($params->remove_current_profile_picture))
      return $this->remove_current_profile_picture();

    # ? Check Notifications
    if (isset($params->checked_notifications_at))
      $this->checked_notifications_at = date("Y-m-d H:i:s", time());

    # ? Birthday
    if (
      !empty($params->day)
      && !empty($params->month)
      && !empty($params->year)

      # Users can only change their birthday a specific amount of times.
      && !$this->birthday
      && ($Date = Validate::birthday($params->day, $params->month, $params->year))
    )
      $this->birthday = $Date->format("Y-m-d");

    # ? Privacy: Mailings
    foreach (UserSettingsPrivacy::$mailings as $mailing) {
      if (isset($params->$mailing)) {
        $mailing_value = $this->ensure_numeric_bool($params->$mailing);

        $this->user->privacy->$mailing = $mailing_value !== null
          ? $mailing_value
          : $this->user->privacy->$mailing;
      }
    }

    # ? Privacy: Public Profile
    if (isset($params->is_public))
      $this->user->privacy->is_public = $params->is_public > 0 ? 1 : 0;

    # ? Privacy: Image History
    if (isset($params->image_history))
      $this->user->privacy->image_history = $params->image_history > 0 ? 1 : 0;

    # ? Privacy: Policies
    if (isset($params->accepts_policies)) {
      $this->user->privacy->accepts_policies = $params->accepts_policies > 0 ? 1 : 0;

      Cookie::delete("POLICIES_CONSENT_STEP");
      Cookie::set("POLICIES_CONSENT", true, "+10 years");
    }

    $this->db_transaction();

    try {

      # Save and commit!
      $this->save();
      $this->user->privacy->save();
      $this->db_commit();

      return $this;
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      die(error());
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
   * @return ?int
   */
  public function ensure_numeric_bool(mixed $input)
  {

    $filtered_input = filter_var($input, FILTER_VALIDATE_INT);

    if ($filtered_input !== null && ($filtered_input == 0 || $filtered_input == 1))
      return intval($input);
    else
      return null;
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

      return success();
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
      return error("<strong>You have no profile picture set!</strong> You may want to upload one, so you can delete it.");

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
