<?php


?>

<content std minlineauto fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
    <p text mid bold>Modes</p>
  </div>

  <box-model rounded=mid p32>
    <p text bold midler mb6>Enabling modes</p>
    <p text><strong>Enabled modes</strong> will have <strong>influence on the achieved performance</strong> of your
      squad. Any of the enabled ones
      will
      be
      shown in the public facing profile of your squad.</p>
  </box-model>

  <form request="squad:update" fl fldircol gap=smol delay=20 responder=error>
    <?php foreach ($Squad->modes as $mode => $active) {

      switch ($mode) {
        case "osu":
          $mode_name = __("Standard");
          $mode_mods = ["vanilla", "relax", "autopilot"];
          break;

        case "ctb":
          $mode_name = "Catch the Beat";
          $mode_mods = ["vanilla", "relax"];
          break;

        case "taiko":
          $mode_name = ucfirst($mode);
          $mode_mods = ["vanilla", "relax"];
          break;

        case "mania":
          $mode_name = ucfirst($mode);
          $mode_mods = ["vanilla"];
          break;

        default:
          $mode_name = __("Unknown mode");
      }

    ?>

      <box-model outlined=darker rounded=mid>
        <bm-inr size=midler>
          <div fl fldircol gap=std>
            <div fl gap=wide justify-content="space-between" align-items=center>
              <div fl align-items=center gap=std>
                <p text style=height:4.2em;width:4.2em; outlined circled fl alic jucc>
                  <i text wide class="osu-icon osu-<?= $mode === "osu" ? "vanilla" : $mode; ?>"></i>
                </p>
                <div fl fldircol gap=smol>
                  <p text std bold><?= $mode_name; ?></p>
                  <div fl style=gap:.2em;>
                    <?php foreach ($mode_mods as $mode_mod) { ?>
                      <div size=smol rounded=wide background=invert>
                        <div pinline12 pblock4>
                          <p text std color=invert><?= ucfirst($mode_mod); ?></p>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <toggle-switch submit-closest toggled=<?= $active ? "true" : "false"; ?>>
                <div class="toggle_switch__inr">
                  <div class="toggle_switch__switcher">
                  </div>
                  <input type="hidden" name=<?= $mode; ?> value=<?= $active ? "1" : "0"; ?> />
                  <div fl fldirrow justify-content="center">
                    <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </div>
              </toggle-switch>
            </div>
          </div>
        </bm-inr>
      </box-model>
    <?php } ?>
  </form>
</content>