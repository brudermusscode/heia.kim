import * as App from "../application";
import * as Frontend from "../frontend";
import * as Page from "../page";
import * as Utils from "../utils";
import * as Responder from "../elements/responder";
import Overlay from "../elements/Overlay";

/**
 * Create thread.
 *
 * @action CREATE
 * @controller ThreadsController
 * @namespace Thread
 */
$(document).on("submit", '[data-form="threads:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/thread/create",
    data: formdata,
    method: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        Page.get(`/squad/${data.squad_id}/thread/${data.thread_id}`);
      } else button.enable();

      if (data.has_error || !data.status)
        new Responder.Responder().add(
          document.body,
          data.message,
          data.status ? "success" : "error",
          "squad"
        );
    },
    error: function (data) {
      button.enable();
      Frontend.unload();
      new Responder.Responder().add(
        document.body,
        data.statusText,
        "error",
        "squad"
      );
    },
  });
});

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,, POSTS ,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * Create Post in Thread.
 *
 * @action CREATE
 * @controller ThreadPostsController
 * @namespace Thread
 */
$(document).on("submit", '[data-form="threads:post,create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let threads = document.find("[threads-container]");
  let post_count_container = document.find("[thread-post-count]");
  let post_count;

  if (post_count_container)
    post_count = parseInt(post_count_container.innerHTML);

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/thread/post/create",
    data: formdata,
    method: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        if (threads) {
          threads.removeAttribute("empty-objects");
          threads.insertAdjacentHTML("beforeend", data.data);
        }
        Frontend.close_composer();
        Frontend.reload_images();

        if (post_count_container)
          post_count_container.innerHTML = post_count + 1;
      } else button.enable();

      if (data.has_error || !data.status || !threads)
        new Responder.Responder().add(
          __body,
          data.message,
          data.status ? "success" : "error",
          "squad"
        );
    },
    error: function (data) {
      button.enable();
      Frontend.unload();
      new Responder.Responder().add(__body, data.statusText, "error", "squad");
    },
  });
});
