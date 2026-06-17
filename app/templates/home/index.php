<?php

use Heiakim\Model\Gamemode;

if (LOGGED) {
  include __DIR__ . "/_page-navigator.php";

  # Router params.
  $sub = aglobal("sub");

  # Get the favorite mode of CurrentUser.
  $favorite_modes = CurrentUser->favorite_modes();
  $favorite_mode = Gamemode::gumode_text($favorite_modes[0]->mode ?? null);

?>

  <page-structure feed fl fldircol gap=mid>
    <div fl alic jucc gap=smol style="height:124px;" z>
      <a href="/home" sub>
        <mbutton mid outlined has-icon=left <?php display_active($sub, null); ?>>
          <mi>stream</mi>
          <?= __("What's new?") ?>
        </mbutton>
      </a>

      <a href="/home/beatmaps" sub>
        <mbutton mid outlined <?php display_active($sub, "beatmaps"); ?>>
          Your Beatmaps
        </mbutton>
      </a>

      <a href="/home/artists" sub>
        <mbutton mid outlined <?php display_active($sub, "artists"); ?>>
          <?= __("Starred") ?> <?= __("Artists") ?>
        </mbutton>
      </a>
    </div>

    <?php include __DIR__ . "/feed/_" . match ($sub) {
      "beatmaps" => "beatmaps",
      "artists" => "artists",
      default => "index",
    } . ".php"; ?>
  </page-structure>

  <?php include TEMPLATE . "/global/_scroll_end_logo.php"; ?>

<?php } else
  include __DIR__ . "/_landing.php";
