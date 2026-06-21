<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Enum\SquadPrivilege;

/**
 * @var Squad $Squad
 */

?>

<div page-structure=squad>

  <?php if (CurrentUser->sqcan("manage", "members") && CurrentUser->squad->is($Squad)) : ?>
    <div fl jucsb top-actions>
      <div></div>
      <a href="/manage/squad/members">
        <mbutton mid background=slight has-icon=right>
          Manage Members
          <mi>arrow_forward</mi>
        </mbutton>
      </a>
    </div>
  <?php endif; ?>

  <div column-wrapper>

    <div column=small fl fldircol>
      <?php

      /**
       * @var SquadUser
       */
      $Chief = $Squad->chief();

      ?>

      <div fl fldircol gap=smoler>
        <div title-inline mb8 p4 fl alic gap=smol+>
          <p text midler bold><?= SquadPrivilege::CHIEF->get_display()->name ?></p>
        </div>
        <box-model filled=lighter rounded=wide>
          <div p32 fl fldircol alic gap=smol+>
            <picture size=wide circled posrel>
              <?= $Chief->user->image() ?>
              <div style="height:40px;width:40px;position:absolute;bottom:0;right:0;" z background=yellow color=light fl alic jucc circled>
                <mi midler color=light><?= SquadPrivilege::CHIEF->get_display()->icon ?></mi>
              </div>
            </picture>
            <div fl fldircol jucc alic>
              <p text mid bold><?= $Chief->name() ?></p>
              <div fl alic gap=smol>
                <p text slight>Joined</p>
                &middot;
                <p text color=company><?= Time::ago($Squad->created_at, true) ?></p>
              </div>
            </div>
          </div>
        </box-model>
      </div>

      <?php

      /**
       * @var ?SquadRequest
       */
      $PendingInvites = $Squad->invites;

      ?>

      <div fl fldircol gap=smoler>
        <?php if ($PendingInvites->count()) : ?>
          <div title-inline mb8 p4 fl alic jucsb>
            <div fl alic gap=smol>
              <p text midler bold>Pending invites</p>
              &middot;
              <p text midler color=company><?= $PendingInvites->count() ?></p>
            </div>
            <mi>call_made</mi>
          </div>

          <?php foreach ($PendingInvites->take(6) as $Invite) :

            /**
             * @var SquadRequest $Invite
             */

            /**
             * @var User
             */
            $User = $Invite->affected_user;

          ?>

            <a href="<?= $User->link() ?>">
              <box-model outlined=darker hoverable rounded=wide>
                <div p10 style="padding-right:18px;" fl jucsb alic>
                  <div fl alic gap=smol+>
                    <picture size=std circled>
                      <?php $User->image() ?>
                    </picture>
                    <div fl fldircol>
                      <p text semibold><?= $User->name() ?></p>
                      <div fl alic gap=smoler text smol>
                      </div>
                    </div>
                  </div>
                  <mi std>arrow_forward</mi>
                </div>
              </box-model>
            </a>

        <?php endforeach;
        endif; ?>
      </div>
    </div>


    <!--- Middle content --->
    <div column=large fl fldircol flexone>
      <?php

      # Sort all members by their grades starting by the highest privlege and add it
      # to an array so we can iterate through it.

      $Grades_w_Members = [];
      $added_ids = [];

      foreach (SquadPrivilege::cases() as $key => $Grade) :

        $Grades_w_Members[$Grade->get_display()->name]["Grade"] = $Grade;

        foreach ($Squad->members as $Member) {
          if ($Member->has_privileges_of($Grade) && !in_array($Member->id, $added_ids)) {
            $Grades_w_Members[$Grade->get_display()->name]["Members"][] = $Member;
            $added_ids[] = $Member->id;
          }
        }

      endforeach; ?>

      <?php foreach ($Grades_w_Members as $grade_name => $Iteration) :

        /**
         * @var SquadPrivilege
         */
        $Grade = $Iteration["Grade"];

        # Continue on Chief.
        if ($Grade === SquadPrivilege::CHIEF) continue;

        # Different view for restricted members count.
        if ($Grade === SquadPrivilege::MEMBER) : ?>

          <?php if (isset($Iteration["Members"])) : ?>
            <div background=slighter rounded fl alic jucc pinline24 pblock24>
              <p text smol>
                Restricted members not shown &nbsp;&middot;&nbsp;
                <strong color=company><?= count($Iteration["Members"]) ?></strong>
              </p>
            </div>
          <?php endif; ?>

          <?php continue; ?>
        <?php endif; ?>


        <?php if (!isset($Iteration["Members"])) : ?>

          <box-model dno slight outlined rounded=wide fl fldircol jucc alic gap=smol pinline32 pblock42>
            <div fl jucc alic gap=smol+>
              <mi mid><?= $Grade->get_display()->icon ?></mi>
              <p text semibold midler><?= $Grade->get_display()->name ?></p>
            </div>
          </box-model>

        <?php else :

          /**
           * @var SquadUser
           */
          $LatestMember = collect($Iteration["Members"])
            ->sortByDesc("created_at")
            ->first();

        ?>

          <div fl fldircol gap=smol+>
            <div fl alic jucsb gap alic title-inline>
              <div fl alic gap=smol>
                <div fl alic gap=smol>
                  <mi><?= $Grade->get_display()->icon ?></mi>
                  <p text midler bold><?= $grade_name ?></p>
                </div>
                <p text bold>&middot;</p>
                <p text midler color=company><?= count($Iteration["Members"]) ?></p>
              </div>

              <div fl alic gap=smol hide-mobile>
                <a href="<?= $LatestMember->user->link() ?>">
                  <div fl alic gap=smol hoverable p4 rounded=wide pr6>
                    <picture size=smol circled>
                      <?php $LatestMember->image() ?>
                    </picture>
                    <p text><?= $LatestMember->name() ?></p>
                  </div>
                </a>
                &middot;
                <p text color=company><?= Time::ago($LatestMember->created_at, true) ?></p>
              </div>
            </div>


            <div members fl alistart flex-wrap gap=smol>
              <?php foreach ($Iteration["Members"] ?? [] as $Member) :

                /**
                 * @var User
                 */
                $User = $Member->user;

              ?>
                <a href="<?= $User->link() ?>">
                  <box-model outlined=darker hoverable rounded>
                    <div pblock24 pinline24 style=padding-right:16px; fl jucsb alic>
                      <div fl alic gap=smol+>
                        <picture size=std circled>
                          <?php $User->image() ?>
                        </picture>
                        <div fl fldircol>
                          <p text midler bold><?= $User->name() ?></p>
                          <div fl alic gap=smoler text smol>
                            <p slight>Member since</p>
                            &middot;
                            <p color=company><?= Time::ago($LatestMember->created_at) ?></p>
                          </div>
                        </div>
                      </div>

                      <mi std>arrow_forward</mi>
                    </div>
                  </box-model>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!--- Right column --->
    <div column=small fl fldircol hide-tablet>
      <?php

      /**
       * @var SquadFeedItem
       */
      $FeedItems = $Squad
        ->feed_items
        ->sortByDesc("created_at");

      ?>

      <div fl fldircol gap=smoler>
        <div pinline20 pblock4 fl alic jucsb mb8 gap=smol>
          <p text midler bold>Recent happenings</p>
          <mi>history_2</mi>
        </div>

        <?php

        /**
         * @var SquadFeedItem
         */
        $FeedItems = $Squad
          ->feed_items
          ->filter(function ($item) {
            return in_array($item->type, [
              "__squad__/created",
              "__member__/joined",
              "__member__/left",
              "__member__/kicked",
              "__member__/chief+new",
              "__member__/promoted",
              "__member__/demoted",
            ]);
          })
          ->sortByDesc("created_at");

        foreach ($FeedItems->take(6) as $Item) {

          /**
           * @var SquadFeedItem $Item
           */

          /**
           * @var User
           */
          $User = !$Item->user->is($Item->affected_user) ? $Item->affected_user : $Item->user;

          /**
           * @var object
           */
          $display = $Item->display_type();

        ?>

          <box-model filled=lighter rounded=wide>
            <div p8 fl jucsb alic>
              <div fl alic gap=smol>
                <div filled=darker style="height:calc(1.6em + 8px);width:calc(1.6em + 8px);margin-right:-1em;border-width:4px;" outlined=lighter z fl jucc alic circled>
                  <mi smol><?= $display->icon; ?></mi>
                </div>
                <div fl alic gap=smoler>
                  <?php if (!in_array($Item->type, ["__squad__/edit/publicity"]) && $User) { ?>
                    <a href="<?= $User->link(); ?>">
                      <picture clickable-zoom clickable size=smol circled>
                        <?php $User->image(); ?>
                      </picture>
                    </a>
                  <?php } else { ?>
                    <mi>draw_abstract</mi>
                  <?php } ?>
                </div>
                <p text flexone>
                  <?php if (!in_array($Item->type, ["__squad__/edit/publicity"]) && $User) { ?>
                    <a href="<?= $User->link(); ?>" normal><strong><?= $User->name(); ?></strong></a>
                  <?php } ?>

                  <?= $display->append_text; ?>
                </p>
              </div>
              <p text color=company pr8><?= Time::ago($Item->created_at) ?></p>
            </div>
          </box-model>

        <?php } ?>
      </div>
    </div>

  </div>
</div>