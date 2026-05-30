<div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?>>
  <div class=notif__element_inr>
    <picture circled class=notif__element_picture>
      <img src="<?= EMOJI . "/doge2.png"; ?>" />
      <div class=type_badge type=system>
        <i class="mi" size=smol><?= $type_icon; ?></i>
      </div>
    </picture>

    <div class="notif__element_content">
      <p text std><?= $Notification->message; ?></p>
      <p text smol slight><?= $timestamp; ?></p>
    </div>
  </div>
</div>