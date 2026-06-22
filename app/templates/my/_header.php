<?php

/**
 * @var string $sub
 * @var string $category
 * @var string $var
 */

$title_w_desc = [
  "overview" => [
    __("Hey") . ", " . CurrentUser->name,
    __("Welcome to the account manager"),
    "dashboard"
  ],
  "personal" => [
    __("Personal Information"),
    __("You and your profile"),
    "insert_emoticon"
  ],
  "game" => [
    __("Gameplay"),
    __("Your journey and playing preferences"),
    "extension"
  ],
  "privacy" => [
    __("Data & Privacy"),
    __("Manage your data and what other players see"),
    "shield_person"
  ],
  "security" => [
    __("Security"),
    __("Secure your account from third party access"),
    "vpn_key"
  ],
  "website" => [
    "Appearance",
    __("Increase your comfort by adjusting the website to your needs"),
    "desktop_mac"
  ],
];

?>

<?php if (!$sub) : ?>
  <div filled=lighter pt42 pb12 style=position:sticky;top:0;margin-bottom:-32px; z fl alic gap=smol+>
    <div filled=darker fl alic jucc style="height:56px;width:56px;" circled>
      <mi><?= $title_w_desc[$category][2] ?></mi>
    </div>

    <div>
      <p text bold mid><?= $title_w_desc[$category][0] ?></p>
    </div>
  </div>
<?php endif; ?>