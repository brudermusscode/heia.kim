<?php

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

$sub    = aglobal("sub") ?? "index";
$action = aglobal("action");

/**
 * @var Squad
 */
$Squad = CurrentUser->squad;

redirect_unauthorized($Squad);

$base_url = "/manage/squad";

$squad_headline = CurrentUser->squad->headline ?? "default.jpg";

/**
 * @var SquadUser
 */
$SquadUser = CurrentUser->squad_user;

include TEMPLATE . "/my/_header.php";

$path = TEMPLATE . "/squad/manage/page/_$sub.php";
$file_exists = file_exists($path);

?>

<content wide minlineauto fl fldircol gap=mid>
  <div filled=darker rounded=wide fl fldircol jucc alic tac pinline24
    style="height:164px;margin-top:-40px;">
    <p text bold wide>Squad-Manager</p>
    <p text std>Customize the uniqueness of your squad</p>
  </div>

  <?php include $file_exists ? $path : TEMPLATE . "/squad/manage/page/_index.php"; ?>
</content>

<?php include TEMPLATE . "/global/_scroll_end_logo.php";
