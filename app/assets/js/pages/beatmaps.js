import * as Page from "../page";
import * as Utils from "../utils";
import * as Frontend from "../frontend";
import * as Responder from "../elements/responder";
import * as Cookies from "../cookies";
import Overlay from "../elements/Overlay";

let __type_search_timeout = null;
let __search_showcase_timeout;
let __search_showcase_loading_timeout = 0;

/**
 * Closes any open comment windows
 */
const close_comments = () => {
  let comments = document.find("[comments-container]");

  if (comments && comments.hasAttribute("active")) {
    comments.removeAttribute("active");

    setTimeout(() => {
      comments.remove();
    }, 400);
  }
};

/**
 * Closes the search on beatmaps page
 */
// TODO: Use this for global search too.
export const close_search = async () => {
  let search = document.body.querySelector("[search]");

  if (!search || !search.hasAttribute("active")) return;

  search.setAttribute("closing", "");
  search.removeAttribute("active");
  search.querySelector(".search_outer").removeAttribute("style");

  setTimeout(() => {
    search.removeAttribute("closing");
  }, 600);
};

$(function () {
  //

  /**
   * @event keyup
   */
  $(document).on("keyup", function (e) {
    if (e.key.toLowerCase() == "escape") {
      document.activeElement.blur();
      close_comments();
    }
  });

  /**
   * Open the fullscreen filter overlay.
   */
  // TODO: DRY code opening non ajax popups.
  $(document).on("click", "[data-action='filters:open']", function (e) {
    let filters = document.find("filters");

    if (!filters) return;

    new Overlay(filters.innerHTML);
  });

  $(document).on(
    "submit",
    '[data-form="infinite-scroll"][data-form-type="beatmaps"]',
    function (e) {
      e.preventDefault();

      let data = new FormData(this);
      let link = data.get("submit_link");
      let query = data.get("query").replace("&", " ");
      let backup = data.get("backup");

      /**
       * Query is same as the backup?
       */
      if (backup.trim() === query.trim()) return;

      let final_link =
        query.trim().length < 1
          ? link
          : link.concat(`?query=${encodeURIComponent(query)}`);

      Page.get(final_link);

      /**
       * Query is empty?
       */
      if (query.trim().length < 1) return;
      if (__current_user.id === 0) return;

      let formdata = new FormData();
      formdata.append("query", query);
      formdata.append("type", "beatmap");

      $.ajax({
        url: "/search/create",
        data: formdata,
        method: "POST",
        dataType: "JSON",
        contentType: false,
        processData: false,
        success: function (data) {
          console.log(data);
        },
      });

      __infinite_scroll.reached_full_end = false;
    },
  );

  /**
   * Submit search form on enter.
   */
  $(document).on("keyup", '[data-action="beatmaps:search"]', function (e) {
    let form = document.find('[data-form="infinite-scroll"]');
    let submit = form.querySelector("button[type=submit]");

    if (!e.key) return;

    if (e.key.toLowerCase() === "enter") {
      this.blur();
      submit.click();

      pdie("Entered...");

      return;
    }
  });

  /**
   * Manipulate query inputs value on type
   */
  $(document).on("input", '[data-action="beatmaps:search"]', function (e) {
    let form = document.find('[data-form="infinite-scroll"]');
    let value = this.value;
    let query_input = form.find("input[name=query]");

    if (this.value.trim().length < 1) {
      query_input.value = "";

      return;
    }

    query_input.value = value;
  });

  /**
   * Start playing a beatmap set's preview song.
   */
  $(document).on("click", '[data-action="beatmaps:set,play"]', function (e) {
    if (this.hasAttribute("active")) {
      __current_audio_element.pause();
      this.removeAttribute("active");

      return;
    }

    this.setAttribute("active", "");
    let set_id = this.dataset.id;
    let play_button = this.closest(".play_button");
    let length = play_button.querySelector(".length");

    let audio = document.createElement("audio");
    audio.setAttribute(
      "src",
      __osu.beatmap_preview_url + "/preview/" + set_id + ".mp3",
    );
    __main.appendChild(audio);

    __current_audio_element = audio;

    audio.play();

    audio.addEventListener("loadedmetadata", () => {
      const audio_duration = audio.duration;

      play_button.setAttribute("active", "");
      this.setAttribute("active", "");

      audio.addEventListener("timeupdate", () => {
        const current_time = audio.currentTime;
        const progress_percent = (current_time / audio_duration) * 100;

        length.style.width = progress_percent + "%";

        if (audio.ended) length.style.width = "100%";
      });
    });

    // audio.addEventListener('pause', () => {
    //   console.log('Audio was stopped.');
    // });

    audio.addEventListener("ended", () => {
      this.removeAttribute("active");
      length.removeAttribute("style");
      play_button.removeAttribute("active");
      audio.remove();
    });
  });

  /**
   * Search on recently/popular click.
   */
  // TODO: Repair clicking recent searches.
  $(document).on("click", '[data-action="beatmaps:search,recent"]', function (e) {
    let form = document.body.querySelector('[data-form="beatmaps:search"]');
    let query_input = form.querySelector("input[name=query]");
    let query = this.querySelector("input[name=query]").value;
    let input = this.closest("[search]").querySelector(
      'input[data-action="beatmaps:search"]',
    );

    input.value = query_input.value = query;
    input.focus();
    document.dispatchEvent(new KeyboardEvent("keyup", { key: "Enter" }));
    close_search();
  });

  /**
   * ? Comments
   * Add event listeners for dragging the comment window.
   */
  let __current_drag_element = null;
  let __is_dragging = false;
  let __drag_startX = 0;
  let __drag_currentX = 0;

  $(document).on("mousedown", "[dragme]", function (e) {
    __is_dragging = true;
    __drag_startX = e.clientX;
    __drag_currentX = this.parentNode.offsetLeft;
    __current_drag_element = this.parentNode;

    document.body.setAttribute("disable-user-selection", "");

    document.addEventListener("mousemove", (e) => {
      if (!__is_dragging) return;

      let deltaX = e.clientX - __drag_startX;
      let newLeft = __drag_currentX + deltaX;

      // Constrain movement within the screen width
      let maxWidth = window.innerWidth - __current_drag_element.offsetWidth;
      newLeft = Math.max(0, Math.min(newLeft, maxWidth));

      __current_drag_element.style.left = newLeft + "px";
    });

    document.addEventListener("mouseup", (e) => {
      __is_dragging = false;
      document.body.removeAttribute("disable-user-selection");
    });
  });

  /**
   * Creates and appends a new comment.
   *
   * @action CREATE
   * @controller CommentsController
   */
  $(document).on("submit", '[data-form="comments:create old"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let textarea = this.querySelector("textarea");
    let composer = this.closest("[composer]");
    let comments = this.closest("[comments-container]");
    let comments_scroll = comments.find("[get-scroll]");
    let append_comment = comments.querySelector('[data-react="comments:create"]');
    let empty = append_comment.find("[empty]");

    Frontend.load();

    axios.post("/comment/create", formdata).then(async (data) => {
      Frontend.unload();

      if (data.data.status) {
        textarea.value = "";
        textarea.blur();
        composer.removeAttribute("active");

        /**
         * Remove empty box
         */
        if (empty) empty.remove();

        append_comment.insertAdjacentHTML("beforeend", data.data.data);

        /**
         * Get new height of the comments container and set the
         * parent's height accordingly
         */
        let comments_padding = window
          .getComputedStyle(comments)
          .getPropertyValue("padding-top");
        let comments_height = append_comment.clientHeight;
        let comments_newHeight =
          composer.clientHeight +
          comments_height +
          parseInt(comments_padding.replace("px", "")) * 2;
        comments.style.height = comments_newHeight + "px";

        /**
         * Scroll to the end
         */
        comments_scroll.scrollTop = comments_scroll.scrollHeight;

        Frontend.reload_images();
      } else {
        new Responder.Responder().add(
          document.body,
          data.data.message,
          "error",
          "comments",
        );
      }
    });
  });

  /**
   * Open comment window.
   *
   * @action GET
   */
  $(document).on("click", '[data-action="beatmaps:comments,open"]', function (e) {
    e.preventDefault();

    let formdata = new FormData();
    let set_id = this.dataset.id;
    let comments = document.querySelector("[comments-container]");
    let has_info_winow = this.closest("[has-info-window]");

    /**
     * Hide the info window
     */
    if (has_info_winow) {
      has_info_winow.removeAttribute("has-info-window");
      Frontend.disable_info_window("beatmap_comments");
    }

    if (comments) {
      close_comments();

      return;
    }

    formdata.append("id", set_id);

    Frontend.load();

    axios.post(`/beatmapsets/comments`, formdata).then(async (data) => {
      Frontend.unload();

      document.querySelector("app").insertAdjacentHTML("beforeend", data.data.data);

      Frontend.reload_images();

      let comments = document.querySelector("[comments-container]");
      let composer = comments.find("[composer]");
      let comments_scroll = comments.querySelector(".comments");
      let textareas = document.querySelectorAll("textarea[auto-resize]");

      /**
       * Calculate the full height for the comment window to attach to.
       */
      let comments_padding = window
        .getComputedStyle(comments)
        .getPropertyValue("padding-top");
      let comments_height =
        comments_scroll.querySelector("[get-height]").clientHeight;
      let comments_newHeight =
        composer.clientHeight +
        comments_height +
        parseInt(comments_padding.replace("px", "")) * 2;

      if (comments_newHeight > window.innerHeight)
        comments_newHeight = window.innerHeight;

      comments.setAttribute("active", "");
      comments.style.height = comments_newHeight + "px";
      comments_scroll.scrollTop = comments_scroll.scrollHeight;

      /**
       * Add blur event listeners for all textareas.
       */
      textareas.forEach((elem) => {
        elem.addEventListener("input", (e) => {
          if (elem.value.trim().length < 1) elem.style.height = "auto";
          else elem.style.height = `${elem.scrollHeight}px`;
        });

        elem.addEventListener("blur", (e) => {
          elem.closest("[composer]").removeAttribute("active");
        });
      });
    });
  });

  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,, REQUESTS ,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */

  /**
   * Create request
   *
   * @action UPDATE
   * @controller UsersController
   */
  $(document).on("submit", '[data-form="requests:create"]', function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let radio = this.hasAttribute("radio");
    let show_responder = this.hasAttribute("responder");

    clearTimeout(__submit);

    if (!radio && button) button.disable();

    if (this.hasAttribute("delayed")) __submit_timeout = __submit_timeout_delay;

    __submit = setTimeout(() => {
      let formdata = new FormData(this);

      $.ajax({
        url: "/request/create",
        data: formdata,
        method: "POST",
        contentType: false,
        processData: false,
        success: function (data) {
          Frontend.unload();
          button.enable();

          if (data.status) {
            Page.reload();
          }

          if (show_responder || !data.status)
            new Responder.Responder().add(
              document.body,
              data.message,
              data.status ? "success" : "error",
              "settings",
            );
        },
        error: function (data) {
          button.enable();
          new Responder.Responder().add(
            document.body,
            data.statusText,
            "error",
            "settings",
          );
        },
      });
    }, __submit_timeout);

    __submit_timeout = 0;
  });
});

// document.addEventListener("scroll", async (e) => {
//   let _container = document.body.querySelector('[scroll="infinite"]');
//   let real_bodyScrollHeight = document.body.scrollHeight - window.innerHeight;
//   let formdata;
//   let formatted_data;

//   if (
//     !_container ||
//     __infinite_scroll.reached_end ||
//     !window.location.pathname.includes("beatmaps") ||
//     __page.is_loading
//   )
//     return;

//   formdata = new FormData(
//     document.body.querySelector('[data-form="beatmaps:search"]')
//   );
//   formatted_data = new URLSearchParams(formdata);

//   if (
//     window.scrollY >= real_bodyScrollHeight - __infinite_scroll.page_offset &&
//     !(__infinite_scroll.reached_end || __infinite_scroll.reached_full_end)
//   ) {
//     __infinite_scroll.reached_end = true;
//     await load_beatmaps(formatted_data, _container);
//     __infinite_scroll.reached_end = false;
//   }
// });
