<?php

use Heiakim\Model\Artist;

?>

<header page="" scroll-manipulated="">
  <div class="title">
    <div fl="" align-items="center" fldircol="">
      <h1 text="" wide="" bold="" title=""><?= __("Artists") ?></h1>
      <section>
        <p text="" smol="" tac><?= __("All the artists you can find through our beatmap collection") ?>
        </p>
      </section>
    </div>
  </div>
</header>

<content widest artists-content fl fldircol gap=wide>
  <div class="content-shelf carousel-shelf" fl fldircol gap>
    <div title-inline>
      <div fl aliend jucsb>
        <div class=cs__label>
          <div fl gap=smol alic class=cs__label_title>
            <mbutton mid filled icon-only no-hover>
              <mi mid>star</mi>
            </mbutton>
            <p text bold>Newcomer</p>
          </div>
        </div>

        <div class="carousel-shelf-button-group">
          <div fl gap=smol>
            <mbutton icon-only outlined carousel-action=previous disabled>
              <mi>west</mi>
            </mbutton>
            <mbutton icon-only outlined carousel-action=next>
              <mi>east</mi>
            </mbutton>
          </div>
        </div>
      </div>
    </div>

    <carousel>
      <ul class="carousel">
        <?php

        /**
         * Newly added
         */
        $Artists = Artist::orderByDesc("id")->limit(12)->get();

        foreach ($Artists as $key => $Artist) {
          $big_cover = true;

        ?>

          <div class=carousel-item>
            <?php include __DIR__ . "/_artist.php"; ?>
          </div>

        <?php } ?>
      </ul>
    </carousel>
  </div>

  <div class="content-shelf carousel-shelf" fl fldircol gap>
    <div title-inline>
      <div fl aliend jucsb>
        <div class=cs__label>
          <div fl gap=smol alic class=cs__label_title>
            <mbutton mid filled icon-only no-hover>
              <mi mid>trending_up</mi>
            </mbutton>
            <p text bold><?= __("Most played") ?></p>
          </div>
        </div>

        <div class="carousel-shelf-button-group">
          <div fl gap=smol>
            <mbutton icon-only outlined carousel-action=previous disabled>
              <mi>west</mi>
            </mbutton>
            <mbutton icon-only outlined carousel-action=next>
              <mi>east</mi>
            </mbutton>
          </div>
        </div>
      </div>
    </div>

    <carousel>
      <ul class="carousel">
        <?php

        /**
         * Most played Artists
         */
        $Artists = Artist::select('artists.*')
          ->join('mapset_artists', 'artists.id', '=', 'mapset_artists.artist_id')
          ->join('mapsets', 'mapset_artists.mapset_id', '=', 'mapsets.id')
          ->join('maps', 'mapsets.id', '=', 'maps.set_id')
          ->groupBy('artists.id')
          ->orderByRaw('SUM(maps.plays) DESC')
          ->limit(24)
          ->get();

        foreach ($Artists as $key => $Artist) {
          $big_cover = true;

        ?>

          <div class=carousel-item>
            <?php include __DIR__ . "/_artist.php"; ?>
          </div>

        <?php } ?>
      </ul>
    </carousel>
  </div>
  </div>

  <div mt="wide" fl justify-content="center" style="max-width: 620px; margin-inline: auto">
    <div background="dynamic" pblock32 pinline32 rounded="wide">
      <div fl gap>
        <p text mid normalize-icon>
          <i class="mi">tips_and_updates</i>
        </p>
        <p text std>This is a test view of the artists page. A senseful structure is still being determined and will
          soon be
          implemented.</p>
      </div>
    </div>
  </div>

  <?php

  include TEMPLATE . "/global/_scroll_end_logo.php";
