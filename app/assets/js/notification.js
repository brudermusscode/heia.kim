import * as Frontend from "./frontend.js";

$(function () {
  $(document).on(
    "click",
    "[data-action='notification:category'] [data-category]",
    function (e) {
      let category = this.dataset.category;
      let center = document.find("ui-component[type=notifications]");
      let react = center?.find("nc-inr");
      let button = this;
      let buttons = this.parentElement.find_all("[data-category]");

      if (!center) {
        Frontend.close_ui_components();
        Frontend.create_responder(
          "<strong>Where is your notification-center? 🥲</strong>",
          "error"
        );

        return;
      }

      center.set_loading();

      buttons.forEach((b) => {
        b.deactivate();
        b.setAttribute("showing", true);
      });

      setTimeout(() => {
        $.ajax({
          url: "/notification/category/" + category,
          method: "GET",
          success: function (data) {
            center.unset_loading();

            if (data.status) {
              react.innerHTML = data.data;
              Frontend.reload_images();
              button.activate();
            } else {
              Frontend.create_responder(data);
            }
          },
        });
      }, 100);
    }
  );
});
