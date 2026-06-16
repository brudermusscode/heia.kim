import * as Request from "../requests";
import * as Frontend from "../frontend";

document.addEventListener("DOMContentLoaded", () => {
  $(document).on("click", '[data-action="reaction:create"] [emoji]', function (e) {
    let reactions =
      this.closest("[reactions-window]") || this.closest("[reactions-outer]");
    let reaction = this.dataset.reaction;
    let form = reactions.find("form");
    let formdata = new FormData(reactions.find("form"));

    let id = form.find("input[name=reference_id]").value;
    let reaction_input = form.find("input[name=reaction]");
    let boxes = document.find_all(`[reactions-container][data-id="${id}"]`);
    let add;

    let already_reacted;
    let count = 0;
    let new_count;

    // Update the reaction input value.
    reaction_input.value = reaction;

    $.ajax({
      url: Request.url(this),
      data: formdata,
      method: "POST",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.close_composer();

        if (data.status) {
          boxes.forEach((box) => {
            add = box;
            already_reacted = add.find(`[data-reaction="${reaction}"]`);

            /**
             * User reacted already?
             */
            if (already_reacted) {
              count = already_reacted.find("[count]");

              if (already_reacted.hasAttribute("active")) {
                new_count = parseInt(count.innerHTML) - 1;

                if (new_count === 0) already_reacted.remove();
                else {
                  count.innerHTML = new_count;
                  already_reacted.unactivate();
                }
              } else {
                new_count = parseInt(count.innerHTML) + 1;
                count.innerHTML = new_count;
                already_reacted.activate();
              }
            } else {
              add.insertAdjacentHTML("afterbegin", data.data);
            }
          });
        }
      },
      error: function (data) {
        new Responder.Responder().add(document.body, data.message, "error", "users");
      },
    });
  });

  /**
   * Open the reactions window.
   */
  $(document).on("submit", '[data-form="ui:reactions"]', function (e) {
    e.preventDefault();

    if (document.find("[reactions-window]")) return;

    let button = this.find("[submit-closest]");

    const rect = button.getBoundingClientRect();
    const x = rect.left;
    const y = rect.top;

    button.disable();

    $.ajax({
      url: this.dataset.url,
      success: function (data) {
        button.enable();

        if (data.status) {
          document.find("main").insertAdjacentHTML("beforeend", data.data);
          let container = __main.find("[reactions-window]");

          container.style.left = x - container.clientWidth / 2 + "px";
          container.style.top = y + "px";
        } else Frontend.create_responder(data);
      },
    });
  });
});
