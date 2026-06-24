import * as Page from "./page.js";
import * as Cookie from "./cookies.js";
import * as Settings from "./settings.js";
import * as Frontend from "./frontend.js";
import * as Responder from "./elements/responder.js";

if (!Settings.LOG) console.log = () => {};

$.ajaxSetup({
  contentType: false,
  processData: false,
  error: function (error) {
    Frontend.ajax_error(error);
  },
});

const init_application = async () => {
  console.log(
    "%cJesus loves you! 🌞💌",
    "color:light-blue;font-size:3em;font-weight:800;",
  );

  let Route = Page.get_route(window.location.pathname + window.location.search);

  Frontend.init(Route, null, true);

  // TODO: Build cookie notice banner.
  // TODO: Build feeback dialogue.

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

export const resize_textareas = () => {
  let textareas = document.find_all("textarea[auto-resize]");

  textareas.forEach((t) => {
    t.style.height = t.scrollHeight;
  });
};

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

document.addEventListener("DOMContentLoaded", async () => {
  //

  await init_application();

  window.addEventListener("popstate", async (e) => {
    if (!e.state) return;
    if (e.state.href == undefined || !e.state.href) return;

    await Page.get(e.state.href, true);
  });

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

  let __submit_confirm_backup_text;
  let __submit_confirm_backup_background_value;
  let __submit_confirm_is_before_content = false;

  /**
   * Button that when clicked, asks you if you are sure.
   */
  $(document).on("click", "[confirm]", async function (e) {
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

  $(document).on("input", "form", function (e) {
    e.preventDefault();

    let button = this.querySelector("[confirm]");

    if (!button) return;

    let input = e.target;
    let input_backup = input.dataset.backup;

    if (input.value === input_backup) button.setAttribute("disabled", "");
    else button.removeAttribute("disabled");
  });

  $(document).on("click", "[privacy-cookie-consent]", async function (e) {
    let decision = this.dataset.decision;

    if (decision === "accept") Cookie.set("COOKIE_CONSENT", true, 365);
    else Cookie.set("COOKIE_CONSENT", false, 365);

    Frontend.remove_header_notice(document.querySelector("[cookie-notice]"));
  });

  $(document).on("click", "[clipboard-copy]", function (e) {
    let tocopy = this.getAttribute("clipboard-copy");
    clipboard_copy(tocopy);
  });

  /**
   * @event click
   * @this HTMLElement scroll-wrapper[next]
   */
  $(document).on(
    "click",
    "scroll-wrapper [next], scroll-wrapper [previous]",
    function (e) {
      let direction = this.dataset.direction;
      let $wrapper = this.closest(
        '[data-structure="scrollable-wrapper"]',
      ).querySelector(".wrapper-inr");
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
    },
  );

  $(document).on("input", "textarea[auto-resize]", function () {
    this.style.height = "auto";
    this.style.height = "calc(" + this.scrollHeight + "px)";
  });

  $(document).on("click", "[select-choose-file]", function (e) {
    e.preventDefault();

    if (this.hasAttribute("disabled")) return;

    let form = this.closest("form");
    let input = form.querySelector("input[type=file]");

    input.click();
  });

  $(document).on("click", "[input-type=file]", function (e) {
    let input = this.find("input[type=file]");

    if (input) input.click();
  });

  /**
   * Shows image preview when selecting one through a file input.
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
   * Change the language.
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

  /**
   * @event click
   * @this [choose-drop-file] [choose], [choose-drop-file] [change]
   */
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
