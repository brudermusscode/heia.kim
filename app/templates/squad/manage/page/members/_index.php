<?php

use Bruder\Heiakim\Enum\SquadPrivilege;

/**
 * @var string $tab
 * @var User $CurrentUser
 */

/**
 * @var SquadUser
 */
$Members = $Squad->members;

?>

<div fl fldircol gap=mid>
  <?php

  foreach (SquadPrivilege::cases() as $Privilege) {

    /**
     * Show restircted users in a special way. Filthy cheaters.
     */
    if ($Privilege === SquadPrivilege::UNRESTRICTED)
      continue;

    $Members = $Squad->members()
      ->whereRaw("(clan_priv & ?) != 0", $Privilege->value)
      ->get();

    /**
     * @var string
     */
    $privilege_name = $Privilege->get_display()->name;
    $privilege_icon = $Privilege->get_display()->icon;

    /**
     * @var HTML
     */
    $none = <<<HTML
        <box-model rounded=mid>
          <bm-inr size=wide fl fldircol gap=smol+ alic jucc>
            <div style="height:3.2em;width:3.2em;" filled=darker circled fl jucc alic>
              <mi>$privilege_icon</mi>
            </div>
            <div tac>
              <p text midler bold>None</p>
              <p text>There are no $privilege_name<span>s</span></p>
            </div>
          </bm-inr>
        </box-model>
      HTML;

  ?>

    <div fl fldircol gap=smol>
      <p text bold midler title-inline><?= $Privilege->get_display()->name; ?></p>

      <?php

      if (!$Members->count())
        echo $none;
      else {

        /**
         * @var int
         */
        $members_count = 0;

        foreach ($Members as $Member) {

          /**
           * Only show the chief in the first place.
           */
          if ($Privilege !== SquadPrivilege::CHIEF && $Member->is_owner())
            continue;

          /**
           * Continue on Member privilege for any user, that
           * has higher privileges than Member and being unrestricted.
           */
          if (
            $Privilege === SquadPrivilege::MEMBER
            && count($Member->privileges()) > 2
            && $Member->has_privileges_of(SquadPrivilege::UNRESTRICTED)
          )
            continue;

          /**
           * Continue on restricted members.
           */
          if ($Member->is_restricted())
            continue;

          /**
           * Increase the member count.
           */
          if ($Privilege === SquadPrivilege::MEMBER)
            $members_count++;

          /**
           * @var User
           */
          $User = $Member->user;

          /**
           * Privilege
           */
          $Privileges = $SquadUser->privileges();

          /**
           * Include the Member card.
           */
          include dirname(dirname(__DIR__)) . "/_member.php";
        }

        /**
         * If no member is displayed, show the none here dialogue.
         */
        if (!$members_count && $Privilege === SquadPrivilege::MEMBER)
          echo $none;
      }

      ?>
    </div>
  <?php } ?>
</div>