<?php

namespace Heiakim\Model\Restriction;

use Heiakim\Application\Application;
use Heiakim\Model\User;
use Heiakim\Justin;
use Heiakim\Utils\Utils;
use Verot\Upload\Upload;

class RestrictionAppeal extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "restriction_id",
    "content",
    "live_play_file",
    "is_appeal",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected $youtube_url_formats = [
    /** youtube.com */
    "https://youtube.com/",
    "https://www.youtube.com/",
    "https://m.youtube.com/",
    /** youtu.be */
    "https://www.youtu.be/",
    "https://youtu.be/",
    /** youtube-nocookie.com */
    "https://youtube-nocookie.com/",
    "https://www.youtube-nocookie.com/",
    "https://m.youtube-nocookie.com/",
  ];

  protected $content_length = [
    20,
    6000
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {
    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var ?Restriction
     */
    $Restriction = $params->restriction;

    /**
     * If user is not yet restricted, they need to
     * send a live play because of a freeze. If there is no live
     * play attached, return an error.
     */
    if (!$Restriction && !isset($params->live_play_file))
      return $this->error();

    /**
     * If a live play file is send with the params, check for it
     * to be a youtube link.
     */
    if (isset($params->live_play_file) && strlen(trim($params->live_play_file)) > 0)
      if (!$this->live_play_url_is_youtube($params->live_play_file))
        return $this->error("<strong>Your link doesn't seem to link to YouTube.</strong>");

    /**
     * Content can only be submitted if already restricted.
     */
    // TODO: Think about implementing max length to appeal.
    if (
      isset($params->content)
      && strlen(preg_replace("/\s+/", "", $params->content)) < $this->content_length[0]
    )
      return $this->error("<strong>Your appeal should atleast be " . $this->content_length[0] . " characters long.</strong> Put some effort, it's your account!");

    /**
     * @var RestrictionAppeal
     */
    $Appeal = $CurrentUser->appeals()
      ->make([
        "restriction_id" => $Restriction?->id,
        "content" => $params->content ?? null,
        "live_play_file" => $params->live_play_file,
        "is_appeal" => $Restriction && !$CurrentUser->appeal_locked() ? 1 : null,
        "updated_at" => null,
      ]);

    /**
     * Save it!
     */
    $Appeal->save();

    /**
     * Delete all appeals with REDO_REQUESTED status.
     */
    foreach (
      $Appeal->user
        ->appeals()
        ->where("status", "REDO_REQUESTED")
        ->get() as $Appeal
    )
      $Appeal->delete();

    return $this->success("<strong>Appeal submitted!</strong> We will soon reach out to you.");
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params) {}

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {
    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var self
     */
    $Appeal = $params->appeal;

    /**
     * Live play is pointing to YouTube?
     */
    if (!$this->live_play_url_is_youtube($params->live_play_file))
      return $this->error("<strong>Your link doesn't seem to link to YouTube.</strong>");

    /**
     * Update it!
     */
    $Appeal->update([
      "live_play_file" => $params->live_play_file,
    ]);

    return $this->success("<strong>Appeal updated!</strong>");
  }

  /**
   * @param string $url
   * @return bool
   */
  public function live_play_url_is_youtube(string $url)
  {
    $live_play_url_is_youtube = false;
    foreach ($this->youtube_url_formats as $valid_youtube_url)
      if (strpos($url, $valid_youtube_url) === 0)
        $live_play_url_is_youtube = true;

    return $live_play_url_is_youtube;
  }

  /**
   * @return object
   */
  public function upload_live_play($file)
  {
    /**
     * No file set?
     */
    if (!$file)
      return $this->success("");

    /**
     * @var Upload
     */
    $video = new Upload($file, "heiakim-english");

    /**
     * Upload failed?
     */
    if (!$video->uploaded) {
      $video->clean();
      return $this->error($video->error);
    }

    /**
     * Begin image processing
     */
    $video_name = Utils::random_alpha_token(62);
    $video->allowed = ["video/*"];
    $video->file_max_size = "1G";
    $video->file_auto_rename = false;
    $video->file_new_name_body = $video_name;

    /**
     * Process it!
     */
    $video->process(_root() . "/public/data/users/liveplay-videos");

    /**
     * Processing failed?
     */
    if (!$video->processed) {
      $video->clean();
      return $this->error($video->error);
    }

    /**
     * Set the file name.
     */
    $this->live_play_file = "$video_name.$video->file_dst_name_ext";

    return $this->success("Live play uploaded!");
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?Restriction
   */
  public function restriction()
  {
    return $this->belongsTo(Restriction::class);
  }
}
