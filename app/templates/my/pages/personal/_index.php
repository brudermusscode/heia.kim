<box-model outlined=darker p24 fl fldircol gap=smol>
  <div p12 fl fldircol gap=smol>
    <p text midler bold><?= __("General") ?></p>
    <p text std><?= __("These can be visible to other players and might be visible in search results.") ?></p>
  </div>

  <a href="/editor">
    <div hoverable p12 style="padding-right:32px;" rounded=mid>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap align-items=center>
          <picture size=midler circled>
            <?php CurrentUser->image(); ?>
          </picture>
          <div posrel>
            <p text bold><?= __("Picture") ?></p>
            <p text>Use the new Profile Editor</p>
          </div>
        </div>
        <div posrel>
          <mi midler><?= EDITOR_ICON; ?></mi>
          <div notification-dot style="top:-4px;right:-4px;"></div>
        </div>
      </div>
    </div>
  </a>

  <div style="height:1px;" minline12 background=slight></div>

  <div open="personal:name" hoverable p12 style="padding-right:32px;" rounded=mid>
    <div fl gap align-items=center justify-content=space-between>
      <div fl gap align-items=center>
        <mi wide style="min-width:52px;">format_size</mi>
        <div>
          <p text std bold color=company><?= CurrentUser->name; ?></p>
          <p text std><?= __("Public username") ?></p>
        </div>
      </div>
      <mi midler>east</mi>
    </div>
  </div>

  <div style="height:1px;" minline12 background=slight></div>

  <div open="personal:birthday" hoverable p12 style="padding-right:32px;" rounded=mid fl gap alic jucsb>
    <div fl gap alic>
      <mi wide style="min-width:52px;">celebration</mi>
      <div>
        <p text bold>
          <?= CurrentUser->settings->birthday
            ? "<span  color=company>" . date_format(date_create(CurrentUser->settings->birthday), 'd. F Y') . "</span>"
            : 'Birthday'; ?>
        </p>
        <p text>
          <?= CurrentUser->settings->birthday
            ? __("Birthday")
            : __("Add your birthday for surprises!"); ?>
        </p>
      </div>
    </div>
    <mi midler>east</mi>
  </div>

  <div style="height:1px;" minline12 background=slight></div>

  <div p12 style="padding-right:32px;" rounded=mid fl gap alic>
    <?php if (CurrentUser->country !== "xx") { ?>
      <div fl style=min-width:52px; jucc>
        <picture size=std circled>
          <img src="<?= IMAGE . "/country-flags/" . CurrentUser->country . ".svg"; ?>" loading=lazy />
        </picture>
      </div>

      <div fl fldircol gap=smoler>
        <p text bold color=company>
          <?= CurrentUser->country_string(); ?>
        </p>
        <p text slight><?= __("Country can not be changed") ?></p>
      </div>
    <?php } else { ?>
      <mi wide style="min-width:52px;">public</mi>
      <div>
        <p text std bold><?= __("No country detected") ?></p>
        <p text std>
          <?= __("Request a manual country change through our {discord-link}") ?>
        </p>
      </div>
    <?php } ?>
  </div>
</box-model>

<box-model filled mb p24>
  <div p12 fl fldircol gap=smol>
    <p text midler bold>Account</p>
    <p text std><?= __("These are hidden information that no one can see") ?></p>
  </div>

  <div hoverable p12 style="padding-right:32px;" rounded=mid
    open="personal:mail" fl gap alic jucsb>
    <div fl gap align-items=center>
      <div style="width:3.2em;" fl justify-content=center align-items=center>
        <mi wide>alternate_email</mi>
      </div>

      <?php if (!filter_var(CurrentUser->email, FILTER_VALIDATE_EMAIL)) { ?>
        <div>
          <p text std bold>E-mail address</p>
          <p text>Add your e-mail address for recovery</p>
        </div>
      <?php } else { ?>
        <div>
          <p text bold color=company><?= CurrentUser->email; ?></p>
          <p text>Connected e-mail address</p>
        </div>
      <?php } ?>
    </div>

    <mi midler>east</mi>
  </div>
</box-model>