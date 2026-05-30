import * as Responder from "./elements/responder";
import * as Frontend from "./frontend";
import * as Utils from "./utils";
import * as HTMLElements from "./HTMLElements";

/**
 * Create new feedback
 *
 * @action CREATE
 * @controller FeedbackController
 */
$(document).on("click", "[data-action='feedback'] mbutton", function (e) {
  let formdata = new FormData();
  let button = this;

  formdata.append("type", "update");
  formdata.append("action", this.dataset.action);

  button.disable();

  axios.post("/feedback/create", formdata).then((data) => {
    if (data.data.status)
      Frontend.remove_header_notice(this.closest("[feedback-notice]"));
    else {
      button.enable();

      new Responder.Responder().add(
        document.body,
        data.data.message,
        "error",
        "feedback"
      );
    }
  });
});

/**
 * Create new feedback
 *
 * @action CREATE
 * @controller FeedbackController
 */
$(document).on("submit", "[data-form='feedback:create']", function (e) {
  e.preventDefault();

  let formdata = new FormData(this);
  let button = this.querySelector("[submit-closest]");
  let count = this.querySelector("[count]");
  let is_active = button.hasAttribute("active");

  button.disable();

  $.ajax({
    url: "/feedback/create",
    data: formdata,
    method: "POST",
    success: function (data) {
      if (data.status) {
        is_active
          ? button.removeAttribute("active")
          : button.setAttribute("active", "");

        if (is_active) {
          if (count) {
            count.querySelector("p").innerHTML =
              parseInt(count.querySelector("p").innerHTML) - 1;
          }
        } else {
          if (count) {
            count.querySelector("p").innerHTML =
              parseInt(count.querySelector("p").innerHTML) + 1;
          }

          button.setAttribute("clicked", "");

          setTimeout(() => {
            button.removeAttribute("clicked");
          }, 1000);
        }
      }

      button.enable();

      if (!data.status) Frontend.create_responder(data);
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
});

/**
 * Cheer for birthday!
 *
 * @action CREATE
 * @controller FeedbackController
 * @namespace Feedback
 */
$(document).on(
  "submit",
  '[data-form="feedback:birthday,create"]',
  function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let formdata = new FormData(this);
    let count = button.find("[counter]");
    let box = this.closest("box-model");

    Frontend.load();
    button.disable();

    $.ajax({
      url: "/feedback/create",
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        console.log(data);

        Frontend.unload();

        if (data.status) {
          button.disable();
          count.innerHTML = parseInt(count.innerHTML) + 1;
          button
            .closest("[append-animation]")
            .insertAdjacentHTML("beforeend", HTMLElements.ELEMENT_CONFETTI);
          button.setAttribute("cheering", true);
          box.removeAttribute("elevated");
        } else {
          button.enable();
        }
      },
      error: function (data) {
        Frontend.unload();
        button.enable();
        new Responder.Responder().add(document.body, data.message, "error");
      },
    });
  }
);
