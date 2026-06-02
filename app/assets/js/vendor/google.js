import * as Responder from "../elements/responder.js";
import * as Page from "../page.js";
import * as Audio from "../audio.js";
import Overlay from "../elements/Overlay.js";
import * as Utils from "../utils.js";
import * as Frontend from "../frontend.js";
import * as HTMLElements from "../HTMLElements.js";

$(function () {
  /**
   * Create new authentication
   *
   * @action CREATE
   * @controller ConnectDiscordController
   * @namespace Connect
   */
  $(document).on(
    "click",
    '[data-action="vendors:google,auth,create"]',
    function (e) {
      e.preventDefault();

      let button = this;

      button.disable();
      Frontend.load();

      $.ajax({
        url: "/connect/google/auth",
        contentType: false,
        processData: false,
        success: function (data) {
          console.log(data);

          // return console.log(data.url);

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
    }
  );

  /**
   * Remove connection
   *
   * @action REMOVE
   * @controller ConnectDiscordController
   * @namespace Connect
   */
  $(document).on("submit", '[data-form="vendors:google,remove"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let button = this;

    button.disable();
    Frontend.load();

    $.ajax({
      url: "/connect/google/remove",
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        console.log(data);

        if (data.status) Page.reload();
        else {
          Frontend.unload();
          button.enable();
        }

        new Responder.Responder().add(
          document.body,
          data.message,
          data.status ? "success" : "error"
        );
      },
      error: function (data) {
        Frontend.unload();
        button.enable();
      },
    });
  });
});
