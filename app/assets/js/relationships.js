import * as Responder from "./elements/responder.js";
import * as Frontend from "./frontend.js";
import * as Utils from "./utils.js";

/**
 * Create relationship
 *
 * @action CREATE
 * @controller RealationshipsController
 */
$(document).on("click", '[data-action="relationship:create"]', function (e) {
  let user_id = this.dataset.userId;
  let formdata = new FormData();
  let actions = this.closest("[relationship-actions]");
  let button = this;

  formdata.append("user_id", user_id);
  formdata.append("type", "friend");

  button.disable();
  button.setAttribute("animation", "pulse");
  button.set_loading();

  $.ajax({
    url: subst(this.dataset.action),
    data: formdata,
    method: "POST",
    success: function (data) {
      button.enable();
      button.removeAttribute("animation");
      button.unset_loading();

      if (data.status) {
        actions.removeAttribute("refollow");
        actions.removeAttribute("unfollow");
        actions.removeAttribute("follow");
        actions.setAttribute("unfollow", "");
      }

      Frontend.create_responder(data);
    },
  });
});

/**
 * Remove relationship
 *
 * @action DELETE
 * @controller RealationshipsController
 */
$(document).on("click", '[data-action="relationship:delete"]', function (e) {
  let user_id = this.dataset.userId;
  let formdata = new FormData();
  let actions = this.closest("[relationship-actions]");
  let button = this;

  formdata.append("user_id", user_id);
  formdata.append("type", "friend");

  button.disable();
  button.setAttribute("animation", "pulse");
  button.set_loading();

  $.ajax({
    url: subst(this.dataset.action),
    data: formdata,
    method: "POST",
    success: function (data) {
      button.enable();
      button.removeAttribute("animation");
      button.unset_loading();

      if (data.status) {
        actions.removeAttribute("unfollow");

        if (data.data.refollow) actions.setAttribute("refollow", "");
        else actions.setAttribute("follow", "");
      }

      Frontend.create_responder(data);
    },
  });
});
