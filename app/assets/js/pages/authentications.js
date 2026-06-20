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
  let redirect = this.getAttribute("redirect");
  let reload = this.hasAttribute("reload");
  let full_reload = this.hasAttribute("full-reload");
  let full_redirect = this.getAttribute("full-redirect");
  let um_open = this.getAttribute("um-open");
  let update_user_references = this.hasAttribute("update-user-references");
  let query = "?";

  query +=
    (redirect ? `redirect=${redirect}&` : "") +
    (full_redirect ? `full-redirect=${full_redirect}&` : "") +
    (um_open ? `um-open=${um_open}&` : "") +
    (reload ? `reload=1&` : "") +
    (full_reload ? `full-reload=1&` : "") +
    (update_user_references ? `update-user-references=1&` : "");

  Frontend.load();

  $.ajax({
    url: Request.url(this),
    method: "POST",
    data: formdata,
    success: function (data) {
      Frontend.unload();
      button.enable();

      if (data.status) return Request.get(`/authentication/${data.data.type}`, query);

      Frontend.respond(data);
    },
  });
});
