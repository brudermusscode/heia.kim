<div hover-menu menu-outer elevated="min">
  <div class="options_inr">
    <div has-tooltip="top">
      <div ripple-effect class="option" data-action="popup:open"
        data-href="/report/new?type=user&id=<?= $User->id; ?>">
        <div class="option_inr">
          <mi size="spec" color=red>campaign</mi>
        </div>
      </div>
      <div ttooltip>
        <p text std bold><?= __("Report") ?></p>
      </div>
    </div>

    <a href="<?= "$base_url/$mode/$mod/$type/" . $Country->abbreviation; ?>" has-tooltip="top">
      <div ripple-effect class="option">
        <div class="option_inr">
          <mi size="spec">captive_portal</mi>
        </div>
      </div>
      <div ttooltip>
        <p text std bold><?= __("Show country rankings") ?></p>
      </div>
    </a>
  </div>
</div>