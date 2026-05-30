<!--- MOST PLAYED TODAY --->

<?php

$Beatmaps = $Feed->most_played_today($base_limit);

?>

<feed-section four-hide>
  <div class="feed_section__inr">
    <div mb=std pblock12>
      <p text midler semi-bold>Most played today</p>
    </div>

    <div class="feed_section__content">
      <div grid-repeat gap="smol">

        <?php if (!$Beatmaps->count()) { ?>

          <box-model outlined style="max-width:620px">
            <bm-inr size=wide>
              <div fl fldircol gap>
                <p>
                  <i class="mi" size=wide>slow_motion_video</i>
                </p>
                <div fl fldircol gap=smol>
                  <p text std bold>Nothing played</p>
                  <p text std>There has nothing been played today.</p>
                </div>
              </div>
            </bm-inr>
          </box-model>

        <?php

        } else
          foreach ($Beatmaps as $key => $Beatmap) {
            $show_play_count = true;
            $beatmap_play_count = $Beatmap->scores_count;

            include COMPONENT . "/beatmaps/_beatmap.php";
          }

        unset($Beatmaps);

        ?>
      </div>
    </div>
  </div>
</feed-section>