import * as Frontend from "../frontend.js";

$(function () {
  /**
   * Start the authentication to connect a new service.
   *
   * @action START
   * @controller ConnectController
   * @namespace Connect
   */
  $(document).on("click", '[data-action="connect:start"]', function (e) {
    e.preventDefault();

    let formdata = new FormData();
    let button = this;
    let type = this.dataset.type;

    formdata.append("type", type);

    button.disable();
    Frontend.load();

    $.ajax({
      url: subst(this.dataset.action),
      data: formdata,
      method: "POST",
      success: function (data) {
        if (data.status) {
          window.location.replace(data.data);
        } else {
          Frontend.unload();
          button.enable();
          Frontend.create_responder(data);
        }
      },
    });
  });
});
