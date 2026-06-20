import * as Page from "../page.js";
import * as Request from "../requests.js";
import * as Frontend from "../frontend.js";

/**
 * @action CREATE
 * @controller AuthenticationsController
 */
$(document).on("submit", '[data-action="authentication:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this;
  let redirect = this.dataset.redirect;
  let update_user_references = this.hasAttribute("update-user-references");

  Frontend.load();

  $.ajax({
    url: Request.url(this),
    method: "POST",
    data: formdata,
    success: function (data) {
      Frontend.unload();
      button.enable();

      if (data.status)
        return Request.get(
          `/authentication/${data.data.type}` +
            (redirect ? `?redirect=${redirect}` : "") +
            (update_user_references
              ? (redirect ? "&" : "?") + `update-user-references=1`
              : ""),
          false,
        );

      Frontend.respond(data);
    },
  });
});

/**
 * Delete authentication
 *
 * @action DELETE
 * @controller UsersController
 */
$(document).on("click", '[data-action="authentication:remove"]', function (e) {
  let formdata = new FormData();
  let button = this;

  formdata.append("token", this.dataset.token);
  button.disable();

  $.ajax({
    url: "/authentication/remove",
    method: "POST",
    dataType: "JSON",
    data: formdata,
    processData: false,
    contentType: false,
    success: function (data) {
      Page.get("/login");
    },
    error: function () {
      Page.get("/login");
    },
  });
});
