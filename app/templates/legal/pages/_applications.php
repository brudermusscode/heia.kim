<?php

use Bruder\Heiakim\Enum\Privilege;

$legal_head_slogan = "Join our team";
$legal_head_title  = "Applications";

include TEMPLATE . "/legal/_header.php";

?>

<div content-width=pre-std fl fldircol content-gap mt>
  <div class="legal__page" color=light fl fldircol gap=mid>
    <div fl jucc>
      <picture style=max-width:400px;margin-bottom:-5.7em;>
        <img src="<?= IMAGE . "/legal/apply.svg" ?>" />
      </picture>
    </div>

    <box-model filled color=dynamic z>
      <bm-inr size=wide>
        <div fl fldircol gap=smol>
          <h2><?= __("Apply to join our team") ?></h2>
          <p text>
            <?= __("Down below you can find links to the applications of positions that applications are currently open to.") ?>
          </p>
        </div>
      </bm-inr>
    </box-model>

    <div fl fldircol gap=smol+>
      <h2 title-inline><?= __("Open applications") ?></h2>

      <div fl fldircol gap=smol>
        <a href="https://zajtg1txfgh.typeform.com/to/wc8EUece" extern target="_blank">
          <box-model background=company color=light grid-keeper clickable>
            <bm-inr size=std fl jucsb alic gap>
              <div fl gap alic>
                <div circled style=height:3.8em;min-width:3.8em; fl alic jucc background=light color=dark>
                  <mi mid><?= Privilege::MODERATOR->get_display()->icon; ?></mi>
                </div>
                <div>
                  <p text mid bold>Moderators</p>
                  <p text std>
                    <?= __("Keep the website clean and the gameplay fair. Moderate various sections on {app-name}") ?>
                  </p>
                </div>
              </div>
              <mi>arrow_forward</mi>
            </bm-inr>
          </box-model>
        </a>

        <a href="https://zajtg1txfgh.typeform.com/to/HQOOAzp2" extern target="_blank">
          <box-model background=company color=light grid-keeper clickable>
            <bm-inr size=std fl jucsb alic gap>
              <div fl gap alic>
                <div circled style=height:3.8em;min-width:3.8em; fl alic jucc background=light color=dark>
                  <mi mid><?= Privilege::NOMINATOR->get_display()->icon; ?></mi>
                </div>
                <div>
                  <p text mid bold>Beatmap Nominators</p>
                  <p text std><?= __("Nominate beatmaps for ranked or loved state in your desired game mode") ?>
                  </p>
                </div>
              </div>
              <mi>arrow_forward</mi>
            </bm-inr>
          </box-model>
        </a>

        <a href="https://zajtg1txfgh.typeform.com/to/JxbA4PF7" extern target="_blank">
          <box-model background=company color=light grid-keeper clickable>
            <bm-inr size=std fl jucsb alic gap>
              <div fl gap alic>
                <div circled style=height:3.8em;min-width:3.8em; fl alic jucc background=light color=dark>
                  <mi mid><?= Privilege::SUPPORTER->get_display()->icon; ?></mi>
                </div>
                <div>
                  <p text mid bold>Assistants</p>
                  <p text><?= __("Assist the team on the discord and process support tickets") ?></p>
                </div>
              </div>
              <mi>arrow_forward</mi>
            </bm-inr>
          </box-model>
        </a>
      </div>
    </div>
  </div>
</div>