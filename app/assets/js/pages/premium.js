import * as Responder from "../elements/responder";
import Overlay from "../elements/Overlay";
import * as App from "../application";
import * as Page from "../page";
import * as Utils from "../utils";
import * as Frontend from "../frontend";
import * as HTMLElements from "../HTMLElements";

let __order_is_done = false;

/**
 * Create order paypal.
 *
 * @action CREATE
 * @controller OrderPayPalController
 * @namespace Order
 */
$(document).on("submit", "[data-form='orders:paypal,create']", async function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let error = false;
  let success = false;
  let canceled = false;

  button.disable();
  Frontend.load();

  const popupOptions = "width=700,height=900,resizable=yes,scrollbars=yes";
  let popup = window.open("", "buyppremiumplus", popupOptions);

  $.ajax({
    url: "/order/paypal/create",
    data: formdata,
    method: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        /**
         * Try multiple times to catch the popup. If it fails,
         * tell the customer and they can try again.
         */
        let tries = 0;
        let interval = setInterval(() => {
          if (popup && !popup.closed) {
            clearInterval(interval);
            popup.location.href = data.links[1].href;
          } else {
            if (!popup.closed) {
              if (tries < 10) {
                echo("trying again...");

                tries++;
              } else {
                clearInterval(interval);

                echo("Too many tries, failing...");

                Page.reload();
                if (popup) popup.close();

                return new Responder.Responder().add(
                  document.body,
                  "<strong>Opening a popup for the request has failed.</strong> Try again!",
                  "error",
                  "orders",
                );
              }
            }
          }
        }, 500);

        /**
         * Popup was caught, so go on with the order.
         */
        let order_id = data.id;

        /**
         * Create a new FormData object to pass to the capture controller.
         */
        formdata = new FormData();
        formdata.append("capture_url", data.links[3].href);
        formdata.append("id", order_id);

        /**
         * Set an interval for capturing the paypal order.
         */

        let capture_interval = setInterval(() => {
          $.ajax({
            url: "/order/paypal/capture",
            data: formdata,
            method: "POST",
            contentType: false,
            processData: false,
            success: function (data) {
              /**
               * Order is finished already?
               */
              if (data.status == 4) {
                clearInterval(capture_interval);
                __order_is_done = true;
                if (popup) popup.close();
                return;
              }

              /**
               * SUCCESS
               */
              if (data.status == 10) {
                success = true;
                if (popup) popup.close();
                new Overlay(HTMLElements.ELEMENT_DONE);
                setTimeout(() => {
                  Page.get("/my/premium");
                }, 2000);
              } else {
                if (data.status == 8 || popup.closed) {
                  error = true;
                  if (popup) popup.close();
                  new Overlay(HTMLElements.ELEMENT_FAIL);
                  setTimeout(() => {
                    Page.reload();
                  }, 2000);
                }

                if (popup.closed) {
                  canceled = true;
                }
              }

              /**
               * On any if catch, return the true order status
               * and show the responder for whether it's
               * successful or not and clear the interval.
               */
              if ((success || canceled || error) && !__order_is_done) {
                clearInterval(capture_interval);

                __order_is_done = true;

                if (!canceled)
                  new Responder.Responder().add(
                    document.body,
                    data.message,
                    data.status ? "success" : "error",
                    "orders",
                  );
              }

              /**
               * If none of the if statements were valid, just
               * return and wait for the next interval.
               */
              return;
            },
            error: function (data) {
              Page.reload();
              return new Responder.Responder().add(
                document.body,
                data.statusText,
                "error",
                "orders",
              );
            },
          });
        }, 1200);
      } else {
        Page.reload();
        if (popup) popup.close();
        new Responder.Responder().add(document.body, data.message, "error", "orders");
      }
    },
    error: function (data) {
      Frontend.unload();
      Page.reload();
      if (popup) popup.close();
      new Responder.Responder().add(
        document.body,
        data.statusText,
        "error",
        "orders",
      );
    },
  });
});

/**
 * Update premium settings. The settings are updated after a short
 * timeout, since the input needs time to change up.
 *
 * @action EDIT
 * @controller SettingsPremiumController
 * @namespace User
 */
$(document).on("submit", "[data-form='users:settings,premium,edit']", function (e) {
  e.preventDefault();

  let button = this.find("[submit-closest]");
  let radio = this.hasAttribute("radio");
  let show_responder = this.hasAttribute("responder");

  clearTimeout(__submit);

  if (!radio && button) button.disable();

  if (this.hasAttribute("delayed")) __submit_timeout = __submit_timeout_delay;

  Frontend.load();

  __submit = setTimeout(() => {
    let form = this;
    let formdata = new FormData(this);

    $.ajax({
      url: "/user/settings/premium/edit",
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();

        if (data.status)
          if (__page.current == "my" || __page.current == "u") {
            Page.reload();
          }

        if (show_responder || !data.status)
          new Responder.Responder().add(
            document.body,
            data.message,
            data.status ? "success" : "error",
            "premium",
          );
      },
      error: function (data) {
        Frontend.unload();
        new Responder.Responder().add(
          document.body,
          data.statusText,
          "error",
          "premium",
        );
      },
    });
  }, __submit_timeout);

  __submit_timeout = 0;
});
