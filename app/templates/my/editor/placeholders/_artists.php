<?php

use Heiakim\Model\Profile;

/**
 * @var Profile $Profile
 * @var bool $object_visibility
 * @var string $object_name
 * @var string $wrapper
 */

?>

<div fl jucsb alic>
  <p text bold ttup title-inline><?= $Profile->get_object_title($object_name); ?></p>
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

<div fl fldircol gap=smoler>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=std></bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=std></bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=std></bm-inr>
  </box-model>
  <box-model grid-keeper outlined rounded=mid>
    <bm-inr size=std></bm-inr>
  </box-model>
</div>