import * as Responder from "../elements/responder.js";
import * as Frontend from "../frontend.js";
import * as Page from "../page.js";

/**
 * @action CREATE
 * @controller AuthenticationsController
 */
$(document).on("submit", '[data-form="authentication:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this;
  let redirect = this.dataset.redirect;
  let type = this.dataset.type;
  let update_user_references = this.hasAttribute("update-user-references");

  formdata.append("type", type);
  Frontend.load();

  $.ajax({
    url: "/authentication/create",
    method: "POST",
    data: formdata,
    success: function (data) {
      Frontend.unload();
      button.enable();

      if (data.status) {
        if (type !== "user:create")
          Frontend.open_popup(
            `/authentication/${type}` +
              (redirect ? `?redirect=${redirect}` : "") +
              (update_user_references
                ? (redirect ? "&" : "?") + `update-user-references=1`
                : ""),
            false
          );
        else Frontend.create_responder(data);
      } else {
        Frontend.create_responder(data);
      }
    },
  });
});

/**
 * Validate
 *
 * @action VALIDATE
 * @controller AuthenticationsController
 */
$(document).on(
  "submit",
  '[data-form="authentications:validate"]',
  function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let button = this;

    button.disable();

    $.ajax({
      url: "/authentication/validate",
      method: "POST",
      data: formdata,
      processData: false,
      contentType: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          let type = formdata.get("type");

          if (type == "delete_user")
            setTimeout(() => {
              window.location.replace("/");
            }, 3000);
          else if (type == "wipe_user") Page.get("/my/game");
        } else {
          button.enable();
        }

        new Responder.Responder().add(
          document.body,
          data.message,
          data.status ? "success" : "error"
        );
      },
      error: function (data) {
        Frontend.ajax_error(data);
      },
    });
  }
);

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
