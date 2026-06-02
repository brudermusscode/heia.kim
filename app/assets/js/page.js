import * as Router from "./router.js";
import * as Page from "./page.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as Cookies from "./cookies.js";
import * as Responder from "./elements/responder.js";
import * as Request from "./requests.js";
import * as MaterialButton from "./elements/mbutton.js";
import * as Settings from "./settings.js";
import Overlay from "./elements/Overlay.js";

/**
 * Page settings
 */
let scroll_interval;

/**
 * Reloads the page by calling the get function and setting the
 * reload parameter to true.
 */
export const reload = (keep_overlays = false) => {
  get(window.location.pathname + window.location.search, false, null, false, true, keep_overlays);
};

/**
 *
 * @param {string} url
 * @returns Router
 */
export const get_route = (url) => {
  let query_params_split = url.split("?");
  let url_no_query_params = query_params_split[0];
  let url_parameter = url_no_query_params.split("/");

  url_parameter.shift();

  /**
   * Mark home if none.
   */
  if (url_parameter[0] === "") url_parameter[0] = "home";

  let route = url_parameter[0];

  return Router.router(route);
};

/**
 *
 * @param {string} href
 * @param {boolean} state
 * @param {?string} anchor
 * @param {boolean} scroll_top
 * @param {boolean} reload
 * @param {boolean} keep_overlays
 * @returns void
 */
export const get = async (href, state = false, anchor = null, scroll_top = true, reload = false, keep_overlays = false) => {
  /**
   * If the page is in loading state, return.
   */
  if (__page.is_loading && !reload) return;
  if (__page.current === "maintenance") return;

  /**
   * Cancel all queued requests.
   */
  __request.queue.forEach((req) => req.abort());
  __request.queue = [];

  let url = href;

  let main_container = document.body.querySelector("main");
  let title;

  /**
   * Serialize the route the user is coming from.
   */
  let coming_from_path = window.location.pathname;
  let coming_from_path_split = coming_from_path.split("/");

  /**
   * Prepare a reload.
   */
  if (reload) url = url.concat(url.includes("?") ? "&reload=" : "?reload=") + random_string(8);

  /**
   * Get the current route.
   */
  let Route = await get_route(url);

  /**
   * Create the object for the history.
   */
  let history_params = {};
  history_params["href"] = url;

  /**
   * Check if the current location is the same as the
   * requested url. Return nothing in this case. Defines a variable
   * is_same_location to use around this function as well as a
   * variable that just figures out if the current location
   * and the destinated one are from the same oruigin page
   */
  let current_location_route = window.location.pathname.split("/").shift()[0];
  let is_same_location = window.location.pathname.concat(window.location.search) == url;
  let is_same_history_route = url == current_location_route;

  /**
   * Return nothing if the current location is the same as the
   * requested one.
   */
  if (!state && is_same_location) return;

  /**
   * Create a new overlay only when the app is being initialized.
   * Otherwise there would be two overlays lingering.
   */
  if (document.find("[loading-app]")) new Overlay(null, false);

  /**
   * Begin the new page load.
   */
  Frontend.close_ui_components();
  Frontend.load(1000);

  /**
   * Scroll to the top if the param is set to true.
   */
  if (scroll_top) Frontend.scroll_to_top();

  /**
   * Profile Editor is only available on Desktop by now. Redirect
   * users that try to access it through a small device.
   */
  if (Route.key == "editor" && (window.innerHeight < 400 || window.innerWidth < 1000)) {
    Frontend.unload();

    console.log("Profile Editor only available on Desktop by now.");

    if (window.location.pathname.split("/")[1] !== "editor") {
      Frontend.open_popup("/ui/unavailable?type=profileeditor");
    } else get("/home");
    return;
  }

  /**
   * Get CSRF token.
   */
  const __csrf_token = document.find("head meta[name=csrf_token]")?.getAttribute("content");

  $.ajax({
    url: url,
    method: "GET",
    success: async function (data) {
      /**
       * Close overlays if.
       */
      if (!keep_overlays) Frontend.close_overlays();

      /**
       * Scroll to the top.
       */
      if (scroll_top && !state) window.scrollTo(0, 0);

      /**
       * Update the page global.
       */
      __page.current = Route.key;
      __page.marked = Route.mark ? Route.mark : Route.key;

      /**
       * Update main content.
       */
      main_container.innerHTML = data;

      /**
       * Extract the title.
       * @var string
       */
      title = main_container.find("title")?.innerHTML;

      /**
       * If a redirect exists, redirect to the page inside the to attribute.
       */
      await redirect();

      // ! Make better -----------------------------------------------------------
      // Fire a request for any <request>-element.
      main_container.find_all("request")?.forEach((elem) => Request.request(elem));

      // ! Please. -----------------------------------------------------------------

      // Pushes the coming state to the browser history and sets a proper
      // title to the document.
      if (!state && !reload) {
        history.pushState(history_params, title, url);
        document.title = title !== undefined && title !== null && title ? title : "Unknown Page";
      }

      clearInterval(scroll_interval);

      /**
       * Stop playing audio.
       */
      if (__current_audio_element) {
        __current_audio_element.pause();
        __current_audio_element.remove();
        __current_audio_element = null;
      }

      /**
       * For each page load, we need to reset the infinite scrolling variables
       * to let the new page, if available, can access those freshly and calculate
       * when to add new items
       */
      __infinite_scroll.start = 0;
      __infinite_scroll.reached_end = false;
      __infinite_scroll.reached_full_end = false;

      /**
       * Set header to scrolled.
       */
      if (window.scrollY >= 20) document.find("[scroll-manipulated]")?.setAttribute("scrolled", "true");

      /**
       * Check for an exception and move it to a direct child of
       * the body to be present in the very foreground.
       */
      Frontend.extract_exception(main_container);

      /**
       * Floating actions are UI elements that appear above the
       * main content and mostly stay fixed in place to get the
       * user to fire of certain actions like lgoin/sign-up. The
       * toggle for showing or hiding is handled in this function.
       */
      Frontend.toggle_floating_actions(Route.key);

      /**
       * Toggle disguised frontend visuals when set.
       */
      Frontend.disguise(Route, is_same_history_route);

      /**
       * Autofocus any input that has the attribute.
       */
      if (document.find("[autofocus]")) document.find("[autofocus]").focus();

      /**
       * Execute once function will only be fired when first
       * accessing a new page, which is determined by the body
       * carrying an attribute named after the route itself.
       */
      if (typeof Route.execute_once === "function" && !document.body.hasAttribute(Route.key)) Route.execute_once(url);

      // TODO: Implement execute() for always firing functions.

      /**
       * Remove all route attributes from any section where it will be
       * added to.
       */
      Object.keys(Router.routes).forEach((index) => {
        document.body.removeAttribute(index);
      });

      /**
       * Set route attributes.
       */
      document.body.setAttribute(Route.key == "" ? "home" : Route.body_attribute !== undefined ? Route.body_attribute : Route.key, "");

      /**
       * Free the clicking on other links by disabling page loading.
       */
      Frontend.unload();

      /**
       * Update the user menu on any reload.
       */
      if (reload) Frontend.update_user_menu();

      /**
       * Load dynamic content.
       */
      Frontend.get_content();

      /**
       * Eval all script tags inside the newly fetched content.
       */
      main_container.find_all("script").forEach((script) => {
        eval(script.innerHTML);
      });

      /**
       * Deactivate all main navigation buttons.
       */
      document.find_all("[page]").forEach((button) => {
        button.unactivate();
      });

      /**
       * Set any navigation button carrying the [page] attribute
       * with the name of the currently processed page to active.
       */
      if (Route.mark) document.find_all(`[page="${Route.mark}"]`)?.forEach((button) => button.activate());
      else document.find_all(`[page="${Route.key}"]`)?.forEach((button) => button.activate());

      // Set the current page to be marked.
      // __page.marked = Route.mark ? Route.mark : route;

      // The previous Router, basically the router for the page
      // the user is coming from.
      let PreviousRoute = Router.router(coming_from_path_split[1]);

      /**
       * Set the body to initialized.
       */
      document.body.setAttribute("initialized", true);

      /**
       * Slide in page navigation buttons.
       */
      let page_navigator = main_container.find("page-navigator");

      if ((Route.is_main_page && PreviousRoute.is_main_page) || Route.key === coming_from_path_split[1])
        Frontend.just_show_navigation(page_navigator);
      else
        setTimeout(() => {
          Frontend.slide_in_navigation(page_navigator);
        }, Settings.PAGE_NAVIGATOR_SHOW_DELAY);

      /**
       * Reinitialize all material buttons.
       */
      MaterialButton.init();

      /**
       * Reload all images.
       */
      Frontend.reload_images();

      return true;
    },
  });
};

/**
 * Finds a redirect element on the page, reads the to attribute
 * and opens this link in the current tab.
 * @returns void
 */
export const redirect = async () => {
  new Promise((resolve, reject) => {
    let main_container = document.find("main");
    let redirector = main_container.find("redirect");
    let delay = redirector?.getAttribute("delay");

    if (redirector && redirector.hasAttribute("to"))
      setTimeout(() => {
        return window.location.replace(redirector.getAttribute("to"));
      }, delay ?? 0);

    resolve(1);
  });
};

export const get_component = async (react, url, data = null, empty_container = false, where = "top") => {
  $.ajax({
    url: url,
    data: data,
    method: "GET",
    processData: false,
    contentType: "JSON",
    success: function (data) {
      if (!data.status) return new Responder.Responder().add(document.body, data.message, "error");

      if (empty_container) {
        react.innerHTML = data.data;
      } else {
        if (where === "top") react.insertAdjacentHTML("afterbegin", data.data);
        else react.insertAdjacentHTML("beforeend", data.data);
      }

      Frontend.reload_images();
    },
    error: function (data) {
      new Responder.Responder().add(document.body, data.message, "error", "user");
    },
  });
};

export const objectify_query_params = (query) => {
  if (!query) return;

  query = query.replace("?", "");

  let params = query.split("&");

  if (!params[0] && params.length > 0) params.shift();

  let obj = {};

  params.forEach((param) => {
    let ass = param.split("=");
    obj[ass[0]] = ass[1];
  });

  return obj;
};

export const disable_scroll = () => {
  let TopScroll = window.pageY || document.documentElement.scrollTop;
  let LeftScroll = window.pageX || document.documentElement.scrollLeft;

  window.onscroll = function () {
    window.scrollTo(LeftScroll, TopScroll);
  };
};

export const enable_scroll = () => {
  window.onscroll = function () {};
};

Utils.delegate(document, "click", "[data-action='legal:consent,forward']", function (e) {
  let page = this.dataset.legalPage;

  Cookies.set("POLICIES_CONSENT_STEP", page, 365);
});

/**
 * Loads the policies consent page up, if the user was
 * registered before the new data privacy policy has been implemented.
 * Will always load it up, if the user did not give positive
 * consent and reloads the page.
 */
export const load_policies_consent_page = (accepts_policies = false) => {
  if (accepts_policies == 1) return false;

  if (__current_user.id && !__current_user.privacy.accepts_policies && !window.location.pathname.includes("consent")) {
    let consent_page = __current_user.privacy.policies_consent_page == "index" ? "" : "/" + __current_user.privacy.policies_consent_page;

    get(`/legal/consent${consent_page}`);

    return true;
  }

  return false;
};

Utils.delegate(document, "click", '[data-action="legal:consent,decide"] mbutton', function (e) {
  let action = this.dataset.letAction;
  let formdata = new FormData();
  let accepts_policies = action == "decline" ? 0 : 1;

  formdata.append("accepts_policies", accepts_policies);

  axios.post("/users/settings/privacy/edit", formdata).then((data) => {
    Page.get("/home");

    Cookies.remove("POLICIES_CONSENT_STEP");
    Cookies.set("POLICIES_CONSENT", true, 365);
    __current_user.privacy.accepts_policies = 1;

    new Responder.Responder().add(document.body, data.data.message, data.data.status ? "success" : "error", "privacy");
  });
});

export const random_string = (length) => {
  const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let randomString = "";

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    randomString += characters.charAt(randomIndex);
  }

  return randomString;
};

/**
 * Create an install app prompt.
 */
$(function () {
  window.addEventListener("beforeinstallprompt", (event) => {
    // Prevent Chrome 76 and later from showing the automatic install prompt
    event.preventDefault();

    // Store the event for later use
    let deferredPrompt = event;

    // Show your own custom install button
    // For example, you can have an "Install" button on your webpage
    const installButton = document.find("installButton");

    if (installButton) {
      installButton.style.display = "block";

      installButton?.addEventListener("click", () => {
        // Trigger the deferred prompt
        deferredPrompt.prompt();

        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === "accepted") {
            console.log("User accepted the install prompt");
          } else {
            console.log("User dismissed the install prompt");
          }

          // Reset the deferred prompt
          deferredPrompt = null;
          installButton.style.display = "none";
        });
      });
    }
  });
});
