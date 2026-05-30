<?php

$legal_head_slogan = __("Your data in our hands");
$legal_head_title  = __("Privacy Policy");

include TEMPLATE . "/legal/_header.php";

?>

<div class=legal__page_content__full scroll-manipulated>
  <div class="legal__page_content__full_inr">
    <div class="inr__flex">
      <div flexone flex-truncate>
        <p text wider bold trimt title><?= __("Your data,") ?></p>
        <p text mid slogan><?= __("our responsibility. What we collect.") ?></p>
      </div>
      <div class="legal_image">
        <picture>
          <img src="<?= IMAGE . "/legal/privacy.svg"; ?>" />
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
            <div>
              <p>Justin-Leon Seidel</p>
              <p>Katerkampweg 46</p>
              <p>48431 Rheine</p>
              <p><?= __("Germany") ?></p>
            </div>
          </div>

          <div fl fldircol gap=smol+>
            <p text mid bold ttup><?= __("connect") ?></p>
            <div fl justify-content=start gap flex-wrap=wrap>
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-at-line"></i></p>
                <p>justin@heia.kim</p>
              </div>
            </div>
          </div>

          <div fl jucend alic gap=smol>
            <p text std><?= __("Last updated") ?></p>
            <mbutton material color=dynamic filled>
              <p text smol bold><?= $privacy_last_updated; ?></p>
            </mbutton>
          </div>
        </bm-inr>
      </box-model>
    </div>
  </div>

  <div class="legal__page">
    <div fl fldircol content-gap>
      <div fl fldircol gap=smol+>
        <div title-inline>
          <p text mid bold color=white><?= __("Introduction") ?></p>
        </div>

        <box-model rounded=wide elevated>
          <bm-inr size=wide>
            <p text><?= __("introduction_text") ?></p>
            <p mt=std><?= __("The terms used are not gender specific.") ?></p>
          </bm-inr>
        </box-model>
      </div>

      <div fl fldircol gap=smol+>
        <div title-inline>
          <p text mid bold color=white><?= __("Table of contents") ?></p>
        </div>

        <box-model rounded=wide elevated>
          <bm-inr size=wide>
            <div class="legal_menu" mt=std>
              <div class="legal_menu__option">
                <p><?= __("Responsible") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Introduction") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Overview of processing") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Relevant legal bases") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Safety measures") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Deletion of data") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Use of cookies") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Provision of the online offer and web hosting") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Registration, login and user accounts") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Web analysis, monitoring and optimization") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Presence in social networks (social media)") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Plugins, embedded functions and content") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Change and update of the privacy policy") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Rights of data subjects") ?></p>
              </div>
              <div class="legal_menu__option">
                <p><?= __("Term definitions") ?></p>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </div>

      <div fl fldircol gap=smol+>
        <div title-inline>
          <p text mid bold color=white><?= __("Overview") ?></p>
        </div>

        <box-model rounded=wide elevated>
          <bm-inr size=wide>
            <p text mt=std><?= __("The following overview summarizes the types of data processed, the purposes of their
              processing and refers to the data subjects.") ?></p>

            <div mt=std>
              <p text bold><?= __("Types of data processed") ?></p>

              <div class="legal_menu" mt=smol>
                <div class="legal_menu__option">
                  <p><?= __("Inventory data") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Contact data") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Content data") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Usage data") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Meta/Communication data") ?></p>
                </div>
              </div>
            </div>

            <div mt=std>
              <p text bold><?= __("Categories of data subjects") ?></p>

              <div class="legal_menu" mt=smol>
                <div class="legal_menu__option">
                  <p><?= __("User") ?></p>
                </div>
              </div>
            </div>

            <div mt=std>
              <p text bold mt=std><?= __("Purposes of processing") ?></p>

              <div class="legal_menu" mt=smol>
                <div class="legal_menu__option">
                  <p><?= __("Provision of contractual services and customer service") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Contact data") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Contact Requests and Communication") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Safety measures") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Range measurement") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Management and response to inquiries") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Feedback") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Marketing") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Profiles with user-related information") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("Provision of our online offer and user-friendliness") ?></p>
                </div>
                <div class="legal_menu__option">
                  <p><?= __("IT infrastructure") ?></p>
                </div>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </div>

      <?php include TEMPLATE . "/legal/privacy/_legal_basis.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_security_measures.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_deletion_data.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_cookies.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_hosting.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_signup_account.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_mailing.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_signup_with.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_community_functions.php"; ?>
      <!-- <?php include TEMPLATE . "/legal/privacy/_signup_with.php"; ?> -->
      <?php include TEMPLATE . "/legal/privacy/_webanalysis_monitoring.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_presence_social_media.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_plugins.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_update_policies.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_your_rights.php"; ?>
      <?php include TEMPLATE . "/legal/privacy/_definitions.php"; ?>

      <div justcontcent>
        <p text smol slight color=white>
          <a rel="noindex,nofollow" href="https://datenschutz-generator.de/" extern target=_blank><?= __("With help of Data Privacy Policy generator by Dr. Thomas Schwenke") ?></a>
        </p>
      </div>
    </div>
  </div>
</div>