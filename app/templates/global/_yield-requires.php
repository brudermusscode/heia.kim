<?php

use Heiakim\Application\Cookie;

?>

<!-- Stylesheets -->
<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/charts.css@1.2.0/dist/charts.min.css ">
<link rel="stylesheet" href="/assets/css/fonts.css">
<link rel="stylesheet" href="/assets/css/animate.css">
<link rel="stylesheet" href="/assets/css/normalize.css">

<!-- Javascript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js "></script>
<script src="/assets/js/jquery371.js"></script>
<script src="/assets/js/utility.js"></script>
<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js"
  type="module"></script>

<script>
  let __body = document.body;
  let __main = document.find("main");
  let __search_icon = document.find("[search-icon]");
  let __sign_icon = document.find("[sign-up-icon]");
  let __join_now = document.find("join-now");

  let __project = {
    environment: "<?= _env("ENVIRONMENT"); ?>",
  };

  let __page = {
    current: "<?= filter_input(INPUT_GET, "page", FILTER_SANITIZE_SPECIAL_CHARS); ?>",
    marked: "home",
    is_loading: false,
    is_darkmode: <?= defined("APP") && APP->dark_mode_enabled == 0 ? "0" : "1"; ?>,
    theme: "<?= defined("APP") ? APP->current_theme ?? APP->main_theme : _env("THEME"); ?>",
    is_sounds_enabled: <?= defined("SOUNDS_ENABLED") && SOUNDS_ENABLED == 0 ? "0" : "1"; ?>,
    is_animations_enabled: <?= defined("ANIMATIONS_ENABLED") && ANIMATIONS_ENABLED == 0 ? "0" : "1"; ?>,
    overlay: null,
  };

  __page["params"] = <?= json_encode($_GET) ?>;

  // An object for handling request state.
  let __request = {
    queue: [],
  };

  let __infinite_scroll = {
    page_offset: 1620,
    start: 0,
    limit: 40,
    reached_end: false,
    reached_full_end: false,
  };

  let __current_overlay = null;
  let __current_ui_component = null;
  let __current_second_overlay;
  let __current_audio_element;
  let __current_hover_card;


  let __material_button_ripple_effect_remove_interval = 100;
  let __material_button_ripple_effect_done = false;


  let __submit;
  let __submit_timeout = 0;
  let __submit_timeout_delay = 324;

  let __osu = {
    "beatmap_preview_url": "<?= _env("OSU_BEATMAPS") ?>"
  };

  <?php

  echo "let __get_params = {\n";
  foreach ($get_params ?? [] as $key => $gp)
    echo $key . ": " . '"' . $gp . '",' . "\n";
  echo "};\n\n";

  ?>

  <?php

  $CurrentUser = CurrentUser;

  $name = CurrentUser->name;
  $priv = CurrentUser->priv;
  $policies_consent = CurrentUser->privacy->accepts_policies ?? 0;
  $policies_consent_page = Cookie::get("POLICIES_CONSENT_STEP") ?? "index";
  $legal_lang = defined("LEGAL_LANG") ? LEGAL_LANG : "en";

  echo <<<TEXT
    let __current_user = {
      id: $CurrentUser->id,
      name: "$name",
      priv: $priv,
      legal_lang: "$legal_lang",
      privacy: {
        accepts_policies: $policies_consent,
        policies_consent_page: "$policies_consent_page"
      }
    }
  TEXT;

  ?>
</script>

<?php

/**
 * This file includes the main.bundle.js which can be used around
 * the website as a script file. I bet it's loading faster if it
 * is directly included in the DOM instead of imported through a file.
 *
 * * In dev
 * it's refering to a local script file which
 * will be automatically generated and updated by the node
 * development server.
 *
 * * In production & staging
 * It will include the whol production bundle
 * directly to the DOM.
 */

if (current_env() === "dev") :
  echo '<script type="text/javascript" src="' . _env("NODE_BUNDLE_OUTPUT_PUBLIC_PATH") . 'main.bundle.js"></script>';
else :
  echo "<script defer>";
  include ROOT . "/public/assets/js/main.production.bundle.js";
  echo "</script>";
endif;
