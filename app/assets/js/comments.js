import * as Frontend from "./frontend";
import * as Responder from "./elements/responder";
import * as Utils from "./utils";
import * as Page from "./page";

/**
 * Open the comments composer.
 */
$(document).on("submit", '[data-form="comments:create,view"]', function (e) {
  e.preventDefault();

  let composer = document.find("[composer]");

  if (composer) return;

  let formdata = new FormData(this);
  let search = new URLSearchParams(formdata);
  let url = "/comment/create?" + search;

  Page.get_component(__main, url);
});

/**
 * Create comment
 *
 * @action CREATE
 * @controller CommentsController
 */
$(document).on("submit", '[data-form="comments:create"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let react;
  let timeline;
  let comment;
  let composer = document.find("[composer]");

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/comment/create",
    data: formdata,
    method: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.status) {
        Frontend.close_overlays();
        react = document.find(
          `[data-react="comments:create"][data-id="${data.id ?? 0}"]`
        );

        if (react) {
          react.insertAdjacentHTML("beforeend", data.data);
          timeline = react.closest(".timeline-object");

          if (timeline)
            timeline.find(".timeline-object--icon").removeAttribute("dno");
        }

        Frontend.reload_images();
        Frontend.close_composer();
      } else {
        button.enable();
        new Responder.Responder().add(
          document.body,
          data.message,
          "error",
          "comments"
        );
      }
    },
    error: function (data) {
      button.enable();
      Frontend.unload();
      new Responder.Responder().add(
        document.body,
        data.statusText,
        "error",
        "comments"
      );
    },
  });
});

/**
 * Delete a comment
 *
 * @action DELETE
 * @controller CommentsController
 */
Utils.delegate(
  document,
  "submit",
  '[data-form="comments:remove"]',
  function (e) {
    e.preventDefault();

    let formdata = new FormData();
    let button = this.find("[submit-closest]");
    let id = this.closest("box-model")?.dataset.id ?? 0;
    let comment = this.closest("[comment]");

    formdata.append("id", id);

    Frontend.load();
    button.disable();

    $.ajax({
      url: "/comment/remove",
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          comment.remove();
        } else {
          button.enable();
          new Responder.Responder().add(
            document.body,
            data.message,
            "error",
            "comments"
          );
        }
      },
      error: function (data) {
        button.enable();
        Frontend.unload();
        Frontend.ajax_error(data);
      },
    });
  }
);

/**
 * Fetch more comments.
 */
$(document).on("submit", '[data-form="comments:fetch"]', function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.find("[submit-closest]");
  let limit = this.find("input[name=limit]").value;
  let offset = this.find("input[name=offset]");
  let react;

  button.disable();
  Frontend.load();

  $.ajax({
    url: "/comment/fetch",
    data: formdata,
    type: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      Frontend.unload();

      if (data.end) {
        if (button.find("mi")) button.find("mi").innerHTML = "done";
        button.setAttribute("background", "slight-green");
        button.setAttribute("color", "dark-green");
        button.removeAttribute("submit-closest");
        button.removeAttribute("filled");
      } else {
        button.enable();
        offset.value = parseInt(offset.value) + parseInt(limit);
      }

      if (data.status) {
        Frontend.close_overlays();
        react = document.find(
          `[data-more="comments"][data-id="${data.id ?? 0}"]`
        );
        react.insertAdjacentHTML("beforeend", data.data);
        Frontend.reload_images();
      } else {
        new Responder.Responder().add(
          document.body,
          data.message,
          "error",
          "comments"
        );
      }
    },
    error: function (data) {
      button.enable();
      Frontend.unload();
      new Responder.Responder().add(
        document.body,
        data.statusText,
        "error",
        "comments"
      );
    },
  });
});
