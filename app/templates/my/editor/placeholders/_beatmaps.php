<?php

use Heiakim\Model\Profile;

/**
 * @var Profile $Profile
 * @var bool $object_visibility
 * @var string $object_name
 * @var string $wrapper
 */

?>

<div fl alic jucsb>
  <div fl fldircol alistart gap=smoler title-inline>
    <p text bold mid><?= $Profile->get_object_title($object_name); ?></p>
  </div>
  <toggle-switch toggled=<?= $object_visibility ? "true" : "false"; ?>>
    <div class="toggle_switch__inr">
      <div class="toggle_switch__switcher"></div>
      <input type="hidden" name="profile[<?= $wrapper; ?>][][<?= $object_name; ?>]" value="<?= !$object_visibility ? "0" : "1"; ?>" />
      <div fl fldirrow justify-content="center">
        <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
        </div>
      </div>
    </div>
  </toggle-switch>
</div>

<div grid-repeat gap=smol>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=wider>&nbsp;</bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=widerer>&nbsp;</bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=wider>&nbsp;</bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=wider>&nbsp;</bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=wider>&nbsp;</bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=wider>&nbsp;</bm-inr>
  </box-model>
</div>