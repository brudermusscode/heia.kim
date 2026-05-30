import * as Utils from "../utils";

/**
 * When clicked, evaluate if it was the model itself or the
 * dropdown option.
 */
Utils.delegate(document, "click", "mselect", function (e) {
  let dropdown = this.querySelector("mselect-dropdown");
  let dropdown_size = dropdown.querySelector("[get-size]");
  let height = dropdown_size.clientHeight;
  let width = dropdown_size.clientWidth;
  let type = this.getAttribute("mselect-type");

  if (!e.target.closest("mselect-dropdown")) {
    dropdown.setAttribute("active", "");
    dropdown.style.height = height + "px";

    setTimeout(() => {
      dropdown.style.width = width + "px";

      setTimeout(() => {
        dropdown_size.setAttribute("active", "");
      }, 320);
    }, 60);
  }

  if (
    e.target.closest("mselect-option") ||
    e.target.tagName === "mselect-option"
  ) {
    dropdown.removeAttribute("active");
    dropdown.removeAttribute("style");
    dropdown_size.removeAttribute("active");

    let change_visible_value = e.target.closest(
      "[mselect-change-visible-value]"
    )
      ? e.target.closest("[mselect-change-visible-value]")
      : e.target;
    let visible_value = this.querySelector("[mselect-visible-value]");
    let change_input_value = change_visible_value.getAttribute(
      "mselect-input-value"
    );
    let input = this.querySelector("input[mselect-input]");

    /**
     * Each type
     */
    if (type == "input-visible") {
      visible_value.innerHTML = change_visible_value.innerHTML;
      input.value = change_input_value;
    } else if (type == "visible") {
      visible_value.innerHTML = change_visible_value.innerHTML;
    }
  }
});

/**
 * Close all when clicked outside.
 */
document.addEventListener("click", function (e) {
  const target = e.target;
  const selects = document.querySelectorAll("mselect");

  if (!target.matches("mselect") && !target.closest("mselect")) {
    /**
     * Close the dropdown
     */
    selects.forEach((element) => {
      element.querySelector("mselect-dropdown").removeAttribute("active");
      element.querySelector("mselect-dropdown").removeAttribute("style");
      element.querySelector("[get-size]").removeAttribute("active");
    });
  }
});

/**
 * ? Old select model
 * Open a select model's dropdown and close all others.
 */
Utils.delegate(document, "click", "select-model", function (e) {
  const selects = document.querySelectorAll("select-model");
  const current_select = this;
  let dropdown = this.querySelector("[select-model-dropdown]");
  let dropdown_height = dropdown.querySelector("[get-height]").clientHeight;
  let option;
  let value = this.querySelector("[select-model-value]");
  let input = this.querySelector("input");
  let input_value;

  let is_only_select = this.hasAttribute("only-select");
  let is_join_values = this.hasAttribute("join-values");
  let is_option =
    e.target.matches(".select_model__dropdown_option") ||
    (e.target.closest(".select_model__dropdown_option") &&
      e.target
        .closest(".select_model__dropdown_option")
        .matches(".select_model__dropdown_option"));

  /**
   * Close all other dropdowns
   */
  selects.forEach((select) => {
    select.removeAttribute("active");
    select.querySelector("[select-model-dropdown]").removeAttribute("style");
  });

  /**
   * Evaluate if the clicked element is an option inside the
   * select model and close this if true.
   */
  if (is_option) {
    option = e.target.matches("[select-model-dropdown-option]")
      ? e.target
      : e.target.closest("[select-model-dropdown-option]");

    let has_data_value = option.hasAttribute("data-value");

    e.target.closest("select-model").removeAttribute("active");
    e.target.closest("[select-model-dropdown]").removeAttribute("style");

    /**
     * Replace the text shown in the select with the text shown of
     * the option that has been clicked. It considers about a
     * data-value attribute being present on the target element
     * and uses this instead of the innerHTML.
     */
    if (!is_only_select) {
      value.innerHTML = option.innerHTML;
      if (input) {
        if (has_data_value) input.value = option.dataset.value;
        else input.value = option.innerHTML;
      }
    }

    /**
     * Join all values
     *
     * When clicked on the options inside the select model, it
     * will join the values into the input inside it seperated by
     * a comma.
     */
    if (is_join_values) {
      value = option.getAttribute("value");
      input_value = input.value;
      let input_split_values = input_value.split(",");
      let should_add = true;

      input_split_values.forEach((val) => {
        if (val === value) {
          should_add = false;
        }
      });

      if (should_add)
        input.value = input_value ? `${input_value},${value}` : value;
    }

    /**
     * If the clicked element is the select mdoel itself, it
     * should open it
     */
  } else {
    if (current_select.hasAttribute("active")) return;

    this.setAttribute("active", true);
    dropdown.style.height = dropdown_height + "px";
  }
});

/**
 * Close all dropdowns when clicked outside.
 */
$(document).on("click", function (e) {
  const target = e.target;
  const selects = document.querySelectorAll("select-model");

  if (!target.matches("select-model") && !target.closest("select-model")) {
    selects.forEach(function (element) {
      element.removeAttribute("active");
      element.querySelector("[select-model-dropdown]").removeAttribute("style");
    });
  }
});
