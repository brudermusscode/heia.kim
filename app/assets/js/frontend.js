import * as Page from "./page.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";
import * as Window from "./window.js";
import * as Responder from "./elements/responder.js";
import * as Setting from "./settings.js";
import * as MaterialButton from "./elements/mbutton.js";
import * as Request from "./requests.js";
import * as Audio from "./audio.js";

let Cookie = require("js-cookie");

$.ajaxSetup({
  contentType: false,
  processData: false,
  error: function (error) {
    Frontend.ajax_error(error);
  },
});

/**
 * Initializes all the important parts of the frontend in one run.
 */
export const init = async (Route, PreviousRoute = null, update_refs = false) => {
  let main_container = document.find("main");

  extract_exception(document.body);

  await Page.redirect();

  if (update_refs) await update_user_menu();

  page_navigator(Route, PreviousRoute);
  toggle_floating_actions(Route.key);
  get_content();
  unload();
  reload_images();

  MaterialButton.init();

  document.body.setAttribute("initialized", true);
  document.body.setAttribute(
    Route.key == ""
      ? "home"
      : Route.body_attribute !== undefined
        ? Route.body_attribute
        : Route.key,
    "",
  );

  main_container?.find_all("request")?.forEach((elem) => Request.request(elem));
  main_container.find_all("script").forEach((script) => {
    eval(script.innerHTML);
  });
};

export const reload = () => {
  return Page.reload();
};

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

export const scroll_to_top = () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

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
 * Short version to create a responder.
 */
export const respond = (message, status = "error", append_to = document.body) => {
  create_responder(message, status, append_to);
};

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

export const close_current_overlay = () => {
  __page.overlay?.delete();
};

export const close_exception_overlay = () => {
  document.find("exception-container")?.remove();
  __page.exception = null;
};

export const extract_exception = (from) => {
  if (from && !(from instanceof Element) && from.includes("exception-container"))
    document.body.insertAdjacentHTML("beforeend", from);
  else if (from && !(from instanceof Element)) return;

  let exception = document.find("exception-container");

  if (exception) {
    exception.remove();
    document.body.appendChild(exception);

    __page.exception = exception;
  }
};

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
 * --------------------------------------------
 * Prompt Animation ---------------------------
 * --------------------------------------------
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

export const close_responder = (responder) => {
  new Responder.Responder().close(responder);
};

export const set_background_color = (color) => {
  return (document.body.style.backgroundColor = color);
};

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
    respond("<strong>Menu where? 🙁</strong>");
    return;
  }

  if (menu.hasAttribute("inactive")) {
    menu.removeAttribute("inactive");
    localStorage.setItem("component-user-menu", 1);
  } else {
    menu.setAttribute("inactive", true);
    localStorage.setItem("component-user-menu", 0);
  }
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

  __page.component = null;
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

export const update_user_menu = async () => {
  return new Promise((resolve, reject) => {
    let menu = document.find("user-menu");

    menu?.setAttribute("deleted", true);

    $.ajax({
      url: "/ui/user-menu",
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

        resolve(1);
      },
    });
  });
};

/**
 * --------------------------------------------
 * Floating sign actions ----------------------
 * --------------------------------------------
 */

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
 * --------------------------------------------
 * Page Navigator -----------------------------
 * --------------------------------------------
 */

export const page_navigator = (Route, PreviousRoute = null) => {
  let slide = PreviousRoute?.page_navigator !== Route.page_navigator;

  Frontend.show_page_navigator(slide);
};

export const show_page_navigator = async (slide = true) => {
  let navigation = await ensure_page_navigator();

  if (!navigation) return;

  let options = navigation.find_all("[pn-option]");
  let timer = 0;

  options.forEach((option) => {
    if (slide) {
      setTimeout(() => {
        option.setAttribute("slidin", "");
      }, timer);

      timer += 100;
    } else option.setAttribute("show", "");
  });
};

const ensure_page_navigator = async () => {
  let tries = 0;
  let navigation = document.find("page-navigator");
  while (!navigation) {
    navigation = document.find("page-navigator");
    await Utils.sleep(100);
    tries++;

    if (tries === 20) break;
  }

  return navigation;
};

const infscroll_fetch = () => {
  let _container = document.find('[scroll="infinite"]');
  let form = document.find('[data-form="infinite-scroll"]');
  let _type = document.find("[scroll-type]")?.getAttribute("scroll-type");
  let offset = document.find('[data-react="infinite-scroll-offset"]');
  let loader = document.find('[data-react="scroll:reached-end"]');
  let page_end_bird = document.find("[page-end-bird]");
  let real_bodyScrollHeight = document.body.scrollHeight - window.innerHeight;
  let url;

  if (!_container || !form || !_type || __page.is_loading) return;

  let offset_number = parseInt(offset.value);
  let limit_number = form.find("input[name=limit]").value;
  let formdata = new FormData(form);
  let formatted_data = new URLSearchParams(formdata);

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

        if (!data.status) respond(data);
      },
      error: function (data) {
        __page.is_loading = false;
        page_end_bird.removeAttribute("style");
        page_end_bird.style.display = "none";
        loader.removeAttribute("show-loader");
        respond(data.statusText, "error");
      },
    });
  }
};

const scroll_header_toggle = () => {
  let scroll_container = document.body.querySelectorAll("[scroll-manipulated]");

  if (!scroll_container[0]) return;
  if (
    (scroll_container[0] && document.documentElement.scrollTop >= 40) ||
    document.body.scrollTop >= 40
  )
    scroll_container.forEach((s) => {
      s.setAttribute("scrolled", true);
    });
  else
    scroll_container.forEach((s) => {
      s.setAttribute("scrolled", false);
    });
};

$(function () {
  //

  MaterialButton.init();

  /**
   * @event keyup
   */
  $(document).on("keyup", function (e) {
    const key = (key) => {
      return e.key.toLowerCase() === key;
    };

    const tag = (tag) => {
      return e.target.tagName.toLowerCase() === tag;
    };

    // Lovely click sounds.
    if (__page.is_sounds_enabled) {
      Audio.stop("[click-audio]");
      Audio.play("[click-audio]");
    }

    if (key("enter")) e.preventDefault();
    if (
      key("enter") &&
      (tag("input") || tag("textarea")) &&
      e.target.hasAttribute("enter-submitable")
    ) {
      let form = e.target.closest("form");
      let button = form.querySelector("[submit-closest]");
      if (form && button && !button.hasAttribute("disabled")) {
        let required_empty = false;
        form.find_all("input")?.forEach((input) => {
          if (!input.value && input.hasAttribute("required")) required_empty = !0;
        });

        // if (!required_empty) $(form).submit();
      }
    }

    if (key("escape")) {
      if (__page.exception) return close_exception_overlay();
      if (!__page.overlay) close_ui_components();
      if (!__page.overlay?.locked) __page.overlay?.delete();
    }

    if (key("e")) {
      if (!__current_user.id || __page.component) return;

      Page.get(`/editor`);
    }

    if (key("p")) {
      if (!__current_user.id || __page.component) return;

      Page.get(`/u/${__current_user.id}`);
    }
  });

  /**
   * @event click
   */
  $(document).on("click", async function (e) {
    // Closes opened inner prompts.
    let inner_prompts = document.find_all("[has-inner-prompt][active]");
    if (inner_prompts && !__prompt_animation_playing)
      inner_prompts.forEach(async (i) => {
        if (e.target.closest("[has-inner-prompt]") !== i && e.target !== i) {
          let prompt = i.find("[inner-prompt]");
          if (prompt) slide_out_prompt(prompt);
          i.unactivate();
        }
      });

    // Closes all jump menus.
    if (!e.target.closest("[open-more-menu]") && !e.target.closest("[menu-more]"))
      Frontend.close_jump_menus();

    // Custom handling for <a> tags.
    let anchor = e.target.closest("a");
    let href = anchor?.getAttribute("href");

    if (anchor !== null) {
      if (!anchor.hasAttribute("extern") && href) {
        e.preventDefault();

        await Page.get(href, false, anchor);
      }
    }

    // Close all ui components.
    if (
      __page.component &&
      !e.target.closest("ui-component") &&
      !e.target.closest("[open-ui-component]") &&
      !__page.overlay
    )
      Frontend.close_ui_components(true);

    // Close mode menu on outside click.
    let mode_menu = e.target.closest("mode-menu");
    if (!mode_menu || !mode_menu.matches("mode-menu")) Frontend.close_mode_menu();
  });

  /**
   * @event scroll
   */
  $(window).on("scroll", function () {
    close_composer();
    scroll_header_toggle();
    infscroll_fetch();
  });

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

    if (!component) {
      Frontend.create_responder(
        "<strong>UI component not found 🥲</strong>",
        "error",
      );

      return;
    }

    // Close the componetn if it is active already.
    if (component.hasAttribute("active")) return Frontend.close_ui_components(true);

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

            __page.component = component;

            // TODO: Button will activate when immediately pressing ESC.
            button.activate();
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }, 100);
  });

  $(document).on("click", "user-menu [toggle]", function (e) {
    toggle_user_menu();
  });

  $(document).on("click", "[close-responder]", function (e) {
    e.preventDefault();

    let responder = this.closest("responder");

    if (responder) close_responder(responder);
  });

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
   * Carousel
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
   * Inner prompts are elements that should be opened at the same place of the click-
   * ed element to open it, like a popup.
   *
   * @event click
   * @this HTMLElement [open-inner-prompt]
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
   * Close a visible overlay. Respects exception-overlay as a seperate overlay.
   *
   * @event click
   * @this HTMLELement [close-overlay]
   */
  $(document).on("click", "[close-overlay]", function (e) {
    if (this.closest("exception-container")) return close_exception_overlay();

    __page.overlay?.delete();
  });

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
