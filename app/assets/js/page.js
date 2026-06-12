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
  get(
    window.location.pathname + window.location.search,
    false,
    null,
    false,
    true,
    keep_overlays,
  );
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
export const get = async (
  href,
  state = false,
  anchor = null,
  scroll_top = true,
  reload = false,
  keep_overlays = false,
) => {
  if (__page.is_loading && !reload) return;
  if (__page.current === "maintenance") return;

  // Abort all queued ajax requests.
  __request.queue.forEach((req) => req.abort());
  __request.queue = [];

  const __csrf_token = document
    .find("head meta[name=csrf_token]")
    ?.getAttribute("content");
  let url = href;
  let main_container = document.body.querySelector("main");
  let title;
  let coming_from_path = window.location.pathname;
  let coming_from_path_split = coming_from_path.split("/");

  // Prepare a reload if set.
  if (reload)
    url = url.concat(url.includes("?") ? "&reload=" : "?reload=") + random_string(8);

  /**
   * @var object
   */
  let Route = await get_route(url);

  let history_params = {};
  history_params["href"] = url;

  // Compare the new loaction with the previous.
  let current_location_route = window.location.pathname.split("/").shift()[0];
  let is_same_location =
    window.location.pathname.concat(window.location.search) == url;
  let is_same_history_route = url == current_location_route;

  // Return early with nothing when it's the same page.
  if (!state && is_same_location) return;

  // Set frontend state.
  Frontend.close_ui_components();
  Frontend.load(1000);
  Frontend.scroll_to_top();

  // Profile Editor is only available on Desktop by now. Redirect users that try to
  // access it through a small device.
  if (
    Route.key == "editor" &&
    (window.innerHeight < 400 || window.innerWidth < 1000)
  ) {
    Frontend.unload();

    if (window.location.pathname.split("/")[1] !== "editor") {
      Request.get("/ui/unavailable?type=profileeditor");
    } else get("/home");

    return;
  }

  $.ajax({
    url: url,
    method: "GET",
    success: async function (data) {
      //

      // Close all overlays if not set different.
      if (!keep_overlays) Frontend.close_overlays();

      // Scroll to the top only when not going back in history.
      // if (scroll_top && !state) window.scrollTo(0, 0);

      // Update __page global.
      __page.current = Route.key;
      __page.marked = Route.mark ? Route.mark : Route.key;

      // Insert the new content to the <main>.
      main_container.innerHTML = data;

      // Extract the title.
      title = main_container.find("title")?.innerHTML;

      // Fire of any redirect that could be found inside the document tree.
      await redirect();

      // Fire a request for any <request>-element.
      main_container.find_all("request")?.forEach((elem) => Request.request(elem));

      // Pushes the coming state to the browser history and sets a proper title to
      // the document.
      if (!state && !reload) {
        history.pushState(history_params, title, url);
        document.title =
          title !== undefined && title !== null && title ? title : "Unknown Page";
      }

      clearInterval(scroll_interval);

      // Stop playing any audio and reset the global.
      if (__current_audio_element) {
        __current_audio_element.pause();
        __current_audio_element.remove();
        __current_audio_element = null;
      }

      // For each page load, we need to reset the infinite scrolling variables to let
      // the new page, if available, can access those freshly and calculate when to
      // add new items
      __infinite_scroll.start = 0;
      __infinite_scroll.reached_end = false;
      __infinite_scroll.reached_full_end = false;

      // Set the header to scroll manipulated if scroll top is higher than a given
      // amount.
      if (window.scrollY >= 20)
        document.find("[scroll-manipulated]")?.setAttribute("scrolled", "true");

      // Set frontend state.
      Frontend.unload();
      Frontend.extract_exception(main_container);
      Frontend.toggle_floating_actions(Route.key);
      Frontend.disguise(Route, is_same_history_route);
      Frontend.get_content();
      Frontend.update_user_menu();
      Frontend.reload_images();

      // Find [autofocus] and focus it.
      if (document.find("[autofocus]")) document.find("[autofocus]").focus();

      // If set, execute a function only once in a Route main key.
      if (
        typeof Route.execute_once === "function" &&
        !document.body.hasAttribute(Route.key)
      )
        Route.execute_once(url);

      // If set, execute a function on any page load.
      if (typeof Route.execute_always === "function") Route.execute_always(url);

      // Remove the Router key from body and set a new one based on the new page.
      Object.keys(Router.routes).forEach((index) => {
        document.body.removeAttribute(index);
      });

      document.body.setAttribute(
        Route.key == ""
          ? "home"
          : Route.body_attribute !== undefined
            ? Route.body_attribute
            : Route.key,
        "",
      );

      // Find all <script> elements and fire off their scripts -
      main_container.find_all("script").forEach((script) => {
        eval(script.innerHTML);
      });

      // Deactivates all [page] elements which are links/buttons to different pages
      // and in the following, find the one that has been clicked and any that is e-
      // qual to the one clicked..
      document.find_all("[page]").forEach((button) => {
        button.unactivate();
      });

      if (Route.mark)
        document
          .find_all(`[page="${Route.mark}"]`)
          ?.forEach((button) => button.activate());
      else
        document
          .find_all(`[page="${Route.key}"]`)
          ?.forEach((button) => button.activate());

      let PreviousRoute = Router.router(coming_from_path_split[1]);

      // Body can be set to initialized here!
      document.body.setAttribute("initialized", true);

      let page_navigator = main_container.find("page-navigator");

      // Based on where we came from, we want to either just show the page navigator
      // without any animations, or slide in when visiting a new main model page like
      // squads
      if (
        (Route.is_main_page && PreviousRoute.is_main_page) ||
        Route.key === coming_from_path_split[1]
      )
        Frontend.just_show_navigation(page_navigator);
      else
        setTimeout(() => {
          Frontend.slide_in_navigation(page_navigator);
        }, Settings.PAGE_NAVIGATOR_SHOW_DELAY);

      // Reinitialize all material buttons for a sleek animation when clicked.
      MaterialButton.init();

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

export const get_component = async (
  react,
  url,
  data = null,
  empty_container = false,
  where = "top",
) => {
  $.ajax({
    url: url,
    data: data,
    method: "GET",
    processData: false,
    contentType: "JSON",
    success: function (data) {
      if (!data.status)
        return new Responder.Responder().add(document.body, data.message, "error");

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

Utils.delegate(
  document,
  "click",
  "[data-action='legal:consent,forward']",
  function (e) {
    let page = this.dataset.legalPage;

    Cookies.set("POLICIES_CONSENT_STEP", page, 365);
  },
);

/**
 * Loads the policies consent page up, if the user was
 * registered before the new data privacy policy has been implemented.
 * Will always load it up, if the user did not give positive
 * consent and reloads the page.
 */
export const load_policies_consent_page = (accepts_policies = false) => {
  if (accepts_policies == 1) return false;

  if (
    __current_user.id &&
    !__current_user.privacy.accepts_policies &&
    !window.location.pathname.includes("consent")
  ) {
    let consent_page =
      __current_user.privacy.policies_consent_page == "index"
        ? ""
        : "/" + __current_user.privacy.policies_consent_page;

    get(`/legal/consent${consent_page}`);

    return true;
  }

  return false;
};

Utils.delegate(
  document,
  "click",
  '[data-action="legal:consent,decide"] mbutton',
  function (e) {
    let action = this.dataset.letAction;
    let formdata = new FormData();
    let accepts_policies = action == "decline" ? 0 : 1;

    formdata.append("accepts_policies", accepts_policies);

    axios.post("/users/settings/privacy/edit", formdata).then((data) => {
      Page.get("/home");

      Cookies.remove("POLICIES_CONSENT_STEP");
      Cookies.set("POLICIES_CONSENT", true, 365);
      __current_user.privacy.accepts_policies = 1;

      new Responder.Responder().add(
        document.body,
        data.data.message,
        data.data.status ? "success" : "error",
        "privacy",
      );
    });
  },
);

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
