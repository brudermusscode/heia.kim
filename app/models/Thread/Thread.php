<?php

namespace Bruder\Heiakim\Model\Thread;

use Bruder\Justin;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Squad;

class Thread extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "clan_id",
    "user_id",
    "title",
    "closed",
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
     * @var Squad
     */
    $Squad =  $CurrentUser->squad;

    /**
     * Title is not empty?
     */
    if (!trim($params->title))
      return $this->error("<strong>Please set a title!</strong>");

    /**
     * @var string
     */
    $closed =
      isset($params->closed)
      && $params->closed
      && $CurrentUser->sqcan("manage", "content")
      ? 1 : 0;

    /**
     * @var Thread
     */
    $Thread = $Squad->threads()
      ->create([
        "user_id" => $CurrentUser->id,
        "title" => $params->title,
        "closed" => $closed,
      ])->fresh();

    /**
     * Prepare the new parameters for the post.
     */
    $post_params = (object) [
      "CurrentUser" => $CurrentUser,
      "squad" => $Squad,
      "thread" => $Thread,
      "content" => $params->content,
      "attachments" => $params->attachments ?? null,
    ];

    /**
     * Create the ThreadPost.
     */
    $ThreadPost = (new ThreadPost)->new($post_params);
    if (!$ThreadPost->status) {
      $Thread->delete();

      return $ThreadPost;
    }

    /**
     * If there was an error, by adding attachments or something
     * e. g., return the message from adding the post.
     */
    if ($ThreadPost->has_error)
      $this->return->has_error = true;

    /**
     * Append some id's for redirecting the user to the new thread.
     */
    $this->return->thread_id = $Thread->id;
    $this->return->squad_id  = $Squad->id;

    return $this->success($ThreadPost->message);
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
   * @return Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "clan_id", "id");
  }

  /**
   * @return ThreadPost
   */
  public function posts()
  {
    return $this->hasMany(ThreadPost::class);
  }
}
