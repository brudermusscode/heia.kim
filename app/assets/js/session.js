import * as Responder from "./elements/responder.js";
import * as Page from "./page.js";
import * as Audio from "./audio.js";
import Overlay from "./elements/Overlay.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as HTMLElements from "./HTMLElements.js";

/**
 * Login
 *
 * @action CREATE
 * @controller SessionsController
 */
$(document).on("submit", '[data-form="session:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let animation;
  let button = this.find("mbutton[submit-closest]");
  let page_overlay = document.find("page-loader");

  Frontend.load();

  $.ajax({
    url: "/session/create",
    data: formdata,
    method: "POST",
    success: function (data) {
      if (data.status) {
        if (__page.is_sounds_enabled) Audio.play("[bell-downtoup-audio]");

        __current_user.id = data.data.user.id;

        page_overlay.insertAdjacentHTML("beforeend", HTMLElements.ELEMENT_DONE);

        animation = page_overlay.querySelector("dotlottie-wc");
        animation.addEventListener("complete", () => {
          if (data.data.user.priv < 2) window.location.replace("/download");
          else window.location.replace("/home");
        });
      } else {
        if (__page.is_sounds_enabled) Audio.play("[bell-negative-audio]");

        Frontend.unload();
        Frontend.close_overlays();
      }
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
});

/**
 * Destroy session
 *
 * @action DELETE
 * @controller SessionsController
 */
$(document).on("submit", '[data-form="session:delete"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/session/delete",
    data: formdata,
    method: "POST",
    success: async function (data) {
      if (data.status) {
        if (data.data.current_session) {
          if (__page.is_sounds_enabled)
            await Audio.play("[bell-uptodown-audio]");
          setTimeout(() => {
            window.location.replace("/home");
            Frontend.unload();
          }, 1200);
        } else {
          Frontend.unload();
          Page.reload();
        }
      } else button.enable();

      new Responder.Responder().add(
        document.body,
        data.message,
        data.status ? "success" : "error"
      );
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
});
