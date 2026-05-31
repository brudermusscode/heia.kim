<div fl fldircol gap>
  <box-model outlined=darker>
    <div p24>
      <div p12>
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

      <div mt=smol mb=smol style="height:1px;width:calc(100% - 2.4em);margin-inline:1.2em;" filled=darker></div>

      <div hoverable p12 style="padding-right:32px;" rounded=mid
        data-category=<?= $category ?>
        data-sub=name>
        <div fl gap align-items=center justify-content=space-between>
          <div fl gap align-items=center>
            <div style="width:3.2em;" fl justify-content=center align-items=center>
              <p>
                <i class="mi" size=mid>format_size</i>
              </p>
            </div>

            <div>
              <p text std bold><?= CurrentUser->name; ?></p>
              <p text std><?= __("Public username") ?></p>
            </div>
          </div>

          <mi midler>east</mi>
        </div>
      </div>

      <div mt=smol mb=smol style="height:1px;width:calc(100% - 2.4em);margin-inline:1.2em;" filled=darker></div>

      <div hoverable p12 style="padding-right:32px;" rounded=mid
        data-category=<?= $category ?>
        data-sub=birthday>
        <div fl gap align-items=center justify-content=space-between>
          <div fl gap align-items=center>
            <div style="width:3.2em;" fl justify-content=center align-items=center>
              <mi wide>celebration</mi>
            </div>

            <div>
              <p text bold>
                <?= CurrentUser->settings->birthday
                  ? date_format(date_create(CurrentUser->settings->birthday), 'd. F Y')
                  : 'Birthday'; ?>
              </p>
              <p text>
                <?= CurrentUser->settings->birthday ? __("Birthday") : __("Add your birthday for surprises!"); ?>
              </p>
            </div>
          </div>

          <mi midler>east</mi>
        </div>
      </div>

      <div mt=smol mb=smol style="height:1px;width:calc(100% - 2.4em);margin-inline:1.2em;" filled=darker></div>

      <div p12 style="padding-right:32px;" rounded=mid>
        <div fl gap align-items=center justify-content=space-between>
          <div fl gap align-items=center>

            <?php if (CurrentUser->country !== "xx") { ?>

              <div fl style=width:3.2em; justify-content=center>
                <picture size=std circled>
                  <img src="<?= IMAGE . "/country-flags/" . CurrentUser->country . ".svg"; ?>" loading=lazy />
                </picture>
              </div>

              <div fl fldircol gap=smoler>
                <p text std bold>
                  <?= CurrentUser->country_string(); ?>
                </p>
                <p text slight><?= __("Country can not be changed") ?></p>
              </div>

            <?php } else { ?>

              <div fl style=width:3.2em; justify-content=center>
                <mi wide>public</mi>
              </div>

              <div>
                <p text std bold><?= __("No country detected") ?></p>
                <p text std>
                  <?= __("Request a manual country change through our {discord-link}") ?>
                </p>
              </div>

            <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </box-model>
</div>

<div fl fldircol gap>
  <box-model filled mb>
    <bm-inr size=mid>
      <div p12>
        <p text midler bold>Account</p>
        <p text std><?= __("These are hidden information that no one can see") ?></p>
      </div>

      <div hoverable p12 style="padding-right:32px;" rounded=mid
        data-category=<?= $category ?>
        data-sub=mail>
        <div fl gap align-items=center justify-content=space-between>
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
                <p text std bold><?= CurrentUser->email; ?></p>
                <p text>Connected e-mail address</p>
              </div>
            <?php } ?>
          </div>

          <mi midler>east</mi>
        </div>
      </div>
    </bm-inr>
  </box-model>
</div>