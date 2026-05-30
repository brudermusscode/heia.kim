<?php

use Bruder\Application\Session\SessionManager;

$Session = new SessionManager;
$CurrentUser = $Session->get('user');

?>

<div class="user__headline_container__profile_image" mb="std">
  <div fl fldirrow justify-content="center">
    <div class="user__headline_container__profile_image__rank">
      <picture quadrat rounded="wide" size="wide" <?php if (isset($restricted) && $restricted) echo " shadowed=smol"; ?>>
        <img rounded="wide" src="<?= GDPR ? AVATAR . '/default' : AVATAR . "/$user_info->id"; ?>" />
      </picture>

      <div class="rank__icon">
        <p></p>
      </div>
    </div>
  </div>
</div>