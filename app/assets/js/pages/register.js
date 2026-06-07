import * as Page from "../page.js";
import * as Audio from "../audio.js";
import * as Frontend from "../frontend.js";

/**
 * Create a new authentication for a given provider.
 *
 * @action start
 * @controller ConnectionsController
 * @event click
 */
$(document).on("click", "[data-action='connection:start']", function (e) {
  e.preventDefault();
  Frontend.load();

  if (!this.dataset.provider || !this.dataset.callAction) return;

  let formdata = new FormData();
  formdata.append("provider", this.dataset.provider);
  formdata.append("action", this.dataset.callAction);

  $.ajax({
    url: "/connection/start",
    data: formdata,
    method: "POST",
    success: function (data) {
      if (data.status && data.data.link?.includes("https://")) {
        return window.location.replace(data.data.link);
      }

      Frontend.unload();
      Frontend.create_responder(data);
    },
  });
});

/**
 * @action EDIT
 * @controller PasswordResetsController
 */
$(document).on("submit", '[data-form="password-resets:edit"]', function (e) {
  e.preventDefault();

  let data = new FormData(this);
  let button = this.find("[submit-closest]");
  let redirect = this.getAttribute("redirect-to");
  let reload = this.hasAttribute("reload");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/password-reset/update",
    data: data,
    method: "POST",
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        if (__page.is_sounds_enabled) Audio.play("[bell-downtoup-audio]");
        if (redirect) Page.get(redirect);
        else if (reload) Page.reload();
        else Page.get("/login");
      } else {
        if (__page.is_sounds_enabled) Audio.play("[bell-negative-audio]");
        button.enable();
      }

      Frontend.create_responder(data);
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
});
