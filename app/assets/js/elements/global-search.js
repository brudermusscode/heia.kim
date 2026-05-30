import * as Utils from "../utils";
import * as Frontend from "../frontend";
import Overlay from "./Overlay";

/**
 * KEYBOARD SHORTCUTS
 *
 * F: Open global search
 */
document.addEventListener("keyup", (e) => {
  let focused_element = document.activeElement.tagName.toLowerCase();
  let invalid_elements = ["input", "textarea", "button"];

  if (!e.key) return;

  if (e.key.toLowerCase() === "f") {
    /**
     * Return if any input is focused.
     */
    if (invalid_elements.includes(focused_element)) return;

    open_global_search();
  }
});

/**
 * Keypresses while search input is focused
 */
let __global_search_timeout;
let __global_search_timeout_ms = 600;

Utils.delegate(
  document,
  "input",
  '[data-action="search:start"]',
  async function (e) {
    let value = this.value;
    let formdata = new FormData();
    let search = document.querySelector("[global-search]");
    let search_loader = search.querySelector("[search-loader]");
    let search_results = search.querySelector(".global_search__result");

    clearTimeout(__global_search_timeout);

    if (value.trim().length < 1) {
      search_loader.removeAttribute("active");
      if (search_results) search_results.remove();
      return;
    }

    formdata.append("query", value);

    search_loader.setAttribute("active", "");

    __global_search_timeout = setTimeout(() => {
      axios.post("/ui/search/fetch", formdata).then((data) => {
        search_results = search.querySelector(".global_search__result");

        if (search_results) search_results.remove();

        search.insertAdjacentHTML("beforeend", data.data.data);

        const images = document.body.querySelectorAll("img");
        Frontend.reload_images(images);

        search_loader.removeAttribute("active");
      });
    }, __global_search_timeout_ms);
  }
);

/**
 * Open the global search.
 */
Utils.delegate(
  document,
  "click",
  "[data-action='search:open']",
  async function (e) {
    open_global_search();
  }
);

/**
 * Close the overlay of the search when a links is clicked.
 */
Utils.delegate(document, "click", "[global-search] a", async function (e) {
  Frontend.close_overlays();
});

/**
 * Opens the global search in an overlay.
 */
export const open_global_search = () => {
  let search;
  let overlay;

  /**
   * If search is open already, return.
   */
  if (search && search.hasAttribute("active")) return;

  overlay = new Overlay();

  axios.post("/ui/global-search").then((data) => {
    setTimeout(() => {
      overlay.append(data.data.data);
      setTimeout(() => {
        document.find('[data-action="search:start"]').focus();
      }, 201);
    }, 200);
  });
};
