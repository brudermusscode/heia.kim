import * as Page from "./page.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as Window from "./window.js";
import * as Responder from "./elements/responder.js";
import * as Setting from "./settings.js";
import * as MaterialButton from "./elements/mbutton.js";
import * as Request from "./requests.js";
import Overlay from "./elements/Overlay.js";

let Cookie = require("js-cookie");

/**
 * Sets the frontend to be loading.
 */
export const loaded = () => {
  let overlay = document.find("overlay[loading-app]");

  overlay?.setAttribute("visible", false);

  setTimeout(() => {
    overlay?.remove();
  }, Setting.ANIMATION_TIME);

  document.body.setAttribute("toggled", false);
  document.body.setAttribute("initialized", true);
};

let __frontend_load_overlay_timeout = 0;

/**
 * Sets the frontend to be loading.
 */
export const load = (show_overlay_delay = 0) => {
  __page.is_loading = true;

  /**
   * Show the circular loader around the logo old twitter style ~
   */
  document.body.querySelectorAll("[main-logo]").forEach((l) => {
    l.setAttribute("loading", "");
  });

  /**
   * Show the page-loader.
   */
  __frontend_load_overlay_timeout = setTimeout(() => {
    document.find("page-loader")?.setAttribute("visible", true);
  }, show_overlay_delay);
};

/**
 * Unsets the loading state of the frontend.
 */
export const unload = () => {
  clearTimeout(__frontend_load_overlay_timeout);

  __page.is_loading = false;

  setTimeout(() => {
    /**
     * Hide the circular loader around the logo.
     */
    document.body.querySelectorAll("[main-logo]").forEach((l) => {
      l.removeAttribute("loading");
    });

    /**
     * Hide the page-loader.
     */
    document.find("page-loader")?.setAttribute("visible", false);
  }, Setting.INIT_TIMEOUT);
};

/**
 * Some pages should be fullscreen and look different from the
 * rest of the design language used. This function modifies all
 * elements that should appear/disappear or just look different.
 *
 * @param { Object } Route - The Router object.
 * @return { void }
 */
export const disguise = (Route, history_route_is_equal) => {
  /**
   * Set the disguised background color if.
   */
  let disguised = document.find("disguised");
  if (!Route.disguised && !disguised) document.body.removeAttribute("someone-else");
  else document.body.setAttribute("someone-else", true);

  /**
   * If a new page is called, different styles to header and
   * sub header can be set through this section.
   */
  // let header = document.find("page-navigator");
  // let __toggle_header = document.find("toggle-header");
  // let show_header = document.find("show-header");

  if (!history_route_is_equal) {
    //  || __toggle_header
    // if ((Route.hide_header && !show_header) || __toggle_header) {
    //   header.setAttribute("toggled", true);
    //   document.find("main").setAttribute("toggled", true);
    // } else {
    //   header.setAttribute("toggled", false);
    //   document.find("main").removeAttribute("toggled");
    // }

    if (typeof Route.execute === "function") Route.execute();
  }
};

/**
 * Scrolls to the top!
 */
export const scroll_to_top = () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

/**
 * Preloads a list of <img> elements by creating new Image
 * instances and marks them with a [loaded] tag so they will fade in.
 *
 * @param {HTMLImageElement[]} arr - Array of <img> elements to load
 * @returns {void}
 */
export const load_images = (arr) => {
  arr.forEach((img) => {
    if (img.hasAttribute("loaded")) return;

    let new_img = new Image();
    new_img.src = img.src;

    if (new_img.complete) {
      img.setAttribute("loaded", true);

      return;
    }

    img.onerror = () => {
      img.onerror = "";

      return;
    };

    img.onload = () => {
      img.setAttribute("loaded", true);

      return;
    };
  });
};

/**
 * Reloads all images and marks them again so new ones fade in.
 */
export const reload_images = () => load_images(document.find_all("img"));

/**
 * Creates a new responder with default values set.
 * Accepts either a message string or an object with `message` and `status`.
 *
 * @param {string|{message?: string, status?: boolean}} message - The message or config object
 * @param {string} status - Status string ("error", "success", etc.)
 * @param {HTMLElement} append_to - Element to append the responder to
 * @returns {void}
 */
export const create_responder = (
  message,
  status = "error",
  append_to = document.body,
) => {
  if (typeof message === "object" && message !== null && !Array.isArray(message))
    new Responder.Responder().add(
      append_to,
      message?.message ?? "No message",
      message?.status ? "success" : "error",
    );
  else new Responder.Responder().add(append_to, message, status);
};

/**
 * Removes all overlays.
 */
export const close_overlays = () => {
  let overlays = document.querySelectorAll("overlay");

  close_exception_overlay();

  if (!overlays) return;

  overlays.forEach((overlay) => {
    overlay.removeAttribute("visible");

    setTimeout(() => {
      overlay.remove();

      if (
        document.body.hasAttribute("toggled") &&
        document.body.getAttribute("toggled") == "true"
      )
        document.body.setAttribute("toggled", "false");

      __prompt_animation_playing = false;
    }, 400);
  });

  __current_overlay = null;
  __current_second_overlay = null;
};

/**
 * Only removes the exception overlay.
 */
export const close_exception_overlay = () => {
  document.find("exception-container")?.remove();
};

/**
 * Extract an exception from the incoming reponse either as text
 * or HTML already.
 *
 * @param {string|HTMLElement} from
 * @returns {void}
 */
export const extract_exception = (from) => {
  if (from && !(from instanceof Element) && from.includes("exception-container"))
    document.body.insertAdjacentHTML("beforeend", from);
  else if (from && !(from instanceof Element)) return;

  let exception = document.find("exception-container");

  if (exception) {
    exception.remove();
    document.body.appendChild(exception);
  }
};

/**
 * Handles AJAX errors by cleaning up UI and displaying an error message.
 *
 * - Unloads any loaders
 * - Re-enables all submit buttons
 * - Extracts the exception message
 * - Shows a responder with the error status
 *
 * @param {{ responseText: string, statusText: string }} error
 * @returns {void}
 */
export const ajax_error = (error) => {
  unload();

  /**
   * Activate all submit buttons, so the user can try again.
   */
  let buttons = document.find_all("[submit-closest]");
  if (buttons)
    buttons.forEach((button) => {
      button.enable();
    });

  extract_exception(error.responseText);
  create_responder(error.statusText, "error");
};

/**
 * Finds all <get-content></get-content> elements and loads the
 * content from the specified from attribute dynamically.
 */
export const get_content = () => {
  let get_contents = document.find_all("get-content");
  let url;
  let new_element;

  get_contents.forEach((element) => {
    url = element.getAttribute("from");

    if (!url) return;

    Request.queue({
      url: url,
      method: "GET",
      success: function (data) {
        element.insertAdjacentHTML("afterend", data?.data ?? data);
        new_element = element.nextElementSibling;
        element.remove();

        // Execute the JS code globally.
        new_element.find_all("script")?.forEach((script) => {
          $.globalEval(script.innerHTML);
        });

        Frontend.reload_images();
      },
      error: function (error, status) {
        if (status !== "abort") Frontend.ajax_error(error);
      },
    });
  });
};

/**
 * ? Prompt animation
 */
let __prompt_animation_playing = false;

export const slide_out_prompt = (prompt) => {
  if (!prompt || __prompt_animation_playing) return;

  __prompt_animation_playing = true;

  let outer_box = prompt.closest("box-model");
  let inner_prompt = prompt.closest("[has-inner-prompt]");
  let prompt_elements = [
    prompt.find("[prompt-header]"),
    prompt.find("[prompt-inner-content]"),
  ];

  prompt.unactivate();
  outer_box?.unactivate();
  inner_prompt?.unactivate();
  prompt_elements.forEach((element) => {
    element.unactivate();
  });

  setTimeout(() => {
    prompt.unstyle();

    __prompt_animation_playing = false;
  }, 110);
};

export const slide_in_prompt = (prompt) => {
  if (__prompt_animation_playing || !prompt) return;

  let prompt_padding = parseFloat(window.getComputedStyle(prompt).padding);
  let prompt_header = prompt.find("[prompt-header]");
  let prompt_content = prompt.find("[prompt-content]");
  let prompt_content_margin = parseFloat(
    window.getComputedStyle(prompt_content).marginBottom,
  );

  // Outer box exists, it's an inner prompt.
  let outer_box = prompt.closest("box-model");
  if (outer_box) {
    let inner_prompts = document.find_all("[has-inner-prompt][active]");

    let i = 0;
    while (i < inner_prompts.length) {
      slide_out_prompt(inner_prompts[i].find("[prompt]"));
      i++;
    }
  }

  let prompt_elements = [prompt_header, prompt.find("[prompt-inner-content]")];

  /**
   * Show the prompt and adjust it's height depending on the
   * height of the prompt content.
   */
  __prompt_animation_playing = true;
  prompt.activate();
  prompt.style.height =
    prompt_content.clientHeight + prompt_padding * 2 + prompt_content_margin + "px";

  outer_box?.activate();
  prompt.closest("[has-inner-prompt]")?.activate();

  setTimeout(() => {
    /**
     * Fade in the various elements from top to bottom after each
     * other with a set delay.
     */
    let animation_delay_timer = 0;
    prompt_elements.forEach((element) => {
      setTimeout(() => {
        element.activate();
      }, animation_delay_timer);

      animation_delay_timer += 120;
    });

    setTimeout(() => {
      prompt.style.overflow = "visible";
      prompt.style.height = "initial";

      __prompt_animation_playing = false;
    }, Setting.ANIMATION_TIME - 200);
  }, 200);
};

/**
 * Iterates through all jump menus and removes them from the frontend.
 */
export const close_jump_menus = () => {
  let menus = document.find_all("[menu-more");

  if (menus)
    menus.forEach((menu) => {
      let menu_outer = menu.closest("[menu-outer]");
      let menu_open = menu_outer?.find("[open-more-menu]");

      menu_open?.unactivate();

      if (menu.hasAttribute("active")) {
        menu.unactivate();
        let box = menu.closest("box-model");
        if (box) box.unactivate();
      }
    });
};

/**
 * Closes a given responder and hides it from the frontend.
 * @param {HTMLElement} responder
 */
export const close_responder = (responder) => {
  new Responder.Responder().close(responder);
};

/**
 * Sets the body's background color.
 */
export const set_background_color = (color) => {
  return (document.body.style.backgroundColor = color);
};

/**
 * Animates the ajax response container based on the return of requests.
 *
 * @param {string} type
 */
export const ajax_response = (type = "success") => {
  let container = document.find("ajax-response");

  container.setAttribute(type, true);
  container.activate();

  container.addEventListener("animationend", function (e) {
    container.removeAttribute(type);
    container.deactivate();
  });
};

/**
 * Createsa a responder based on the `responder` attribute being
 * set or not and the status of the data passed.
 *
 * @param {JSON} data
 * @param {string} responder
 * @returns {}
 */
export const create_dynamic_responder = (data, responder) => {
  if (data.status)
    if (responder === "success") Frontend.create_responder(data.message, "success");
    else if (responder === "error") Frontend.create_responder(data.message, "error");

  if (responder === "always")
    Frontend.create_responder(data.message, data.status ? "success" : "error");
};

/**
 * Adds the given string to the INFO_WINDOWS cookie to disable it.
 */
export const disable_info_window = (str) => {
  let info_windows_cookie = Cookie.get("INFO_WINDOWS");
  Cookie.set("INFO_WINDOWS", info_windows_cookie + "," + str, 365);
};

export const show_header_notice = async (elem) => {
  if (!elem) return;

  elem.removeAttribute("donotshow");
  let elem2 = elem.querySelector(".hover_card");

  if (!elem2) return;

  setTimeout(() => {
    elem2.setAttribute("active", "true");
  }, 200);
};

export const remove_header_notice = async (elem) => {
  if (elem) {
    let active_elements = elem.querySelectorAll("[active=true]");

    if (active_elements)
      active_elements.forEach((a) => {
        a.removeAttribute("active");
      });

    await Utils.sleep(400);
    elem.setAttribute("animation", "zoom-out");
    await Utils.sleep(400);
    elem.remove();
  }
};

export const close_composer = () => {
  let composer = document.find("[composer]");
  let reactions = document.find("[reactions-window]");

  if (composer) {
    composer.setAttribute("unactive", true);
    setTimeout(() => {
      composer.remove();
    }, 400);
  }

  if (reactions) {
    reactions.setAttribute("unactive", true);
    setTimeout(() => {
      reactions.remove();
    }, 400);
  }
};

export const toggle_user_menu = () => {
  let menu = document.find("user-menu");

  if (!menu) {
    create_responder("<strong>Where is the user menu dude? 🙄</strong>");
    return;
  }

  menu.hasAttribute("inactive")
    ? menu.removeAttribute("inactive")
    : menu.setAttribute("inactive", true);
};

export const close_ui_components = (toggle_user_menu = true) => {
  let user_menu = document.find("user-menu");
  let components = document.find_all("ui-component");
  let buttons = user_menu?.find_all("mbutton");

  if (toggle_user_menu && user_menu?.hasAttribute("inactive"))
    Frontend.toggle_user_menu();

  buttons?.forEach((button) => button.removeAttribute("active"));

  components.forEach((component) => {
    let categories = component.find_all("nc-tab-option");

    categories.forEach((button, key) => {
      button.removeAttribute("showing");
      button.unactivate();
    });

    component.deactivate();
  });

  __current_ui_component = null;
};

export const close_mode_menu = () => {
  let menu = document.find("mode-menu");
  if (!menu) return;

  let is_open = menu.hasAttribute("active");
  if (!is_open) return;

  menu.unactivate();

  let jump_menu = menu.find("jump-menu");
  jump_menu.unactivate();

  setTimeout(() => {
    jump_menu.removeAttribute("style");
  }, 200);
};

export const update_user_menu = () => {
  let url = "/ui/user-menu";
  let menu = document.find("user-menu");

  console.log("[Frontend] Updating user menu...");

  menu?.setAttribute("deleted", true);

  $.ajax({
    url: url,
    method: "GET",
    success: function (data) {
      if (data.status) {
        document.body.insertAdjacentHTML("afterbegin", data.data);
        menu?.remove();
        Frontend.reload_images();
      } else {
        menu?.removeAttribute("deleted");
        create_responder(data);
      }
    },
  });
};

export const toggle_floating_actions = (route_name) => {
  /**
   * Pages to show different actions in global UI.
   */
  const sign_up_routes = ["login", "begin", "register", "password-reset"];
  const search_routes = [
    "",
    "u",
    "squad",
    "login",
    "begin",
    "register",
    "password-reset",
    "home",
  ];
  const join_now_routes = [
    "login",
    "begin",
    "register",
    "password-reset",
    "connect",
    "download",
  ];

  /** Join now - container */
  if (__join_now)
    if (join_now_routes.includes(route_name)) __join_now.unactivate();
    else __join_now.activate();

  /** Global search icon */
  if (__search_icon)
    if (!search_routes.includes(route_name)) __search_icon.activate();
    else __search_icon.unactivate();

  /** Sign up icon */
  if (__sign_icon)
    if (!sign_up_routes.includes(route_name)) __sign_icon.activate();
    else __sign_icon.unactivate();

  hide_floating_action();
};

export const hide_floating_action = () => {
  if (__page.current.length < 1 || __page.current == "home") return;

  let footer = document.find("footer");

  if (!footer) return;

  let footerPosition = footer.getBoundingClientRect();
  let array = [];
  let floating_action = document.find("[floating-action]");

  if (floating_action) array.push(floating_action);
  if (array.length === 0) return;

  array.forEach((action) => {
    let computedStyle = window.getComputedStyle(action);

    if (computedStyle.display !== "none")
      if (
        footerPosition.top - 100 <= window.innerHeight &&
        footerPosition.bottom >= 0
      ) {
        action.unactivate();
      } else {
        action.activate();
      }
  });
};

/**
 * ? Main-Navigation
 */
export const slide_in_navigation = (navigation) => {
  if (!navigation) return;

  let options = navigation.find_all("[pn-option]");
  let timer = 0;

  options.forEach((option) => {
    setTimeout(() => {
      option.setAttribute("slidin", "");
    }, timer);

    timer += 100;
  });
};

export const just_show_navigation = (navigation) => {
  if (!navigation) return;

  let options = navigation.find_all("[pn-option]");

  options.forEach((option) => {
    option.setAttribute("show", "");
  });
};

$(function () {
  /**
   * Initialize all the material buttons to open up the ripple effect.
   */
  MaterialButton.init();

  /**
   * ? Keyboard Shortcuts
   */
  $(document).on("keyup", function (e) {
    if (!e.key) return;

    /**
     * ESC
     */
    if (e.key.toLowerCase() === "escape") {
      if (document.querySelector("overlay")) close_overlays();
      close_ui_components();

      return;
    }
  });

  /**
   * ? Click Events
   */
  $(document).on("click", function (e) {
    /**
     * Close inner prompts.
     */
    let inner_prompts = document.find_all("[has-inner-prompt][active]");
    if (inner_prompts && !__prompt_animation_playing)
      inner_prompts.forEach(async (i) => {
        if (e.target.closest("[has-inner-prompt]") !== i && e.target !== i) {
          let prompt = i.find("[inner-prompt]");
          if (prompt) await slide_out_prompt(prompt);
          i.unactivate();
        }
      });
  });

  /**
   * ? Scroll Events
   */
  $(window).on("scroll", function () {
    let header = document.querySelector("[scroll-changed]");

    if (!header) return;

    if (window.scrollY >= 200) {
      header.setAttribute("active", "");
    } else {
      header.removeAttribute("active");
    }
  });

  /**
   * ? UI Components
   */
  $(document).on("click", "[open-ui-component]", function (e) {
    let user_menu = document.find("user-menu");
    let component_name = this.getAttribute("open-ui-component");
    let component = document.find("ui-component[type=" + component_name + "]");
    let inr = component?.find("nc-inr");
    let buttons = component?.find_all("[data-category]");
    let floating_buttons = component?.find_all("nc-tabs-floating nc-tab-option");
    let toggle_user_menu = this.getAttribute("toggle-user-menu");
    let url = this.getAttribute("url");
    let button = this;

    /**
     * UI component was not found somehow?
     */
    if (!component) {
      Frontend.create_responder(
        "<strong>UI component not found 🥲</strong>",
        "error",
      );

      return;
    }

    /**
     * If a component is active and the button for it was pressed,
     * close all components.
     */
    if (component.hasAttribute("active")) {
      Frontend.close_ui_components(true);

      return;
    }

    /**
     * Toggle the user menu if it is not yet toggled.
     */
    if (
      this.hasAttribute("toggle-user-menu") &&
      !user_menu.hasAttribute("inactive") &&
      ((is_numeric(toggle_user_menu) &&
        window.innerWidth < parseInt(toggle_user_menu)) ||
        !is_numeric(toggle_user_menu))
    )
      Frontend.toggle_user_menu();

    /**
     * Close all components before opening a new one.
     */
    Frontend.close_ui_components(false);

    component.activate();

    let increasing_delay = 0;
    floating_buttons.forEach((b, key) => {
      b.deactivate();
      setTimeout(() => {
        b.setAttribute("showing", true);
      }, increasing_delay);
      increasing_delay += 60;

      if (floating_buttons.length === key + 1) floating_buttons[0].activate();
    });

    component.set_loading();

    setTimeout(() => {
      $.ajax({
        url: url,
        method: "GET",
        success: function (data) {
          component.unset_loading();

          if (data.status) {
            inr.innerHTML = data.data;
            Frontend.reload_images();

            __current_ui_component = component;

            // TODO: Button will activate when immediately pressing ESC.
            button.activate();
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }, 100);
  });

  /**
   * ? Infinite Scrolling
   */
  $(window).on("scroll", (e) => {
    let _container = document.find('[scroll="infinite"]');
    let form = document.find('[data-form="infinite-scroll"]');
    let _type = document.find("[scroll-type]");
    let offset = document.find('[data-react="infinite-scroll-offset"]');
    let loader = document.find('[data-react="scroll:reached-end"]');
    let page_end_bird = document.find("[page-end-bird]");
    let real_bodyScrollHeight = document.body.scrollHeight - window.innerHeight;
    let formdata;
    let formatted_data;
    let url;

    /**
     * Return if there is no infinite scroll container.
     */
    if (!_container || !form || !_type || __page.is_loading) return;

    _type = _type.getAttribute("scroll-type");
    let offset_number = parseInt(offset.value);

    let limit_number = form.find("input[name=limit]").value;
    formdata = new FormData(form);
    formatted_data = new URLSearchParams(formdata);

    if (_type === "squad") url = "/squad/score/fetch";
    if (_type === "beatmaps") url = "/beatmap/fetch";
    if (_type === "squads") url = "/squad/fetch";

    if (
      window.scrollY >= real_bodyScrollHeight - __infinite_scroll.page_offset &&
      !(__infinite_scroll.reached_end || __infinite_scroll.reached_full_end)
    ) {
      __page.is_loading = true;
      __infinite_scroll.reached_end = true;
      loader.setAttribute("show-loader", "");
      loader.removeAttribute("style");
      page_end_bird.style.display = "none";

      url = url + "?" + formatted_data;

      $.ajax({
        url: url,
        method: "GET",
        processData: false,
        contentType: false,
        success: function (data) {
          __page.is_loading = false;

          if (data.status) {
            _container.insertAdjacentHTML("beforeend", data.data);
            offset.value = offset_number + parseInt(limit_number);

            /**
             * full-end set?
             */
            if (document.find("infinite-scroll-full-end-reached")) {
              pdie("Reached full scroll end!");
              __infinite_scroll.reached_full_end = true;
              page_end_bird.removeAttribute("style");
              loader.style.display = "none";
            } else __infinite_scroll.reached_end = false;

            if (!data.data) {
              page_end_bird.removeAttribute("style");
              loader.style.display = "none";
            }
          }

          reload_images();

          if (!data.status)
            new Responder.Responder().add(
              document.body,
              data.message,
              "error",
              "beatmaps",
            );
        },
        error: function (data) {
          __page.is_loading = false;
          page_end_bird.removeAttribute("style");
          page_end_bird.style.display = "none";
          loader.removeAttribute("show-loader");
          new Responder.Responder().add(
            document.body,
            data.statusText,
            "error",
            "beatmaps",
          );
        },
      });
    }
  });

  $(document).on("click", "user-menu [toggle]", function (e) {
    toggle_user_menu();
  });

  /**
   * Click event listener for closing & hiding a given responder
   * from the frontend.
   */
  $(document).on("click", "[close-responder]", function (e) {
    e.preventDefault();

    let responder = this.closest("responder");

    if (responder) close_responder(responder);
  });

  /**
   * ? Theme
   * Switch dark or light mode.
   */
  $(document).on("click", "theme-switcher", function (e) {
    let current_theme = __page.theme;
    let body = document.body;

    /**
     * Remove all theme classes from body element.
     */
    document.body.className.split(" ").forEach((classs) => {
      if (classs.startsWith("theme--")) body.classList.remove(classs);
    });

    /**
     * Set the new theme class.
     */
    body.classList.add(
      "theme--" + current_theme + (__page.is_darkmode ? "" : "-dark"),
    );

    /**
     * Manipulate all theme switcher elements on page to shwo the
     * new state of dark mode enabled.
     */
    if (this.hasAttribute("active"))
      document.querySelectorAll("theme-switcher").forEach((item) => {
        item.removeAttribute("active");
      });
    else
      document.querySelectorAll("theme-switcher").forEach((item) => {
        item.setAttribute("active", "");
      });

    Cookie.set("DARKMODE", __page.is_darkmode ? 0 : 1, {
      expires: 365,
      domain: Utils.serialize_cookie_domain(),
    });

    __page.is_darkmode = __page.is_darkmode ? 0 : 1;
  });

  /**
   * Set a new theme.
   */
  $(document).on("click", '[data-action="my:website,theme"]', function (e) {
    let theme = this.getAttribute("chooser-option");
    let body = document.body;

    body.className.split(" ").forEach((classs) => {
      if (classs.startsWith("theme--")) body.classList.remove(classs);
    });

    body.classList.add("theme--" + theme + (__page.is_darkmode ? "-dark" : ""));

    Cookie.set("THEME", theme, {
      expires: 365,
      domain: Utils.serialize_cookie_domain(),
    });

    __page.theme = theme;
  });

  /**
   * ? Carousel
   */
  $(document).on(
    "click",
    ".carousel-shelf .carousel-shelf-button-group mbutton",
    function (e) {
      let wrapper = this.closest(".carousel-shelf");
      let action = this.getAttribute("carousel-action");
      let scrollable = wrapper.find(".carousel");
      let next = wrapper.find("[carousel-action='next']");
      let previous = wrapper.find("[carousel-action='previous']");

      const maxScroll = scrollable.scrollWidth - scrollable.clientWidth;

      let margin_right = window
        .getComputedStyle(scrollable.find(".carousel-item"))
        .getPropertyValue("margin-right");
      let scroll_amount = scrollable.clientWidth + parseInt(margin_right);

      if (action == "next") scrollable.scrollLeft += scroll_amount;
      else if (action == "previous") scrollable.scrollLeft -= scroll_amount;

      if (maxScroll - scrollable.scrollLeft <= 100) {
        next.setAttribute("disabled", "");
        previous.removeAttribute("disabled");
      } else {
        if (next.hasAttribute("disabled")) next.removeAttribute("disabled");
      }

      if (maxScroll - scrollable.scrollLeft + 100 > maxScroll) {
        next.removeAttribute("disabled");
        previous.setAttribute("disabled", "");
      } else {
        if (previous.hasAttribute("disabled")) previous.removeAttribute("disabled");
      }
    },
  );

  /**
   * ? Toggle switches.
   */
  $(document).on("click", "toggle-switch", function (e) {
    let input = this.find('input[type="hidden"]');

    if (this.getAttribute("toggled") === "false") {
      this.setAttribute("toggled", true);
      if (input && !this.hasAttribute("keep-value")) input.value = "1";
    } else {
      this.setAttribute("toggled", false);
      if (input && !this.hasAttribute("keep-value")) input.value = "0";
    }
  });

  /**
   * Dynamically update boolean values in cookies.
   *
   * If the cookie doesn't exist, it will automatically set it to 0,
   * assuming the user wants to disable a setting that has not been
   * touched yet.
   */
  $(document).on("click", "[update-cookie-bool]", function (e) {
    setTimeout(() => {
      let setting = this.getAttribute("update-cookie-bool");
      let cookie_name = setting.toUpperCase();
      let cookie = Cookie.get(cookie_name);
      let is_enabled = cookie == 1 || cookie == undefined;
      let domain = Utils.serialize_cookie_domain();

      Cookie.set(cookie_name, is_enabled ? "0" : "1", {
        domain: domain,
        expires: 780,
      });

      switch (cookie_name) {
        case "SOUNDS":
          __page.is_sounds_enabled = is_enabled ? 0 : 1;
          break;
        case "ANIMATIONS":
          __page.is_animations_enabled = is_enabled ? 0 : 1;
          break;
        default:
        // nothing.
      }
    }, 100);
  });

  /**
   * ? Inner prompts
   * Inner prompts are elements that should be opened at the same
   * place of the clicked element to open it, like a popup.
   */
  $(document).on("click", "[open-inner-prompt]", function (e) {
    if (__prompt_animation_playing) return;

    let prompt_outer = this.closest("[has-inner-prompt]");
    if (prompt_outer?.is_activated()) return;

    let prompt = prompt_outer.find("[inner-prompt]");

    if (prompt_outer && !prompt_outer.hasAttribute("active")) {
      slide_in_prompt(prompt);
    } else {
      slide_out_prompt(prompt);
    }
  });

  /**
   * Close all overlays.
   */
  $(document).on(
    "click",
    '[close-overlay], [o-closer], [data-action="overlays:close"]',
    function (e) {
      close_exception_overlay();
      close_overlays();
    },
  );

  /**
   * Open popups dynamically.
   */
  $(document).on("click", "[data-action='popup:open']", function (e) {
    Request.get(this.dataset.href);
  });

  /**
   * Scroll to the top.
   */
  $(document).on("click", "[scroll-top]", function (e) {
    Window.scroll_to_top();
  });

  /**
   * Play a random audio file from the audio-tags in the body.
   */
  $(document).on("click", "[play-random-sound]", function (e) {
    let audios = document.querySelectorAll("audio");

    if (!audios.length || !__page.is_sounds_enabled) return;

    Utils.random(audios).play();
  });

  /**
   * Set a given animation and remove it after the in css set
   * animation duration.
   */
  $(document).on("click", "[toggle-animation]", function (e) {
    let animation = this.getAttribute("toggle-animation");

    this.setAttribute(animation, "");

    let style = window.getComputedStyle(this);
    let duration = style.getPropertyValue("animation-duration");
    let milliseconds = parseFloat(duration) * 1000;

    setTimeout(() => {
      this.removeAttribute(animation);
    }, milliseconds);
  });

  $(document).on("click", "mode-menu", function (e) {
    let is_open = this.hasAttribute("active");
    let is_loading = this.hasAttribute("loading");

    if (is_open || is_loading) return;

    let menu = this.find("jump-menu");
    let menu_inr = menu.find("[jm-inr]");

    this.activate();

    menu.style.height = menu_inr.clientHeight - 40 + "px";
    menu.activate();
    menu.style.height = menu_inr.clientHeight + "px";
  });

  $(document).on("click", "mode-menu [mm-menu] a", function (e) {
    let menu = this.closest("jump-menu");
    let mode_menu = menu.closest("mode-menu");
    let mode_name = this.find("p").innerHTML;
    let mod_icon_class = this.find("mi").className;
    let button = mode_menu.find("[mm-open]");

    button.find("mi").className = mod_icon_class;
    button.find("p").innerHTML = mode_name;

    menu.unactivate();
  });

  $(document).on("click", "[expand-more-show]", function (e) {
    const menu = this.closest("[expand-more]");
    const hidden = menu.find("[expand-more-hidden]");
    const text = menu.find("[expand-more-button-text]");

    let is_collapsed = menu.hasAttribute("active");

    is_collapsed ? menu.unactivate() : menu.activate();
  });

  /**
   * Hide chat button when footer comes into view.
   */
  $(window).on("scroll", function (e) {
    if (!__page.is_loading) hide_floating_action();
  });

  /**
   * Loads a given href of the dataset of the button dynamically
   * into the app container.
   */
  // TODO: Find out if this function is really needed.
  $(document).on("click", '[data-action="get"]', function (e) {
    e.preventDefault();

    let button = this;
    let url = this.dataset.href;
    let composer = this.hasAttribute("open-composer");

    if (composer && document.find("[composer]")) return;
    if (!url) return;

    button.disable();
    Frontend.load();

    $.ajax({
      url: url,
      contentType: false,
      processData: false,
      success: function (d) {
        unload();
        button.enable();

        if (d.status && d.data) {
          __body.insertAdjacentHTML("beforeend", d.data);
          Frontend.reload_images();
        } else new Responder.Responder().add(__body, e.statusText, "error");
      },
      error: function (e) {
        unload();
        button.enable();
        new Responder.Responder().add(__body, e.statusText, "error");
      },
    });
  });

  $(document).on("click", "toggle-sub-menu", function (e) {
    let sub_menu = document.find("sub-menu");

    if (sub_menu.hasAttribute("active")) {
      this.unactivate();
      sub_menu.unactivate();
    } else {
      this.activate();
      sub_menu.activate("active", "");
    }
  });

  $(document).on("click", "[close-dialogue]", function (e) {
    let dialogue = this.closest("[dialogue]");

    if (dialogue) dialogue.remove();

    if (dialogue && this.dataset.infoWindow)
      disable_info_window(this.dataset.infoWindow);
  });

  /**
   * Basically a radio element but cooler.
   */
  $(document).on("click", "[chooser] [chooser-option]", function (e) {
    let input = $(this).closest("[chooser]").find("[chooser-input]");
    let chooser = $(this).closest("[chooser]");
    let option_value = $(this).attr("chooser-option");
    let options = chooser[0].find_all("[chooser-option]");

    if (option_value) input.value = option_value;

    options.forEach(function (option) {
      option.unactivate();
    });

    this.activate();
  });
});
