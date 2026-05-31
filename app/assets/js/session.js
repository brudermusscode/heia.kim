import * as Responder from "./elements/responder.js";
import * as Page from "./page.js";
import * as Audio from "./audio.js";
import Overlay from "./elements/Overlay.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as HTMLElements from "./HTMLElements.js";

/**
 * Create a new Session.
 *
 * @action CREATE
 * @controller SessionsController
 * @event submit
 */
$(document).on("submit", '[data-form="session:create"]', function (e) {
  e.preventDefault();

  let animation;
  let button = this.find("mbutton[submit-closest]");
  let page_overlay = document.find("page-loader");

  Frontend.load();

  $.ajax({
    url: "/session/create",
    data: new FormData(this),
    method: "POST",
    success: function (data) {
      if (__page.is_sounds_enabled)
        data.status
          ? Audio.play("[bell-downtoup-audio]")
          : Audio.play("[bell-negative-audio]");

      if (data.status) {
        setTimeout(() => {
          window.location.replace("/home");
        }, 1200);

        return;
      }

      Frontend.unload();
      Frontend.close_overlays();
    },
  });
});

/**
 * Destroy session.
 *
 * @action DELETE
 * @controller SessionsController
 * @event submit
 */
$(document).on("submit", '[data-form="session:delete"]', function (e) {
  e.preventDefault();

  let button = this.find("[submit-closest]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/session/delete",
    data: new FormData(this),
    method: "POST",
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        // Deleted session is not the current one, just reload
        // the page.
        if (!data.data.is_current_session) Page.reload();
        else {
          if (__page.is_sounds_enabled) Audio.play("[bell-uptodown-audio]");

          setTimeout(() => {
            window.location.replace("/home");
          }, 1200);
        }

        return;
      }

      Frontend.create_responder(data);
      button.enable();
    },
  });
});
