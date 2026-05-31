<?php

$legal_head_slogan = "";
$legal_head_title  = "";
$legal_head_url = "/home";

include TEMPLATE . "/legal/_header.php";

?>

<div class="legal__page_content__full" scroll-manipulated>
  <div class="legal__page_content__full_inr">
    <div class="inr__flex">
      <div flexone style="padding-block:25px 26px;">
        <p text wider bold>Hey <?= LOGGED ? CurrentUser->name : __("you"); ?>,</p>
        <p text mid><?= __("welcome to the legal section!"); ?></p>
      </div>
    </div>
  </div>
</div>

<div style=width:100%;>
  <div content-width=mid grid-repeat gap=smol flexone>
    <div grid-keeper>
      <a sub href="<?= "$base_url/privacy"; ?>">
        <box-model class="legal__page_card" clickable shadowed=min animation=slide-up>
          <div class="legal__page_card__image">
            <picture style="right:-6em;">
              <img src="<?= IMAGE . "/legal/privacy.svg"; ?>" />
            </picture>
          </div>

          <div class="legal__page_card__inr">
            <div class="legal__page_card__inr_aligner">
              <div class="legal__page_card__container">
                <div class="legal__page_card__container_inr">
                  <div class="legal__page_card__container_inr__textline" fl fldircol gap=smol+>
                    <div circled style=height:3.2em;width:3.2em; background=slight fl alic jucc>
                      <mi wide>policy</mi>
                    </div>
                    <div>
                      <p name text bold mid><?= __("Privacy") ?></p>
                      <p desc text std><?= __("Your data in our hands") ?></p>
                    </div>
                  </div>
                  <div mt=mid fl jucsb alic gap=smol>
                    <div has-tooltip=bottom>
                      <div class=textline fl align-items=center gap=smol>
                        <p text><?= $privacy_last_updated; ?></p>
                      </div>
                      <div ttooltip>
                        <p text bold><?= __("Last updated") ?></p>
                      </div>
                    </div>
                    <mi>arrow_forward</mi>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </box-model>
      </a>
    </div>

    <?php

    use Heiakim\Application\Cookie;

    $consent_step_cookie = Cookie::exists("POLICIES_CONSENT_STEP") ? Cookie::get("POLICIES_CONSENT_STEP") : "index";

    if (LOGGED && !CurrentUser->privacy->accepts_policies) {
      $consent_url = "/legal/consent/$consent_step_cookie";

    ?>


      <div grid-keeper>
        <a sub href="<?= $consent_url; ?>">
          <box-model class="legal__page_card" clickable shadowed=min animation=slide-up>
            <div class="legal__page_card__image">
              <picture style="right:-6em;">
                <img src="<?= IMAGE . "/legal/consent.svg"; ?>" />
              </picture>
            </div>

            <div class="legal__page_card__inr">
              <div class="legal__page_card__inr_aligner">
                <div class="legal__page_card__container">
                  <div class="legal__page_card__container_inr">
                    <div class="legal__page_card__container_inr__textline" fl fldircol gap=smol+>
                      <div circled style=height:3.2em;width:3.2em; background=slight fl alic jucc>
                        <mi wide>rocket_launch</mi>
                      </div>
                      <div>
                        <p name text bold mid><?= __("Policy consent") ?></p>
                        <p desc text std><?= __("A tour through space") ?></p>
                      </div>
                    </div>
                    <div mt=mid fl jucend>
                      <mi>arrow_forward</mi>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </box-model>
        </a>
      </div>

    <?php } ?>

    <div grid-keeper>
      <a sub href="<?= "$base_url/imprint"; ?>">
        <box-model class="legal__page_card" clickable shadowed=min animation=slide-up>
          <div class="legal__page_card__image">
            <picture style="right:-6em;">
              <img src="<?= IMAGE . "/legal/imprint.svg"; ?>" />
            </picture>
          </div>

          <div class="legal__page_card__inr">
            <div class="legal__page_card__inr_aligner">
              <div class="legal__page_card__container">
                <div class="legal__page_card__container_inr">
                  <div class="legal__page_card__container_inr__textline" fl fldircol gap=smol+>
                    <div circled style=height:3.2em;width:3.2em; background=slight fl alic jucc>
                      <mi wide>shield_person</mi>
                    </div>
                    <div>
                      <p name text bold mid><?= __("Imprint") ?></p>
                      <p desc text std><?= __("Who is responsible?") ?></p>
                    </div>
                  </div>
                  <div mt=mid fl jucend>
                    <mi>arrow_forward</mi>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </box-model>
      </a>
    </div>


    <div grid-keeper dno>
      <box-model class="legal__page_card" shadowed=min animation=slide-up>
        <div class="legal__page_card__image">
          <picture style="right:-6em;top:1.2em;">
            <img src="<?= IMAGE . "/legal/sources.svg"; ?>" />
          </picture>
        </div>

        <div class="legal__page_card__inr">
          <div class="legal__page_card__inr_aligner">
            <div class="legal__page_card__container">
              <div class="legal__page_card__container_inr">
                <div class="legal__page_card__container_inr__textline">
                  <p icon><i class="ri-copyleft-line"></i></p>
                  <p name text bold mid>Sources</p>
                  <p desc text std>Stuff from thrid parties</p>
                </div>
                <div mt=mid fl justify-content=end align-items=center gap>
                  <a>
                    <buttonmon btn-style=hkim size=std btn-bg=white class="lt">
                      <p text std semi-bold><i class="ri-arrow-right-line"></i></p>
                    </buttonmon>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </box-model>
    </div>


    <div grid-keeper>
      <a href="<?= "$base_url/team"; ?>">
        <box-model class="legal__page_card" clickable shadowed=min animation=slide-up>
          <div class="legal__page_card__image">
            <picture style="right:-6em;">
              <img src="<?= IMAGE . "/legal/team.svg"; ?>" />
            </picture>
          </div>

          <div class="legal__page_card__inr">
            <div class="legal__page_card__inr_aligner">
              <div class="legal__page_card__container">
                <div class="legal__page_card__container_inr">
                  <div class="legal__page_card__container_inr__textline" fl fldircol gap=smol+>
                    <div circled style=height:3.2em;width:3.2em; background=slight fl alic jucc>
                      <mi wide>diversity_1</mi>
                    </div>
                    <div>
                      <p name text bold mid><?= __("The Team") ?></p>
                      <p desc text std><?= __("Behind the scenes") ?></p>
                    </div>
                  </div>
                  <div mt=mid fl jucend>
                    <mi>arrow_forward</mi>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </box-model>
      </a>
    </div>


    <div grid-keeper>
      <a sub href="<?= "$base_url/development"; ?>">
        <box-model class="legal__page_card" clickable shadowed=min animation=slide-up>
          <div class="legal__page_card__image">
            <picture style="right:-6em;">
              <img src="<?= IMAGE . "/legal/dev.svg"; ?>" />
            </picture>
          </div>

          <div class="legal__page_card__inr">
            <div class="legal__page_card__inr_aligner">
              <div class="legal__page_card__container">
                <div class="legal__page_card__container_inr">
                  <div class="legal__page_card__container_inr__textline" fl fldircol gap=smol+>
                    <div circled style=height:3.2em;width:3.2em; background=slight fl alic jucc>
                      <mi wide>deployed_code</mi>
                    </div>
                    <div>
                      <p name text bold mid><?= __("Development") ?></p>
                      <p desc text std><?= __("A brief story about us") ?></p>
                    </div>
                  </div>
                  <div mt=mid fl jucend>
                    <mi>arrow_forward</mi>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </box-model>
      </a>
    </div>
  </div>
</div>