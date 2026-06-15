<?php

namespace Heiakim\Model\Thread;

use Heiakim\Application\Application;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Justin;
use Heiakim\Http\Request;

class ThreadPost extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "thread_id",
    "user_id",
    "content",
    "deleted_at",
    "updated_at",
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
     * @var Thread
     */
    $Thread =  $params->thread;

    /**
     * @var bool
     */
    $this->return->has_error = false;

    /**
     * Content for post is empty?
     */
    if (!trim($params->content))
      return $this->error("<strong>Please put some content!</strong>");

    /**
     * Create a thread post.
     */
    $Post = $Thread->posts()->create([
      "user_id" => $CurrentUser->id,
      "content" => $params->content,
      "updated_at" => null,
    ]);

    /**
     * Touch the thread to show on top.
     */
    $Thread->touch();

    /**
     * @var string
     */
    $msg = "<strong>Created!</strong>";

    /**
     * Loop through all attachments, if there are any set and
     * create one for the new post.
     */
    if (isset($params->attachments) && $params->attachments) {
      /**
       * Arraylize the attachments parameter, if it's not an array
       * since we need an array to process it.
       */
      if (!is_array($params->attachments))
        $params->attachment = (array) $params->attachment;

      /**
       * @var int
       */
      $count = 0;
      $failed = 0;

      foreach ($params->attachments as $attachment) {
        /**
         * Increase the count.
         */
        $count++;

        /**
         * Create the type and continue if it is invalid.
         */
        $attachment_array = explode("/", $attachment);
        if (!count($attachment_array) == 2 || !is_numeric($attachment_array[1])) {
          $failed++;
          continue;
        }

        /**
         * Create the params object for creating a new attachment.
         */
        $attachment_params = (object) [
          "thread_post" => $Post,
          "type" => $attachment_array[0],
          "reference_id" => count($attachment_array) > 1 ? (int) $attachment_array[1] : 0,
        ];

        /**
         * Create the attachment!
         */
        $create_attachment = (new ThreadPostAttachment)->new($attachment_params);

        /**
         * Increase the failed count if the previous attachment failed.
         */
        if (!$create_attachment->status)
          // $failed++;
          return $create_attachment;
      }

      /**
       * Append a string to the return message, that a specific
       * amount of attachments could not be added, if any failed.
       */
      if ($failed) {
        $error = true;
        $plural_singular = $failed > 1 ? "attachments" : "attachment";
        $msg .= " $failed $plural_singular failed to be added.";
      }
    }

    /**
     * Get fresh data for the Post.
     */
    $Post = $Post->fresh();

    /**
     * @var bool
     */
    $this->return->has_error = $error ?? false;

    ob_start();
    $is_new = true;
    $CurrentUser;
    include ROOT . "/app/templates/threads/_post.php";
    $this->return->data = ob_get_clean();

    return $this->success($msg);
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
  public function edit() {}

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return Thread
   */
  public function thread()
  {
    return $this->belongsTo(Thread::class);
  }

  /**
   * @return ThreadPostAttachments
   */
  public function attachments()
  {
    return $this->hasMany(ThreadPostAttachment::class);
  }
}
