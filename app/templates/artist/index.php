<?php

use Heiakim\Model\Artist;

include TEMPLATE . "/home/_page-navigator.php";
include __DIR__ . "/_header.php"; ?>

<content widest fl fldircol gap=wide>
  <div class="content-shelf carousel-shelf" fl fldircol gap>
    <div fl aliend jucsb>
      <div fl gap alic>
        <mbutton midler outlined icon-only no-hover>
          <mi>star</mi>
        </mbutton>
        <p text mid bold>Newcomer</p>
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
    <div fl aliend jucsb>
      <div fl gap alic>
        <mbutton midler outlined icon-only no-hover>
          <mi>trending_up</mi>
        </mbutton>
        <p text mid bold><?= __("Most played") ?></p>
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

    <carousel>
      <ul class="carousel">
        <?php

        # TODO: Most played artists as an own table being calulated by a job.

        $Artists = Artist::limit(24)->get();

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

  <div fl jucc style="max-width: 620px; margin-inline: auto" background="dynamic" pblock32 pinline32 rounded="wide" fl alistart gap=smol+>
    <mi>tips_and_updates</mi>
    <p text std>This is a test view of the artists page. A senseful structure is still being determined and will soon be implemented.</p>
  </div>

  <?php

  include TEMPLATE . "/global/_scroll_end_logo.php";
