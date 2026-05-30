import * as Frontend from "../frontend";

$(function () {
  $(document).on("submit", '[data-form="squad:post:create"]', function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let formdata = new FormData(this);
    let inner = this.closest("pm-inr");
    let close_overlay =
      inner?.closest("[posting-machine-overlay]").find("close") ||
      document.find("overlay [close-overlay]");
    let type = inner?.getAttribute("post-type");
    let input_wrappers = inner?.find_all(`[post-type-input]`);
    let posts = document.find("[posts]");
    let responder = this.getAttribute("responder");

    input_wrappers?.forEach((wrapper) => {
      if (wrapper.getAttribute("post-type-input") !== type) {
        wrapper.find_all("input").forEach((input) => {
          input.disabled = true;
        });
        wrapper.find_all("textarea").forEach((textarea) => {
          textarea.disabled = true;
        });
      }
    });

    button.disable();
    Frontend.load();

    $.ajax({
      url: subst(this.dataset.form),
      data: formdata,
      method: "POST",
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          posts?.insertAdjacentHTML("afterbegin", data.data);
          Frontend.reload_images();
          Frontend.close_overlays();
        } else {
          button.enable();
        }

        Frontend.create_dynamic_responder(data, responder);
      },
    });
  });

  $(document).on("click", "[create-post]", function (e) {
    let type = this.getAttribute("create-post");
    let inner = this.closest("pm-inr");
    let type_input = inner.find("input[type=hidden][name=type]");

    if (!type_input)
      return console.log("Could not find input[hidden][name=type]");

    type_input.value = type;
    inner.setAttribute("post-type", type);
    inner.find(`[post-type-input=${type}] [autofocus]`)?.focus();

    let input_wrappers = inner.find_all(`[post-type-input]`);

    input_wrappers.forEach((wrapper) => {
      wrapper.find_all("input").forEach((input) => {
        input.disabled = false;
      });
      wrapper.find_all("textarea").forEach((textarea) => {
        textarea.disabled = false;
      });
    });
  });

  $(document).on(
    "click",
    "[posting-machine-overlay] [post-type-input=poll] [add-option]",
    function (e) {
      let parent = this.closest("[post-type-input=poll]");
      let options = parent.find("[poll-options]");
      let options_length = options.find_all("[p-option]").length;
      let option = options.find("[p-option]");
      let clone, clone_input;

      if (options_length + 1 === 2) {
        options.find("[p-option] [delete-option]").enable();
      }

      clone = option.cloneNode(true);
      clone_input = clone.find("input[type=text]");
      clone_input.value = "";
      clone_input.setAttribute("placeholder", "Another option!");
      clone.focus();
      options.appendChild(clone);
    }
  );

  $(document).on(
    "click",
    "[posting-machine-overlay] [post-type-input=poll] [delete-option]",
    function (e) {
      let options = this.closest("[poll-options]");
      let options_length = options.find_all("[p-option]").length;
      let parent = this.closest("[p-option]");

      if (options.length < 2) return;

      options_length = options_length - 1;
      parent.remove();

      if (options_length === 1) {
        options.find("[p-option] [delete-option]").disable();
      }
    }
  );

  $(document).on("click", "[posting-machine-overlay] close", function (e) {
    let overlay = this.closest("[posting-machine-overlay]");

    overlay.removeAttribute("animation");
    overlay.style.opacity = 0;

    setTimeout(() => {
      overlay.remove();
    }, 200);
  });

  /**
   * Focus the textarea of the composer when clicking the composer anywhere.
   */
  $(document).on("click", "[composer]", function (e) {
    let textarea = this.querySelector("textarea");

    this.setAttribute("active", "");

    textarea.focus();
  });
});
