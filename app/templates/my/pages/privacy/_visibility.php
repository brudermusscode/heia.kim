  <div fl gap=mid alic>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold><?= __("Public profile") ?></p>
      </div>
    </label>
  </div>

  <form request="user:settings:privacy:update" delay="200" no-loader radio responder=error>
    <div fl fldircol gap=mid>
      <div fl gap=smol fldircol>
        <div fl justify-content=space-between align-items=center gap=mid>
          <div style=flex:1;>
            <p text std>
              <?= __("When turned off, your profile will be hidden from any section of this website, including other profiles, leaderboard, beatmap scores and ranking.") ?>
            </p>
          </div>

          <div>
            <toggle-switch submit-closest mt=smol toggled=<?= CurrentUser->privacy->is_public ? "true" : "false"; ?>>
              <div class="toggle_switch__inr">
                <div class="toggle_switch__switcher"></div>
                <input type="hidden" name="is_public" value="<?= CurrentUser->privacy->is_public; ?>" />
                <div fl fldirrow justify-content="center">
                  <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                  </div>
                </div>
              </div>
            </toggle-switch>
          </div>
        </div>
      </div>
    </div>
  </form>

  <?php include TEMPLATE . "/my/_autosave.php";
