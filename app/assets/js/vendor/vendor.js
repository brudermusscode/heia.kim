import * as Responder from "../elements/responder.js";
import * as Page from "../page.js";
import * as Audio from "../audio.js";
import Overlay from "../elements/Overlay.js";
import * as Utils from "../utils.js";
import * as Frontend from "../frontend.js";
import * as HTMLElements from "../HTMLElements.js";

$(function () {
  /**
   * Create new user
   *
   * @action CREATE
   * @controller ConnectUsersController
   * @namespace Connect
   */
  $(document).on("submit", '[data-form="vendors:users,create"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let button = this;
    let overlay = new Overlay(false);

    button.disable();
    Frontend.load();

    $.ajax({
      url: "/connect/user/create",
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: async function (data) {
        console.log(data);

        Frontend.unload();

        if (data.status) {
          overlay.append(HTMLElements.ELEMENT_DONE);

          if (__page.is_sounds_enabled)
            await Audio.play("[bell-downtoup-audio]");

          setTimeout(() => {
            window.location.replace("/download");
          }, 3000);
        } else {
          button.enable();
          overlay.delete();

          if (__page.is_sounds_enabled)
            await Audio.play("[bell-negative-audio]");

          new Responder.Responder().add(document.body, data.message, "error");
        }
      },
      error: function (data) {
        Frontend.unload();
        button.enable();
        overlay.delete();
      },
    });
  });

  /**
   * Create new login auth
   *
   * @action CREATE
   * @namespace Connect
   */
  $(document).on("click", '[data-action="vendors:login"]', function (e) {
    e.preventDefault();

    let button = this;
    let formdata = new FormData();
    let vendor = this.dataset.vendor;

    formdata.append("return_uri", "login");

    button.disable();
    Frontend.load();

    $.ajax({
      url: `/connect/${vendor}/auth`,
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        console.log(data);

        if (data.status) window.location.replace(data.url);
        else {
          Frontend.unload();
          button.enable();

          new Responder.Responder().add(document.body, data.message, "error");
        }
      },
      error: function (data) {
        Frontend.unload();
        button.enable();
      },
    });
  });
});
