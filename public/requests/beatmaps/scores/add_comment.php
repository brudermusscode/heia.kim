<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

// set all necessary requests for getting through this file
$request_list = [
  "comment_string",
  "score_id",
];

// ? not logged/verified, restricted
if (!VERIFIED) exit(0);

// ? query is empty
if ($Request->empti($request_list) || strlen(trim($_POST['comment_string'])) < 3) {
  $return->message = "Submitted empty comment";
  $M->logger($CurrentUser->id, 'Scores', $return->message);
  exit(json_encode($return));
}

// variablize
$score_id = htmlentities($_POST['score_id']);
$comment_string = htmlentities($_POST['comment_string']);

// START TRANSACTION
$pdo->beginTransaction();

// insert new comment (also checks for existence of score)
$comment = $Score->add_comment($score_id, $CurrentUser->id, $comment_string, true);

// ? score is not existing (might be due to profile wipe)
if (!$comment) {
  $return->message = "An error occured while inserting your comment. " . TRY_OR_STAFF;
  exit(json_encode($return));
}

// * alright man, prepare return object
$return->status = true;
$return->comment_id = (int) $comment->id;
$return->message = "Your comment has been submitted!";

exit(json_encode($return));
