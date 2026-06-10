import * as Frontend from "../frontend";
import * as Request from "../requests";

/**
 * @event keyup
 */
document.addEventListener("keyup", (e) => {
  let focused_element = document.activeElement.tagName.toLowerCase();
  let invalid_elements = ["input", "textarea", "button"];

  if (!e.key) return;

  // Open global search.
  if (e.key.toLowerCase() === "f") {
    // Return if any input is focused.
    if (invalid_elements.includes(focused_element)) return;

    Request.get("/search");
  }
});

// Watch keypresses while search input is in focus.
let __global_search_timeout;
let __global_search_timeout_ms = 600;

/**
 * @event input
 * @this HTMLElement <input search>
 */
$(document).on("input", "input[search]", function (e) {
  let value = this.value;
  let search = this.closest("global-search");
  let search_loader = search.find("[loader]");
  let search_results = search.find("search-results");

  clearTimeout(__global_search_timeout);

  if (value.trim().length < 1) {
    if (search_results) search_results.innerHTML = "";
    search_loader.deactivate();

    return;
  }

  search_loader.activate();

  __global_search_timeout = setTimeout(() => {
    $.ajax({
      url: `/get/search?query=${value}`,
      success: function (data) {
        search_results.innerHTML = data.data;
        search_loader.deactivate();
        Frontend.reload_images();
      },
    });
  }, __global_search_timeout_ms);
});

/**
 * Close the overlay of the search when a links is clicked.
 *
 * @event click
 * @this [global-search] a
 */
$(document).on("click", "[global-search] a", function (e) {
  Frontend.close_overlays();
});
