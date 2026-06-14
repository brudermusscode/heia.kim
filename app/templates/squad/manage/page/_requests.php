<?php

/**
 * @var string
 */
$tab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS) ?? "index";

if (!$SquadUser->can("coordinate", "users"))
  include UNAVAILABLE;
else {

?>

  <div content-width=smol>

    <div mt=wide class="setting__main_topping" fl align-items="center" gap=mid mb=std>
      <?php include TEMPLATE . "/my/_back_button.php"; ?>

      <div>
        <p bold text midler><?= __("Pending requests") ?></p>
      </div>
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

      /**
       * @var string
       */
      $file_path = __DIR__ . "/requests/_$tab.php";

      /**
       * Include the requested tab or fallback to the index.
       */
      include file_exists($file_path) ? $file_path : __DIR__ . "/requests/_index.php";

      ?>

    </div>

  </div>

<?php

}
