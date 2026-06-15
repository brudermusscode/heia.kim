import * as Cookie from "./cookies.js";
import * as Audio from "./audio.js";
import * as Page from "./page.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as Responder from "./elements/responder.js";
import * as Settings from "./settings.js";

if (!Settings.LOG) console.log = () => {};

let __type_search_timeout = null;

/**
 * Set default values for recurring ajax settings to avoid needing to set them on any
 * request.
 */
$.ajaxSetup({
  contentType: false,
  processData: false,
  error: function (error) {
    Frontend.ajax_error(error);
  },
});

/**
 * Initialize the application
 */
const init_application = async () => {
  let Route = Page.get_route(window.location.pathname + window.location.search);

  Frontend.init(Route, null, true);

  console.log(
    "%c🌞 Bruder, alles geladen!",
    "color:light-blue;font-size:1.32em;font-weight:800;",
    "\nJustin Seidel ©️ 2022-" + new Date().getFullYear(),
  );

  /**
   * Find cookie notice and show, if available
   */

  // TODO: Build cookie notice banner.
  // TODO: Build feeback dialogue.

  /**
   * Set body attribute for current page. This will apply styles
   * for the current page.
   */
  document.body.setAttribute(Route.key, "");

  setTimeout(() => {
    Frontend.loaded();

    if (typeof Route.execute_once === "function") Route.execute_once();
  }, Settings.INIT_TIMEOUT);
};

export const get_cookie_domain = () => {
  let explode = window.location.hostname.split(".");
  let cookie_domain =
    window.location.hostname === "localhost"
      ? window.location.hostname
      : explode.join(".");

  return cookie_domain;
};

document.addEventListener("DOMContentLoaded", async () => {
  let images = document.querySelectorAll("img");
  let overlay_loading = document.querySelector("overlay[loading-app]");

  /**
   * KEYBOARD SHORTCUT
   */
  document.addEventListener("keyup", (e) => {
    if (!e.key) return;

    /**
     * ? T
     *
     * Test things.
     */
    if (e.key.toLowerCase() === "t") {
      return;
    }

    /**
     * ? ESC
     */
    if (e.key.toLowerCase() === "escape") {
      /**
       * Close ui compose elements
       */
      let reactions = document.find("[reactions-window]");
      let composer = document.find("[composer]");
      if (composer || reactions) Frontend.close_composer();
    }

    /**
     * ? E
     *
     * Begin the editor mode.
     */
    // if (e.key.toLowerCase() === "e") {
    //   if (!__current_user.id) return;

    //   Page.get(`/editor`);
    // }

    /**
     * ? P
     *
     * Go to profile.
     */
    // if (e.key.toLowerCase() === "p") {
    //   if (!__current_user.id) return;

    //   Page.get(`/u/${__current_user.id}`);
    // }
  });

  /**
   * Close all compose ui elements on scroll.
   */
  $(window).on("scroll", function (e) {
    Frontend.close_composer();
  });

  /**
   * Initialize the application.
   */
  await init_application(overlay_loading, images, Settings.INIT_TIMEOUT);

  /**
   * Generalize click event listeners
   */
  document.addEventListener("click", async function (e) {
    let anchor = e.target.closest("a");
    let href;

    /**
     * Close jump menu
     */
    // if (jumper)
    //   jumper.unactivate();

    if (anchor !== null) {
      href = anchor.getAttribute("href");

      if (!anchor.hasAttribute("extern") && href) {
        e.preventDefault();

        await Page.get(href, false, anchor);
      }
    }

    /**
     * Play beatmap audios
     */
    let beatmapset_play = e.target.closest('[data-action="beatmap:set,play"]');
    if (beatmapset_play) Audio.start(beatmapset_play.dataset.setId);

    /**
     * Close all ui components.
     */
    if (
      __current_ui_component &&
      !e.target.closest("ui-component")?.matches("ui-component") &&
      !e.target.closest("[open-ui-component]")?.matches("[open-ui-component]")
    ) {
      Frontend.close_ui_components(true);
    }

    /**
     * Close mode menu on outside click.
     */
    let mode_menu = e.target.closest("mode-menu");
    if (!mode_menu || !mode_menu.matches("mode-menu")) Frontend.close_mode_menu();
  });

  /**
   * Generalize keypress events
   */
  document.addEventListener("keypress", function (e) {
    if (!e.key) return;

    if (e.key.toLowerCase() === "enter") e.preventDefault();
    if (
      e.key.toLowerCase() === "enter" &&
      (e.target.tagName.toLowerCase() === "input" ||
        e.target.tagName.toLowerCase() === "textarea") &&
      e.target.hasAttribute("enter-submitable")
    ) {
      e.preventDefault();

      let form = e.target.closest("form");
      let button = form.querySelector("[submit-closest]");
      if (form && button && !button.hasAttribute("disabled")) button.click();
    }
  });

  /**
   * Generalite scroll event
   */
  document.addEventListener("scroll", (e) => {
    let $scroll_container = document.body.querySelectorAll("[scroll-manipulated]");

    if (!$scroll_container[0]) return;

    if (document.documentElement.scrollTop >= 40 || document.body.scrollTop >= 40) {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", true);
      });
    } else {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", false);
      });
    }
  });

  /**
   * Popstate event (going back in history)
   */
  window.addEventListener("popstate", async (e) => {
    if (!e.state) return;

    Frontend.close_overlays();

    if (e.state.href == undefined || !e.state.href) return;

    await Page.get(e.state.href, true);
  });
});

/**
 * click sound
 */
document.addEventListener("keypress", function () {
  if (__page.is_sounds_enabled) {
    Audio.stop("[click-audio]");
    Audio.play("[click-audio]");
  }
});

/**
 * Creates a button of type submit inside the closest form and
 * triggers a click event on it.
 */
$(document).on("click", "[submit-closest]", function (e) {
  let form = this.closest("form");

  if (!form) return;

  let submit_button = form.querySelector("button[type='submit']");

  if (!submit_button) {
    submit_button = document.createElement("button");
    submit_button.setAttribute("type", "submit");
  }

  form.appendChild(submit_button);
  form.querySelector("button[type='submit']").click();

  if (this.hasAttribute("confirm-submit-button"))
    this.removeAttribute("submit-closest");
});

$(document).on("click", "[confirm-submit-button]", function (e) {
  if (this.hasAttribute("disabled")) return;

  this.setAttribute("submit-closest", "");
});

/**
 * Confirm button
 */
let __submit_confirm_backup_text;
let __submit_confirm_backup_background_value;
let __submit_confirm_is_before_content = false;
Utils.delegate(document, "click", "[confirm]", async function (e) {
  e.preventDefault();

  if (this.hasAttribute("disabled")) return;

  let button_text = this.querySelector(".text");
  let button_before = window.getComputedStyle(button_text, "::before");

  if (!this.hasAttribute("submit-closest")) {
    __submit_confirm_backup_background_value = this.getAttribute("background");

    if (button_before.content != "none") {
      __submit_confirm_is_before_content = true;
      this.setAttribute("sureconfirm", "");
    } else {
      __submit_confirm_backup_text = button_text.innerHTML;
      button_text.innerHTML = "Are you sure?";
      this.setAttribute("color", "white");
    }

    this.setAttribute("submit-closest", "");
    this.setAttribute("background", "red");

    return;
  } else {
    if (__submit_confirm_is_before_content) {
      __submit_confirm_is_before_content = false;
      this.removeAttribute("sureconfirm");
    } else button_text.innerHTML = __submit_confirm_backup_text;

    this.removeAttribute("color");
    this.setAttribute("disabled", "");
    this.setAttribute("background", __submit_confirm_backup_background_value);
    __submit_confirm_backup_text = null;

    this.removeAttribute("submit-closest");

    return;
  }
});

/** Form input/change handler */
Utils.delegate(document, "input", "form", function (e) {
  e.preventDefault();

  let button = this.querySelector("[confirm]");

  if (!button) return;

  let input = e.target;
  let input_backup = input.dataset.backup;

  if (input.value === input_backup) button.setAttribute("disabled", "");
  else button.removeAttribute("disabled");
});

/**
 * Confirm/decline cookies
 */
Utils.delegate(document, "click", '[data-action="cookies"]', async function (e) {
  let decision = this.dataset.decision;

  if (decision === "accept") Cookie.set("COOKIE_CONSENT", true, 365);
  else Cookie.set("COOKIE_CONSENT", false, 365);

  Frontend.remove_header_notice(document.querySelector("[cookie-notice]"));
});

Utils.delegate(
  document,
  "click",
  '[action="privacy:cookie-consent"]',
  async function (e) {
    if (Cookie.get("COOKIE_CONSENT") == "true")
      Cookie.set("COOKIE_CONSENT", false, 14);
    else Cookie.set("COOKIE_CONSENT", true, 365);
  },
);

/**
 * Copy to clipboard
 */
let clipboard_copy = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    new Responder.Responder().add(
      document.body,
      "Copied to clipboard!",
      "success",
      "keyevent",
    );
  });
};

Utils.delegate(document, "click", "[clipboard-copy]", function (e) {
  let tocopy = this.getAttribute("clipboard-copy");
  clipboard_copy(tocopy);
});

/**
 * Scrollable wrapper (horizontal)
 */
Utils.delegate(document, "click", '[data-action="scrollable-wrapper"]', function (e) {
  let direction = this.dataset.direction;
  let $wrapper = this.closest('[data-structure="scrollable-wrapper"]').querySelector(
    ".wrapper-inr",
  );
  let wrapper_max_scroll_width = $wrapper.scrollWidth - $wrapper.clientWidth;

  if (direction === "next") {
    $wrapper.scrollBy({
      left: 228,
      behavior: "smooth",
    });
  } else {
    $wrapper.scrollBy({
      left: -228,
      behavior: "smooth",
    });
  }
});

/**
 * @param {HTMLElement} element
 * @returns void
 */
export const disable = async (element) => {
  return element.setAttribute("disabled", "");
};

/**
 * @param {HTMLElement} element
 * @returns void
 */
export const enable = async (element) => {
  return element.removeAttribute("disabled");
};

export const resize_textareas = () => {
  let textareas = document.find_all("textarea[auto-resize]");

  textareas.forEach((t) => {
    t.style.height = t.scrollHeight;
  });
};

$(function () {
  //

  /**
   * Resize textareas on input
   * @event input
   */
  $(document).on("input", "textarea[auto-resize]", function () {
    this.style.height = "auto";
    this.style.height = "calc(" + this.scrollHeight + "px)";
  });

  /**
   * Open file chooser
   * @event click
   */
  $(document).on("click", "[select-choose-file]", function (e) {
    e.preventDefault();

    if (this.hasAttribute("disabled")) return;

    let form = this.closest("form");
    let input = form.querySelector("input[type=file]");

    console.log(input);

    input.click();
  });

  /**
   * Open file chooser
   */
  // TODO: Find out, why it finds 2x input.
  $(document).on("click", "[input-type=file]", function (e) {
    let input = this.find("input[type=file]");

    if (input) input.click();
  });

  /**
   * Show image preview
   *
   * When selecting an image through the heia.kim image selector in
   * the my section, show a preview of it next to the current image.
   */
  $(document).on("change", "[trigger=update-profile-image]", function (e) {
    e.preventDefault();

    let trigger = document.querySelector("[trigger=update-profile-image]");
    let pictures = document.find_all("picture[update-profile-image]");
    let file = this.files[0];

    if (!trigger || !pictures.length || !file) return;

    pictures.forEach((picture) => {
      let form = this.closest("form");
      let img = picture.find("img");

      if (form) {
        let submit_button = form.find("[submit-closest]");
        let delete_button = form.find('[data-action="users:image,remove"]');

        if (delete_button) delete_button.setAttribute("disabled", "");

        if (submit_button) {
          submit_button.removeAttribute("disabled");
          submit_button.setAttribute("has-tooltip", "");
        }
      }

      img.setAttribute("src", URL.createObjectURL(file));

      let computed_styles = window.getComputedStyle(picture);
      let animation_duration_style =
        computed_styles.getPropertyValue("animation-duration");
      let animation_duration = parseFloat(animation_duration_style.replace("s", ""));
      let milliseconds = animation_duration * 1000;

      if (__page.is_animations_enabled) {
        picture.setAttribute("animation", "changed-zoom");

        setTimeout(() => {
          picture.removeAttribute("animation");
          picture.setAttribute("animation", "pulse");
        }, milliseconds);
      }
    });
  });

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,, JUMP MENU ,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * Close all jump menus if the target is not a jump menu or the
   * button that opens one.
   */
  $(document).on("click", function (e) {
    if (
      !$(e.target).closest("[open-more-menu]").is("[open-more-menu]") &&
      !$(e.target).closest("[menu-more]").is("[menu-more]")
    )
      Frontend.close_jump_menus();
  });

  /**
   * Open a jump menu.
   */
  $(document).on("click", "[open-more-menu]", function (e) {
    let menu = this.closest("[menu-outer]");
    let more = menu.find("[menu-more]");
    let box = this.closest("box-model");

    Frontend.close_jump_menus();

    more.activate();
    this.activate();
    if (box) box.activate();
  });

  /**
   * Close the jump menu when an option in it was clicked.
   */
  $(document).on("click", "[menu-more] .option", function (e) {
    Frontend.close_jump_menus();
  });

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,, REACTIONS WINDOW ,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * Add reaction.
   *
   * @action CREATE
   * @controller ReactionsController
   */
  Utils.delegate(
    document,
    "click",
    '[data-action="reactions:create"] [emoji]',
    function (e) {
      let reactions =
        this.closest("[reactions-window]") || this.closest("[reactions-outer]");
      let reaction = this.dataset.reaction;
      let form = reactions.find("form");

      let id = form.find("input[name=reference_id]").value;
      let reaction_input = form.find("input[name=reaction]");
      let boxes = document.find_all(`[reactions-container][data-id="${id}"]`);
      let add;

      let already_reacted;
      let count = 0;
      let new_count;

      /**
       * Update the reaction input value.
       */
      reaction_input.value = reaction;

      /**
       * Get the FormData object with fresh form data.
       */
      let formdata = new FormData(reactions.find("form"));

      // console.log(box);
      // return console.log(add);

      $.ajax({
        url: "/reaction/create",
        data: formdata,
        method: "POST",
        contentType: false,
        processData: false,
        success: function (data) {
          Frontend.close_composer();

          if (data.status) {
            boxes.forEach((box) => {
              add = box;
              already_reacted = add.find(`[data-reaction="${reaction}"]`);

              /**
               * User reacted already?
               */
              if (already_reacted) {
                count = already_reacted.find("[count]");

                if (already_reacted.hasAttribute("active")) {
                  new_count = parseInt(count.innerHTML) - 1;

                  if (new_count === 0) already_reacted.remove();
                  else {
                    count.innerHTML = new_count;
                    already_reacted.unactivate();
                  }
                } else {
                  new_count = parseInt(count.innerHTML) + 1;
                  count.innerHTML = new_count;
                  already_reacted.activate();
                }
              } else {
                add.insertAdjacentHTML("afterbegin", data.data);
              }
            });
          }
        },
        error: function (data) {
          new Responder.Responder().add(
            document.body,
            data.message,
            "error",
            "users",
          );
        },
      });
    },
  );

  /**
   * Open the reactions window.
   */
  $(document).on("submit", '[data-form="ui:reactions"]', function (e) {
    e.preventDefault();

    if (document.find("[reactions-window]")) return;

    let button = this.find("[submit-closest]");

    const rect = button.getBoundingClientRect();
    const x = rect.left;
    const y = rect.top;

    button.disable();

    $.ajax({
      url: this.dataset.url,
      success: function (data) {
        button.enable();

        if (data.status) {
          document.find("main").insertAdjacentHTML("beforeend", data.data);
          let container = __main.find("[reactions-window]");

          container.style.left = x - container.clientWidth / 2 + "px";
          container.style.top = y + "px";
        } else Frontend.create_responder(data);
      },
    });
  });

  /**
   * Change the language
   */
  $(document).on("click", "[locale]", function (e) {
    let button = this;
    let locale = this.getAttribute("locale");
    let current_url = window.location.href;

    Frontend.load();
    button.disable();

    /**
     * Remove the locale query param, if it exists.
     */
    if (current_url.includes("&lang="))
      current_url = current_url.replace(/&lang=[^&]*/, "");

    /**
     * Redirect the user to the current page with the new locale
     * set as a query param.
     */
    return window.location.replace(current_url.concat(`&lang=${locale}`));
  });

  $(document).on(
    "click",
    "[choose-drop-file] [choose], [choose-drop-file] [change]",
    function (e) {
      let chooser = this.closest("[choose-drop-file]");
      let input = chooser.find("input[type=file]");
      let video = chooser.find("video");

      if (!input) return;

      input.click();

      $(input).on("change", function (e) {
        let file = this.files[0];
        let source = video.find("source") ?? document.createElement("source");

        source.src = URL.createObjectURL(file);

        if (!video.find("source")) {
          video.innerHTML = "";
          video.appendChild(source);
        }

        video.load();

        chooser.activate();
      });
    },
  );

  $(document).on("click", "[choose-drop-file] [remove]", function (e) {
    let chooser = this.closest("[choose-drop-file]");
    let input = chooser.find("input[type=file]");

    chooser.unactivate();
    input.value = "";
  });
});
