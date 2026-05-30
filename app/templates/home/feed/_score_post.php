<?php

use Bruder\Time\Time;

/**
 * User
 */
$User = $Score->user;

?>

<div grid-keeper>
  <box-model filled score-post data-id="<?= $Score->id; ?>">
    <bm-inr style=padding:.2em;>
      <div class=user>
        <div>
          <a href="<?= $User->link(); ?>" fl gap align-items=center>
            <div>
              <picture circled size=std>
                <?php $User->image(); ?>
              </picture>
            </div>
            <div>
              <p text std bold>
                <?= $User->name(); ?>
              </p>
              <p text smol style=opacity:.6;><?= Time::ago($Score->play_time); ?></p>
            </div>
          </a>
        </div>

        <div class="actions">
          <div>
            <p>&nbsp;</p>
          </div>

          <div>
            <form data-form="feedback:create">
              <input type=hidden name=type value=score />
              <input type=hidden name=reference_id value="<?= $Score->id; ?>" />
              <input type=hidden name=action value=thumb_up />
              <div has-count>
                <div count>
                  <p text std bold><?= $Score->feedback()->count(); ?></p>
                </div>
                <mbutton submit-closest icon-only size=std material background=slighter ripple-effect
                  <?php if ($Score->has_received_feedback_from($CurrentUser->id)) echo "active"; ?>>
                  <div>
                    <p>
                      <i class="mi">thumb_up</i>
                    </p>
                  </div>
                </mbutton>
              </div>
            </form>
          </div>

          <div>
            <mselect size=std align=right mselect-type=menu clickable clean>
              <div class=mselect__inr fl align-items=center gap=smol justify-content=center>
                <p mselect-visible-value text bold>
                  <i class="mi">more_vert</i>
                </p>
              </div>
              <mselect-dropdown>
                <div get-size>
                  <div class=msd__inr>
                    <mselect-option mselect-input-value mselect-change-visible-value disabled>
                      <p>Report</p>
                    </mselect-option>
                  </div>
                </div>
              </mselect-dropdown>
            </mselect>
          </div>
        </div>
      </div>

      <div class=content>
        <?php

        $clean_appearance = true;
        $hide_reactions = true;

        include TEMPLATE . "/score/_score.php";

        ?>
      </div>
    </bm-inr>
  </box-model>
</div>