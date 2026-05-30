<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Artist;

/**
 * @var ?Score
 */
$Score = $Notification->reference;

/**
 * @var ?Report
 */
$Report = $CurrentUser->reports()
  ->where("report_type", "score")
  ->where("reference_id", $Score?->id)
  ->first();

/**
 * @var Beatmap
 */
$Beatmap = $Score?->beatmap;

if (!$Score || !$Report || !$Beatmap) :
  include $unavailable;
else :


  /**
   * Create beatmap artists
   */
  $Beatmap->create_featured_artists();

  /**
   * @var Artist
   */
  $Artists = $Beatmap->set->artists;

  /**
   * @var User
   */
  $User = $Score->user;

?>

  <div notification report data-id="<?= $Notification->id; ?>"
    <?php if (!$Notification->read_at) echo " unread "; ?> data-reference-id="<?= $Notification->reference_id; ?>">
    <div fl gap>
      <picture class=notif__element_picture>
        <img src="<?= EMOJI . "/doge-ban.png"; ?>" />
        <div class=type_badge type=report>
          <mi><?= $type_icon; ?></mi>
        </div>
      </picture>

      <div class="notif__element_content">
        <div style=line-height:1.2em;>
          <p text std trimt bold>You've created a report</p>
          <p text smol slight><?= $timestamp; ?></p>
        </div>
      </div>
    </div>

    <box-model filled=lighter mt=smol rounded=smol+ flexone>
      <div p14 style="padding-top:10px;">
        <div flex-truncate>
          <div fl alic>
            <p text std bold color=company><?= number_format($Score->pp); ?></p>
            <mi smol pt4><?= METRIC_ICON; ?></mi>
            <p pinline6>&middot;</p>
            <a href="<?= $User->link() ?>">
              <div fl alic gap=smol p4 rounded hoverable>
                <picture style="margin-top:-5px;" size=smoler circled>
                  <?php $User->image() ?>
                </picture>
                <p text std><?= $User->name; ?></p>
              </div>
            </a>
          </div>
          <div flex-truncate>
            <p text trimt std><strong><?= htmlspecialchars($Beatmap->stripped_title()); ?></strong> &middot;
              <?= htmlspecialchars($Artists->first()->name); ?></p>
          </div>
        </div>
      </div>
    </box-model>
  </div>

<?php endif;
