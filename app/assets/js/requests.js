import * as Frontend from "./frontend";
import * as Page from "./page";
import * as Audio from "./audio";
import Overlay from "./elements/Overlay";
import * as UserManager from "./elements/UserManager";

/**
 * Loads content into an Overlay.
 *
 * @param {string} url
 * @param {string} query
 */
export const get = (url, query) => {
  let timeout = 0;

  // Remove a current overlay.
  if (__page.overlay) {
    __page.overlay.delete();
    timeout = 100;
  }

  Frontend.load();

  setTimeout(() => {
    $.ajax({
      url: url + (query ? query : ""),
      method: "GET",
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          new Overlay(data.data);
        } else Frontend.ajax_response("error");
      },
    });
  }, timeout);
};

/**
 * Create a new ajax request. This will push the request to the global active request
 * array and make it available throughout the website.
 */
export const queue = (options) => {
  const xhr = $.ajax(options);
  __request.queue.push(xhr);

  // Clean up finished request.
  xhr.always(() => {
    __request.queue = __request.queue.filter((req) => req !== xhr);
  });
};

/**
 * @param {HTMLElement} element
 * @param {string} baseUrl
 * @param {string} prefix
 * @returns string
 */
const construct_get_request_url = (
  element,
  baseUrl,
  prefix = "request-get-attribute-",
) => {
  // Initialize query string
  let queryString = "?";

  // Iterate over attributes of the element
  for (const attr of element.attributes) {
    if (attr.name.startsWith(prefix)) {
      // Extract the attribute name without the prefix
      const queryParamName = attr.name
        .slice(prefix.length)
        .replace(/([A-Z])/g, "-$1")
        .replace("-", "_")
        .toLowerCase();
      queryString += `${queryParamName}=${encodeURIComponent(attr.value)}&`;
    }
  }

  // Remove trailing '&' if present
  queryString = queryString.slice(0, -1);

  // Construct full URL
  const fullUrl = baseUrl + queryString;

  return fullUrl;
};

/**
 * Starts a request.
 *
 * @param {HTMLElement} element
 */
export const request = (element) => {
  let method = element.getAttribute("method") ?? "GET";
  let formdata = new FormData();
  let query = "?";
  let close_tab = element.getAttribute("close-tab");
  let redirect_from_data = element.getAttribute("redirect-from-data");
  let redirect_on_error = element.getAttribute("redirect-on-error");
  let responder = element.getAttribute("responder");
  let audio_success = element.getAttribute("audio-success");
  let audio_error = element.getAttribute("audio-error");
  let on_success = element.getAttribute("on-success");

  for (let date in element.dataset) {
    if (date === "action" || date === "method") continue;
    let kebab_cased_key = date.replace(/[A-Z]/g, (date) => "_" + date.toLowerCase());

    formdata.append(kebab_cased_key, element.dataset[date]);
    query += `${kebab_cased_key}=${element.dataset[date]}&`;
  }

  query = query.slice(0, -1);

  $.ajax({
    url: url(element) + (method === "GET" ? query : ""),
    method: method,
    data: method === "POST" ? formdata : null,
    success: function (data) {
      // When a responder should always be shown.
      if (responder !== null && responder === "") Frontend.create_responder(data);

      // ? Error.
      if (!data.status) {
        // When a responder should only show on error.
        if (responder === "error") Frontend.create_responder(data);

        // Play success audio!
        if (audio_error !== null) Audio.play(`[${audio_error}]`);

        if (redirect_on_error) return Page.get(redirect_on_error);

        if (close_tab === "error") window.close();
      }

      // ? Success
      else {
        // Execute JavaScript from attribute.
        if (on_success !== null) $.globalEval(on_success);

        // Play success audio!
        if (audio_success !== null) Audio.play(`[${audio_success}]`);

        // When a responder should only be shown on success.
        if (responder === "success") Frontend.create_responder(data);

        // Redirect the user from a link in data object.
        if (redirect_from_data !== null && data.data?.redirect) {
          redirect_from_data === "full"
            ? window.location.replace(data.data.redirect)
            : Page.get(data.data.redirect);
        }

        // Close the current tab.
        if (close_tab === "success" || close_tab !== null) window.close();
      }
    },
  });
};

/**
 * Either pass an element that has a data-action attribute or a string which both
 * will be transformed to a valid request url.
 */
export const url = (string) => {
  let action = string.dataset.action ?? string.getAttribute("action");
  let url = action ? action : string;

  return "/" + url.replaceAll(":", "/");
};

/**
 * Transforms a snake-case string to kebab-case.
 *
 * @param {object} dataset
 * @return {string}
 */
const kebab_case = (str) => {
  return str.replace(/[A-Z]/g, (letter) => "_" + letter.toLowerCase());
};

/**
 * Any attribute that can be attached to a form to manipulate the
 * behaviour of submitting a form.
 *
 * @var array
 */
const SUBMIT_FORM_ATTRIBUTES = [
  "request",
  "request-do",
  "submit-closest",
  "method",
  "responder",
  "no-scroll-top",
  "toggle-button-active",
  // Will open the given page in the user-manager.
  "um-open",
  "redirect",
  "full-redirect",
  "reload",
  "full-reload",
  "close-overlay",
  "on-success",
  "update-library",
  "update-current-track",
  "interchange-action",
  "delete-object",
  "reload-object",
];

$(function () {
  //

  /**
   * Shadow submitting a form. It mimics the functionality of form[request="…"] when
   * submitted, but as a button, where the dataset entries are being converted to hid-
   * den inputs.
   *
   * @event click
   * @this [request]
   */
  $(document).on("click", "[request], [request-do]", function () {
    if (!this.closest("[shadow-submit]")) return;

    let form = document.createElement("form");
    let button = document.createElement("mbutton");

    button.setAttribute("submit-closest", true);
    form.prepend(button);

    // Create an input inside the form for every dataset entry.
    for (const [key, value] of Object.entries(this.dataset)) {
      form.insertAdjacentHTML(
        "afterbegin",
        `<input type=hidden name=${kebab_case(key)} value="${value}" />`,
      );
    }

    // Append all attributes from this element to the form.
    for (const attribute of this.attributes) {
      if (!SUBMIT_FORM_ATTRIBUTES.includes(attribute.name)) continue;

      form.setAttribute(attribute.name, attribute.value);
    }

    document.body.prepend(form);
    $(form).submit();
    form.remove();
  });

  /**
   * Submitting a form with attribute [request].
   *
   * @event submit
   * @this form[request]
   */
  $(document).on("submit", "[request], [request-do]", function (e) {
    e.preventDefault();

    let request_url = this.getAttribute("request") || this.getAttribute("request-do");

    if (!request_url) return;

    let delay = this.getAttribute("delay") ?? 0;

    setTimeout(() => {
      let formdata = new FormData(this);
      let buttons = this.find_all("[submit-closest]");
      let method = this.getAttribute("method") ?? "POST";
      let responder = this.getAttribute("responder");
      let audio_success = this.getAttribute("audio-success");
      let audio_error = this.getAttribute("audio-error");
      let open = this.getAttribute("um-open");
      let redirect = this.getAttribute("redirect");
      let full_redirect = this.getAttribute("full-redirect");
      let reload = this.getAttribute("reload");
      let full_reload = this.getAttribute("full-reload");
      let scroll_top = !this.hasAttribute("no-scroll-top");
      let execute_success = this.getAttribute("on-success");
      let close_overlay = this.getAttribute("close-overlay");
      let update_user_references = this.hasAttribute("update-user-references");
      let delete_object = this.getAttribute("delete-object");
      let reload_object = this.getAttribute("reload-object");

      request_url = request_url.replaceAll(":", "/");

      buttons.forEach((button) => button.disable());

      if (this.getAttribute("no-loader") == null) Frontend.load();

      if (redirect) {
        let split_redirect_url = redirect.split("/");

        // Build a new redirect url by substituting the colon parameter with actual
        // values from the submitted form.
        split_redirect_url.forEach((section, index) => {
          if (section[0] === ":") {
            let param = section.replace(":", "");
            let value = formdata.get(param);

            redirect = redirect.replace(section, value);
          }
        });
      }

      $.ajax({
        url: "/" + request_url,
        data: formdata,
        method: method,
        success: function (data) {
          Frontend.unload();

          if (data.status) {
            let object = delete_object ?? reload_object ?? null;
            let element = object
              ? document.find(
                  `[${object.toLowerCase()}][data-id="` +
                    data.data[object]?.id +
                    `"]`,
                )
              : null;

            if (delete_object && element) {
              element.remove();
            }

            if (reload_object && element) {
              element.outerHTML = data.data.HTML;
            }

            if (update_user_references) Frontend.update_user_menu();

            if (close_overlay !== null) Frontend.close_current_overlay();

            if (audio_success !== null) Audio.play(`[${audio_success}]`);

            if (open !== null) {
              UserManager.get(open);
            }

            if (full_redirect !== null)
              window.location.replace(
                full_redirect ??
                  redirect ??
                  window.location.pathname + window.location.search,
              );

            if (full_reload !== null)
              window.location.replace(
                window.location.pathname + window.location.search,
              );

            if (redirect !== null) Page.get(redirect);

            if (reload !== null) Page.reload();

            if (responder === "success") Frontend.respond(data);

            if (execute_success) $.globalEval(execute_success);
          } else {
            if (audio_error !== null) Audio.play(`[${audio_error}]`);

            if (responder === "error") Frontend.respond(data.message, "error");
          }

          if (responder !== null && (responder === "always" || !responder))
            Frontend.respond(data);

          if (responder === "simple")
            Frontend.ajax_response(data.status ? "success" : "error");

          buttons.forEach((button) => button.enable());

          Frontend.reload_images();
        },
      });
    }, delay);
  });

  /**
   * Open popups dynamically.
   *
   * @event click
   * @this HTMLElement [request-get]
   */
  $(document).on("click", "[request-get]", function (e) {
    let href = this.getAttribute("request-get");
    let url = "/" + href.replaceAll(":", "/");
    let query = "?";
    let dataset_count = Object.keys(this.dataset).length;

    /**
     * Construct the url query by iterating through all data
     * elements on the clicked element.
     */
    if (dataset_count > 0) {
      for (const key in this.dataset) {
        query += kebab_case(key) + "=" + this.dataset[key] + "&";
      }
    }

    query += "is_popup=kurwa";

    get(url, query);
  });
});
