import * as Responder from "./elements/responder.js";
import * as Frontend from "./frontend.js";

/**
 * Create report.
 *
 * @action CREATE
 * @controller ReportsController
 */
$(document).on("submit", '[data-form="reports:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/report/create",
    data: formdata,
    method: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        Frontend.close_overlays();
      } else button.enable();

      new Responder.Responder().add(
        document.body,
        data.message,
        data.status ? "success" : "error",
        "report"
      );
    },
    error: function (data) {
      button.enable();
      Frontend.unload();
      new Responder.Responder().add(
        document.body,
        data.statusText,
        "error",
        "report"
      );
    },
  });
});
