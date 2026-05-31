<?php

/**
 * @var bool
 */
$gdpr ??= true;

/**
 * @var int
 */
$user_image_id ??= $User->id ?? CurrentUser->id ?? CurrentUser->id ?? 0;

/**
 * @var string
 */
$user_profile_picture =
  !DEV ?
  (!LOGGED && $gdpr ? AVATAR . '/default' : AVATAR . "/$user_image_id")
  : AVATAR . "/0.jpg";

/**
 * Show a red overlay for the image from deleted users.
 */
if (isset($deleted)) { ?>
  <div background=unfollow style="opacity:.24;position:absolute;top:0;left:0;height:100%;width:100%;z-index:2;" circled>
  </div>
<?php

}

unset($deleted);

?>

<img style="vertical-align:middle;" src="<?= $user_profile_picture; ?>" loading=lazy />