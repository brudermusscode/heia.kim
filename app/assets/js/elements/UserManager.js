import * as Responder from "../elements/responder";
import * as Utils from "../utils";
import * as Frontend from "../frontend";
import * as Page from "../page";

/**
 *
 * @param {string} page
 */
export const get = (page) => {
  let center = document.find("ui-component[type='user-manager']");
  let react = center?.find("nc-inr");
  let buttons = center.find_all("nc-tab-option");

  center.set_loading();

  buttons.forEach((b) => {
    b.deactivate();
    b.setAttribute("showing", true);
  });

  setTimeout(() => {
    $.ajax({
      url: page,
      method: "GET",
      success: function (data) {
        Frontend.unload();
        center.unset_loading();

        if (data.status) {
          react.innerHTML = data.data;
          Frontend.get_content();
          Frontend.reload_images();

          if (!page.includes("/"))
            center.find_all(`[open="${page}"]`)?.forEach((btn) => btn.activate());

          return;
        }

        Frontend.respond(data);
      },
    });
  }, 100);
};

$(function () {
  //

  /**
   * Toggle category in windowed User Manager.
   * @event click
   */
  $(document).on(
    "click",
    "ui-component[type='user-manager'] [open], [in-user-manager][open]",
    function (e) {
      e.preventDefault();

      let component = document.find("ui-component[type='user-manager']");
      let url = "/user-manager/" + this.getAttribute("open")?.replaceAll(":", "/");

      Frontend.open_ui_component(component, url);
      get(url);
      this.activate();
    },
  );

  /**
   * Create appeal
   *
   * @action CREATE
   * @controller RestrictionAppealsController
   */
  $(document).on("submit", '[data-form="users:appeal,create"]', function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let formdata = new FormData(this);

    Frontend.load();
    button.disable();

    $.ajax({
      url: "/restriction/appeal/create",
      data: formdata,
      method: "POST",
      processData: false,
      contentType: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          Page.reload();
        } else {
          button.enable();
        }

        new Responder.Responder().add(
          document.body,
          data.message,
          data.status ? "success" : "error",
        );
      },
      error: function (data) {
        Frontend.ajax_error(data);
      },
    });
  });

  /**
   * Wipe scores
   *
   * @action DELETE
   * @controller ScoresController
   */
  $(document).on("submit", '[data-form="users:scores,wipe"]', function (e) {
    e.preventDefault();

    let button =
      this.find("[confirm-submit-button]") ?? this.find("[submit-closest]");
    let formdata = new FormData();

    formdata.append("wipe", true);

    button.disable();

    axios.post("/score/remove", formdata).then(async (data) => {
      if (!data.data.status) button.enable();
      else Page.get("/my/game");

      new Responder.Responder().add(
        document.body,
        data.data.message,
        data.data.status ? "success" : "error",
        "settings",
      );
    });
  });

  /**
   * Formats the date timestamp to a fomated timestamp for frontend output.
   */
  const format_date = (date, locale_string, format_options) => {
    return date.toLocaleDateString(locale_string, format_options);
  };

  /**
   * Update privacy settings
   *
   * @action UPDATE
   * @controller UserPrivacyController
   * @namespace User
   */
  Utils.delegate(
    document,
    "submit",
    "[data-form='users:settings,privacy']",
    async function (e) {
      e.preventDefault();

      setTimeout(async () => {
        let formdata = new FormData(this);

        Frontend.load();

        axios.post(`/users/settings/privacy/edit`, formdata).then(async (data) => {
          console.log(data.data);
          Frontend.unload();

          if (!data.data.status)
            new Responder.Responder().add(
              document.body,
              data.data.message,
              "error",
              "settings",
            );
        });
      }, 100);
    },
  );

  /**
   * Update privacy settings
   *
   * @action UPDATE
   * @controller SquadsController
   */
  Utils.delegate(
    document,
    "submit",
    "[data-form='squads:privacy']",
    async function (e) {
      e.preventDefault();

      setTimeout(async () => {
        let formdata = new FormData(this);

        Frontend.load();

        axios.post(`/squads/edit`, formdata).then(async (data) => {
          console.log(data.data);
          Frontend.unload();

          if (!data.data.status)
            new Responder.Responder().add(
              document.body,
              data.data.message,
              "error",
              "settings",
            );
        });
      }, 100);
    },
  );

  Utils.delegate(document, "click", "radio .r__option", function (e) {
    let value = this.dataset.value;
    let radio = this.closest("radio");
    let input = radio.find("input");
    let setter = radio.closest("setter");

    radio.find_all(".r__option").forEach((r) => {
      r.unactivate();
    });

    this.activate();

    if (radio.hasAttribute("mode") && setter) {
      let radio_mod = setter.find("radio[mod]");
      let radio_mod_input = radio_mod.find("input");

      setter.setAttribute("mode", value);

      if (
        (value == "taiko" && radio_mod_input.value == "autopilot") ||
        (value == "ctb" && radio_mod_input.value == "autopilot") ||
        (value == "mania" && radio_mod_input.value == "relax") ||
        (value == "mania" && radio_mod_input.value == "autopilot")
      ) {
        radio_mod.find(".r__option[mod=vanilla]").click();
      }
    }

    if (input) input.value = value;
  });
});
