<?php

$legal_head_slogan = __("Who is responsible?");
$legal_head_title  = __("Imprint");

include TEMPLATE . "/legal/_header.php";

?>

<div class="legal__page_content__full" scroll-manipulated>
  <div class="legal__page_content__full_inr">
    <div class="inr__flex">
      <div flexone flex-truncate>
        <p text wider bold trimt><?= __("That guy") ?>,</p>
        <p text mid><?= __("who takes the responsibility") ?>.</p>
      </div>
      <div class="legal_image">
        <picture>
          <img src="<?= IMAGE . "/legal/imprint.svg"; ?>" />
        </picture>
      </div>
    </div>
  </div>
</div>

<div content-width=mid fl fldircol content-gap>
  <div class="legal__page" first>
    <div class="legal__page_content">
      <box-model filled=lighter elevated>
        <bm-inr size=wide fl fldircol gap=mid>
          <div fl fldircol gap=smol+>
            <p text mid bold ttup><?= __("Responsible") ?></p>
            <div fl fldircol gap=smol+>
              <strong><?= __("Entries referred to") ?> <div class="legal_para" inline><?= __("§ 5 TMG") ?>
                </div></strong>
              <div>
                <p>Justin-Leon Seidel</p>
                <p>Katerkampweg 46</p>
                <p>48431 Rheine</p>
                <p><?= __("Germany") ?></p>
              </div>

              <div>
                <p><strong><?= __("Represented by") ?>:</strong></p>
                <p>Justin-Leon Seidel</p>
              </div>
            </div>
          </div>

          <div fl fldircol gap=smol+>
            <p text mid bold ttup><?= __("connect") ?></p>
            <div fl jucstart gap=smol flex-wrap=wrap>
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-at-line"></i></p>
                <p>justin@heia.kim</p>
              </div>

              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-smartphone-line"></i></p>
                <p>+49 1577-3602821</p>
              </div>
            </div>
          </div>
        </bm-inr>
      </box-model>
    </div>
  </div>

  <div class="legal__page">
    <div fl fldircol gap=smol+>
      <div title-inline id="relevant-legal-bases">
        <p text mid bold color=white><?= __("Disclaimer") ?></p>
      </div>

      <box-model rounded=wide elevated>
        <bm-inr size=wide>
          <div text>
            <p mb=smol text bold><?= __("Liability for content") ?></p>
            <?= __("legal.imprint.liability.1") ?>
          </div>

          <div text mt=wide>
            <p mb=smol text bold><?= __("Liability for links") ?></p>
            <?= __("legal.imprint.liability.2") ?>
          </div>

          <div text mt=wide>
            <p mb=smol text bold><?= __("Copyright") ?></p>
            <?= __("legal.imprint.copyright.1") ?>
          </div>

          <div text mt=wide>
            <p mb=smol text bold><?= __("Privacy") ?></p>
            <?= __("legal.imprint.privacy.1") ?>
          </div>
        </bm-inr>
      </box-model>
    </div>
  </div>
</div>