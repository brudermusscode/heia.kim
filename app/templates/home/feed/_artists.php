<?php

$FavoriteArtists = CurrentUser->favorite_artists;

?>

<feed-section>
  <div class="feed_section__inr" fl fldircol gap=smol+>
    <div title-inline>
      <p text mid bold><?= __("Artists you liked") ?></p>
    </div>

    <slider-model dno>
      <slider active></slider>
      <slider-move></slider-move>
      <slider></slider>
      <input type=hidden name=months value="1" />
    </slider-model>

    <div dno>
      <input type="range" min="1" max="10" value="1" />
    </div>

    <div class="feed_section__content">

      <div grid-repeat gap=smol>
        <?php if (!$FavoriteArtists->count()) { ?>
          <div grid-keeper>
            <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap flexone>
              <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                <i class="mi" size="wide">stars</i>
              </div>
              <div tac>
                <p text bold wide><?= __("No artists") ?></p>
                <p text std><?= __("You haven't liked any artists.") ?></p>
              </div>
              <div fl jucc gap=smol>
                <a href="/artists">
                  <mbutton material size=mid has-icon=left filled>
                    <mi>explore</mi>
                    <p text bold><?= __("Explore Artists") ?></p>
                  </mbutton>
                </a>
                <mbutton data-action="search:open" material size=mid has-icon=left filled>
                  <mi>search</mi>
                  <p text bold><?= __("Search") ?></p>
                </mbutton>
              </div>
            </box-model>
          </div>
        <?php

        } else
          foreach ($FavoriteArtists as $Feedback) {
            $Artist = $Feedback->reference;

            echo "<div grid-keeper>";
            include TEMPLATE . "/artist/_artist.php";
            echo "</div>";
          }

        ?>
      </div>
    </div>
  </div>
</feed-section>