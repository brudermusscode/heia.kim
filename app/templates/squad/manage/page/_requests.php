<?php

$tab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS) ?? "index";

if (!$SquadUser->can("coordinate", "users")) :
  include UNAVAILABLE;
else : ?>

  <content stdplus minlineauto fl fldircol gap>
    <div fl alic gap>
      <?php include dirname(__DIR__) . "/_back-button.php"; ?>
      <p text mid bold><?= __("Pending requests") ?></p>
    </div>

    <div fl justify-content=start gap=smol mb>
      <a href="<?= "$base_url/requests"; ?>">
        <mbutton mid filled=lighter <?php display_active($tab, "index") ?>>
          <p text><?= __("Requests") ?></p>
        </mbutton>
      </a>

      <a href="<?= "$base_url/requests?tab=invites"; ?>">
        <mbutton mid filled=lighter <?php display_active($tab, "invites") ?>>
          <p text><?= __("Invitations") ?></p>
        </mbutton>
      </a>
    </div>

    <div list fl fldircol gap=smol>
      <?php

      $file_path = __DIR__ . "/requests/_$tab.php";
      include file_exists($file_path) ? $file_path : __DIR__ . "/requests/_index.php";

      ?>
    </div>
  </content>

<?php endif;
