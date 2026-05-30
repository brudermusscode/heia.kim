import * as Utils from "../utils.js";
import * as Responder from "../elements/responder.js";
import * as Frontend from "../frontend.js";
import * as Page from "../page.js";
import * as Audio from "../audio.js";
import * as HTMLElements from "../HTMLElements.js";
import Overlay from "../elements/Overlay.js";

/**
 * Remove Image
 *
 * @action DELETE
 * @controller ImagesController
 */
$(document).on("submit", '[data-form="image:delete"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let image = this.closest("ig-object");
  let id = image.dataset.id;

  formdata.append("id", id);

  button.disable();

  $.ajax({
    url: subst(this.dataset.form),
    method: "POST",
    data: formdata,
    success: function (data) {
      if (data.status) {
        image.remove();
      } else {
        button.enable();
      }

      Frontend.create_responder(data);
    },
  });
});

/**
 * Create new user
 *
 * @action CREATE
 * @controller UsersController
 */
$(document).on("submit", '[data-form="user:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let page_overlay = document.find("page-loader");

  Frontend.load();

  $.ajax({
    url: subst(this.dataset.form),
    method: "POST",
    data: formdata,
    success: function (data) {
      // Frontend.unload();

      if (data.status) {
        page_overlay.insertAdjacentHTML("beforeend", HTMLElements.ELEMENT_DONE);

        if (__page.is_sounds_enabled) Audio.play("[bell-downtoup-audio]");

        let animation = page_overlay.querySelector("dotlottie-player");
        animation.addEventListener("complete", () => {
          setTimeout(() => {
            window.location.replace("/download");
          }, 1000);
        });
      } else {
        Frontend.create_responder(data);
      }
    },
  });
});

/**
 * Remove user
 *
 * @action DELETE
 * @controller UsersController
 */
$(document).on("submit", '[data-form="user:delete"]', function (e) {
  e.preventDefault();

  let button = this.find("[submit-closest]");
  let formdata = new FormData(this);

  Frontend.load();
  button.disable();

  $.ajax({
    url: "/user/delete",
    data: formdata,
    method: "POST",
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        Page.reload();
      } else {
        button.enable();
      }

      new Responder.Responder().add(
        document.body,
        data.message,
        data.status ? "success" : "error"
      );
    },
  });
});

/**
 * Open notifications center
 *
 * @action UPDATE
 * @controller SettingsController
 * @namespace User
 */
$(document).on(
  "click",
  '[data-action="user:notifications,check"]',
  function (e) {
    let menu_option = this.closest("[menu-option]");
    let menu_count = this.find("[notification-dot]");
    let center = document.find("[notifications-center]");
    let append = center.find("[append-notifications]");
    let button = this;

    if (this.is_activated()) return Frontend.close_ui_components();

    Frontend.load();

    $.ajax({
      url: "/notifications",
      method: "GET",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          button.activate();
          menu_count.set_not_active();
          center.activate();

          append.innerHTML = data.data;
          Frontend.reload_images();
        }
      },
      error: function (data) {
        Frontend.unload();
      },
    });
  }
);

/**
 * Swipe through categroies.
 *
 * @action UPDATE
 * @namespace User
 * @controller SettingsController
 */
$(document).on(
  "click",
  '[data-action="notifications:category"] [data-category]',
  async function (e) {
    if (this.is_activated()) return;

    let category = this.dataset.category;
    let notifications = this.closest("[notifications]");
    let append = notifications.find("[append-notifications]");
    let buttons = notifications.querySelectorAll("[data-category]");

    buttons.forEach((button) => {
      button.unactivate();
    });

    this.activate();

    Frontend.load();

    $.ajax({
      url: `/notifications?category=${category}`,
      method: "GET",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          append.innerHTML = data.data;
          Frontend.reload_images();
        }
      },
      error: function (data) {
        Frontend.unload();
      },
    });
  }
);

/**
 * ? Pins
 */

/**
 * @action CREATE
 * @namespace User
 * @controller PinsController
 */
$(document).on("submit", '[data-form="user:pin:create"]', function (e) {
  e.preventDefault();

  let form = this;
  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let pinned_container = document.find("[pinned]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: subst(this.data.form),
    data: formdata,
    method: "POST",
    success: function (data) {
      button.enable();
      Frontend.unload();

      if (data.status) {
        form.setAttribute("data-form", "users:pin,remove");
        button.find("mi").innerHTML = "link_off";
        button.find("p").innerHTML = "Unpin from profile";

        if (pinned_container) Page.reload();
      }

      Frontend.create_responder(data);
    },
  });
});

/**
 * @action DELETE
 * @namespace User
 * @controller PinsController
 */
$(document).on("submit", '[data-form="user:pin:remove"]', function (e) {
  e.preventDefault();

  let form = this;
  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let pinned_container = this.closest("[pinned]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/user/pin/remove",
    data: formdata,
    method: "POST",
    success: function (data) {
      button.enable();
      Frontend.unload();

      if (data.status) {
        form.setAttribute("data-form", "users:pin,create");
        button.find("mi").innerHTML = "add_link";
        button.find("p").innerHTML = "Pin to profile";

        if (pinned_container) Page.reload();
      }

      Frontend.create_responder(data);
    },
  });
});
