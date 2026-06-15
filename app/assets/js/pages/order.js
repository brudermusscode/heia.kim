import * as Request from "../requests";
import * as Frontend from "../frontend";
import { sleep } from "../utils";

/**
 * Delete an Order.
 *
 * @param {string} id
 */
const _delete = (id, silent = true) => {
  let data = new FormData();
  data.append("order_id", id);

  $.ajax({
    url: "/order/delete",
    data: data,
    method: "POST",
    success: function (data) {
      if (silent) return;
    },
  });
};

/**
 * Create an Order.
 *
 * @action CREATE
 * @controller OrdersController
 * @event submit
 * @this HTMLElement <form data-action="order:create">
 */
$(document).on("submit", "[data-action='order:create']", async function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let order_success = false;

  button.disable();
  Frontend.load();
  __page.overlay.lock();

  const popupOptions = "popup,width=500,height=700,resizable=yes,scrollbars=yes";
  const popup = window.open("/loading", "paypal-order", popupOptions);

  if (!popup) {
    Frontend.respond("Could not do what you wanted, try again!", "error");
    Frontend.reload();

    return;
  }

  // Listen for the popup to send a message with type "success", so we know the order
  // is done.
  window.addEventListener("message", function (event) {
    if (event.origin !== window.location.origin) return;
    if (event.data.type !== "success") return;

    order_success = true;
  });

  $.ajax({
    url: Request.url(this),
    data: formdata,
    method: "POST",
    success: async function (data) {
      if (data.status) {
        //

        popup.location.href = data.data.link;

        // When one capturing is still being processed, I don't want to fire a new
        // capture request. So just capture if the order_capture_process is null.
        let order_capture_process = null;

        // Capture the Order.
        let __capture_order_interval = setInterval(() => {
          // Unlock everything to create a new order if the popup is closed without a
          // success event message coming from the popup window.
          if (popup?.closed && !order_success) {
            clearInterval(__capture_order_interval);
            Frontend.unload();
            button.enable();
            __page.overlay?.unlock();
            _delete(data.data.Order.order_id);

            return;
          }

          if (!order_capture_process) {
            $.ajax({
              url: `/order/capture?order_id=${data.data.Order.order_id}`,
              method: "GET",
              beforeSend: function () {
                order_capture_process = 1;
              },
              success: function (ddata) {
                order_capture_process = null;

                // When no order has been found.
                if (!ddata.status && ddata.message === "no_order") {
                  clearInterval(__capture_order_interval);
                  Frontend.respond("Something went wrong! Try agin.");
                  Frontend.reload();
                }

                // Success!
                if (ddata.status) {
                  clearInterval(__capture_order_interval);
                  Frontend.respond(ddata);

                  new Audio(
                    "https://github.com/brudermusscode/heia.kim-cdn/raw/refs/heads/master/sounds/Nyanpasu.mp3",
                  ).play();

                  setTimeout(() => {
                    Frontend.reload();
                    // Page.get("/my");
                  }, 1000);
                }
              },
            });
          }
        }, 1000);

        return;
      }

      // Will only be reached if an error happened.
      __page.overlay?.unlock();
      popup?.close();
      Frontend.unload();
      Frontend.create_responder(data);
    },
  });
});
