import * as Frontend from "./frontend";
import * as Page from "./page";
import * as Audio from "./audio";
import Overlay from "./elements/Overlay";

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
 * @param {string} href
 * @param {Element} append_to
 * @param {boolean} show_responder
 * @param {boolean} overlay
 * @returns {}
 */
const get_content = async (href, append_to, show_responder, overlay) => {
  if (__page.is_loading) return;

  Frontend.load();

  return new Promise((resolve, reject) => {
    $.ajax({
      url: href,
      method: "GET",
      contentType: false,
      processData: false,
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          if (!data.data) return resolve(data);

          if (show_responder !== undefined && show_responder === "success")
            Frontend.create_responder(data.message, "succes");

          if (overlay) {
            let overlay = new Overlay();
            overlay.append(data.data);
            overlay.overlay.find("[autofocus]")?.focus();
          } else if (append_to) {
            append_to.insertAdjacentHTML("beforeend", data.data);
            append_to.find("[autofocus]")?.focus();
          } else {
            __main.insertAdjacentHTML("beforeend", data.data);
            __main.find("[autofocus]")?.focus();
          }
          Frontend.reload_images();
        } else {
          if (show_responder !== undefined && show_responder === "error")
            Frontend.create_responder(data.message, "error");
        }

        if (show_responder !== undefined && show_responder === "always")
          Frontend.create_responder(data.message, data.status ? "success" : "error");

        resolve(data);
      },
      error: function (data) {
        Frontend.ajax_error(data);
        reject(data);
      },
    });
  });
};

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
  let redirect_from_data = element.hasAttribute("redirect-from-data");
  let redirect_on_error = element.getAttribute("redirect-on-error");
  let responder = element.getAttribute("responder");

  for (let date in element.dataset) {
    if (date === "action" || date === "method") continue;

    formdata.append(date, element.dataset[date]);
    query += `${date}=${element.dataset[date]}&`;
  }

  query = query.slice(0, -1);

  $.ajax({
    url: url(element) + (method === "GET" ? query : ""),
    method: method,
    data: method === "POST" ? formdata : null,
    success: function (data) {
      // When a responder should always be shown.
      if (responder !== null && responder === "") Frontend.create_responder(data);

      // When a responder should only show on error.
      if (responder === "error" && !data.status) Frontend.create_responder(data);

      // If an error happened and the redirect on error is set.
      if (!data.status && redirect_on_error) {
        return Page.get(redirect_on_error);
      }

      if (data.status) {
        // When a responder should only be shown on success.
        if (responder === "success") Frontend.create_responder(data);

        // Redirect the user from a link in data object.
        if (redirect_from_data && data.data.redirect) {
          Page.get(data.data.redirect);
        }
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
  "redirect",
  "full-redirect",
  "reload",
  "full-reload",
  "close-overlays",
  "on-success",
  "update-library",
  "update-current-track",
  "interchange-action",
];

$(function () {
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

    /**
     * Create an input inside the form for every dataset entry.
     */
    for (const [key, value] of Object.entries(this.dataset)) {
      form.insertAdjacentHTML(
        "afterbegin",
        `<input type=hidden name=${key} value="${value}" />`,
      );
    }

    /**
     * Append all attributes from this element to the form.
     */
    for (const attribute of this.attributes) {
      if (!SUBMIT_FORM_ATTRIBUTES.includes(attribute.name)) continue;

      form.setAttribute(attribute.name, attribute.value);
    }

    document.body.prepend(form);

    // # Submit the form!
    button.click();

    form.remove();

    return;
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
      let redirect = this.getAttribute("redirect");
      let scroll_top = !this.hasAttribute("no-scroll-top");
      let reload = this.getAttribute("reload");
      let full_reload = this.getAttribute("full-reload");
      let execute_success = this.getAttribute("on-success");
      let close_overlays = this.getAttribute("close-overlays");
      let update_user_references = this.hasAttribute("update-user-references");

      request_url = request_url.replaceAll(":", "/");

      buttons.forEach((button) => button.disable());

      if (this.getAttribute("no-loader") == null) Frontend.load();

      if (redirect) {
        let split_redirect_url = redirect.split("/");

        /**
         * Build a new redirect url by substituting the colon
         * parameter with actual values from the submitted form.
         */
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
            /**
             * Update anything that could have changed for the
             * user in the ui through this request.
             */
            if (update_user_references) Frontend.update_user_menu();

            /**
             * Close all overlays requested.
             */
            if (close_overlays !== null) Frontend.close_overlays();

            /**
             * Play success audio.
             */
            if (audio_success !== null) Audio.play(`[${audio_success}]`);

            /**
             * Page reload requested.
             */
            if (reload !== null) {
              Page.reload();
            }

            /**
             * Redirect requested.
             */
            if (redirect !== null && full_reload === null) {
              Page.get(redirect, false, null, scroll_top);

              /**
               * Full reload requested.
               */
            } else if (full_reload !== null)
              window.location.replace(
                redirect ?? window.location.pathname + window.location.search,
              );

            /**
             * Show responder only on success.
             */
            if (responder !== null && responder === "success")
              Frontend.create_responder(data);

            /**
             * Execute on success functions.
             */
            if (execute_success) $.globalEval(execute_success);
          } else {
            /**
             * Play audio on error.
             */
            if (audio_error !== null) Audio.play(`[${audio_error}]`);

            /**
             * Show responder only on error.
             */
            if (responder !== null && responder === "error")
              Frontend.create_responder(data.message, "error");
          }

          /**
           * Always show responder.
           */
          if (responder !== null && (responder === "always" || !responder))
            Frontend.create_responder(data);

          buttons.forEach((button) => button.enable());
        },
      });
    }, delay);
  });

  /**
   * Open popups dynamically.
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
        query +=
          key.replace(/[A-Z]/g, (letter) => "_" + letter.toLowerCase()) +
          "=" +
          this.dataset[key] +
          "&";
      }

      query += "is_popup=kurwa";
    } else query += "is_popup=kurwa";

    Frontend.load(1000);

    $.ajax({
      url: url + query,
      method: "GET",
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          let overlay = new Overlay();
          overlay.append(data.data);

          setTimeout(() => {
            overlay.overlay.find("[autofocus]")?.focus();
          }, 400);
        } else new Frontend.create_responder(data.message, "error");
      },
    });
  });

  /**
   * Get content from some url and append it to the main container.
   */
  $(document).on("click", "[request-get-old]", async function (e) {
    let append_to = eval(this.getAttribute("request-append-to"));
    let show_responder = this.getAttribute("responder");
    let url = "/" + this.getAttribute("request-get");
    let overlay = this.hasAttribute("overlay");

    if (append_to !== null && !(append_to instanceof Element)) return;

    url = construct_get_request_url(this, url, "request-get-attribute-");

    try {
      let resolved = await get_content(url, append_to, show_responder, overlay);

      if ((resolved.status && !resolved.data) || resolved.end === true) {
        this.setAttribute("done", "");
        this.disable();
      }

      if (!resolved.data) {
        if (resolved.error || resolved.message)
          create_responder(resolved.error || resolved.message);

        return;
      }
    } catch (error) {
      return create_responder(`An error occured: ${error}`, "error");
    }

    // If offset and limit are set as attributes, we want to
    // increase the offset by the limit. It's probably always
    // fetching new data.
    let limit = this.getAttribute("request-get-attribute-limit");
    let offset = this.getAttribute("request-get-attribute-offset");

    if (limit && offset) {
      this.setAttribute(
        "request-get-attribute-offset",
        parseInt(offset) + parseInt(limit),
      );
    }
  });
});
