<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostPollAnswer;

class SquadPostPollAnswersController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "answer_key"],
      optional: [],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_interaction($Post);

    $questions = json_decode($Post->comment_string, true);
    $comment_string = $questions["comment_string"];

    # Just need the poll answer.
    unset($questions["comment_string"]);

    $this->params->answer_key = (int) $this->params->answer_key;

    # Check if the answer key is either higher than -1 or not higher than the count
    # of the questions substracting -1 (zero index).
    if (
      $this->params->answer_key < 0
      || $this->params->answer_key > (count($questions) - 1)
    )
      return error();

    /**
     * @var ?SquadPostPollAnswer
     */
    $PollAnswer = $Post
      ->poll_answers()
      ->where("user_id", CurrentUser->id)
      ->first();

    # Same answer has been given already?
    if ($PollAnswer && $PollAnswer->answer_key === $this->params->answer_key)
      return error();

    # Remove one count from the old answer count.
    if ($PollAnswer) {
      $iota = 0;
      foreach ($questions as $question => $count) {
        if ($iota === $PollAnswer->answer_key) {
          $questions[$question] = (int) $count - 1;
          break;
        }

        $iota++;
      }
    }

    # Add one count to the selected answer count.
    $iota = 0;
    foreach ($questions as $question => $count) {
      if ($iota === $this->params->answer_key) {
        $questions[$question] = (int) $count + 1;
        break;
      }

      $iota++;
    }

    $questions["comment_string"] = $comment_string;

    $PollAnswer?->delete();

    $Post->poll_answers()
      ->create([
        "user_id" => CurrentUser->id,
        "answer_key" => $this->params->answer_key,
        "updated_at" => null,
      ]);

    $Post->update([
      "comment_string" => json_encode($questions),
    ]);

    ob_start();
    $Post;
    include TEMPLATE . "/squad/post/_post.php";

    return success(data: [
      "Post" => $Post->withoutRelations(),
      "HTML" => ob_get_clean(),
    ]);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["answer_key"],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPostPollAnswer
     */
    $PollAnswer = SquadPostPollAnswer::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_interaction($PollAnswer->post);

    $questions = json_decode($PollAnswer->post->comment_string, true);

    # Remove one count to the selected answer count.
    $iota = 0;
    foreach ($questions as $question => $count) {
      if ($iota === $PollAnswer->answer_key) {
        $questions[$question] = (int) $count - 1;
        break;
      }

      $iota++;
    }

    $PollAnswer->delete();
    $PollAnswer->post->update([
      "comment_string" => json_encode($questions),
    ]);

    ob_start();
    $Post = $PollAnswer->post;
    include TEMPLATE . "/squad/post/_post.php";

    return success(data: [
      "Post" => $PollAnswer->post->withoutRelations(),
      "HTML" => ob_get_clean(),
    ]);
  }
}
