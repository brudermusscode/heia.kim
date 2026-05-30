import * as Responder from "../elements/responder.js";
import * as Frontend from "../frontend.js";
import * as Page from "../page.js";

const stop_editor_mode = () => {
  let editor_save = document.find("[editor-save]");
  let editor_overlays = document.querySelectorAll("editor-overlay");
  let mode_button = document.find("mode-menu");

  editor_overlays.forEach((overlay) => {
    overlay.unactivate();
  });

  mode_button?.unset_inactive();
  editor_save?.unset_loading();
};

/**
 * ? Dragging/Repositioning Elements
 */

let __dragging = {
  enabled: false,
  object: null,
  placeholder: null,
  column: null,
  starting_position: null,
  can_drop: false,
  droppable_areas: null,
  current_droppable_area: null,
};

let __is_mousedown_on_draggable_object = false;
let __is_mousemove_inside_droppable = false;

$(function () {
  /**
   * Disable tabs.
   */
  $(document).on("click", "[pn-option]", function (e) {
    if (
      this.disabled() ||
      !window.location.pathname.startsWith("/editor") ||
      !this.find("input[type=hidden]")
    )
      return;

    let off = this.hasAttribute("turned-off");

    off
      ? this.removeAttribute("turned-off")
      : this.setAttribute("turned-off", "");
    this.find("input[type=hidden]").value = off ? "1" : "0";
  });

  /**
   * Starting mousedown.
   */
  $(document).on("mousedown", "[editor-object=draggable]", function (e) {
    if (e.button === 2) return;

    __dragging.object = this;
    __dragging.starting_position = this.getBoundingClientRect();
    __dragging.column = this.closest("[editor-column]");
    __dragging.can_drop = true;
    __dragging.droppable_areas = document.find_all("[editor-object=droppable]");
    __dragging.current_droppable_area = this.closest(
      "[editor-object=droppable]"
    );

    document.body.setAttribute("disable-user-selection", "");

    __is_mousedown_on_draggable_object = true;
  });

  const __check_can_drop_interval = 4;
  let __check_can_drop_counter = 0;
  let __just_started_dragging = true;

  /**
   * Moving, when mousedown.
   */
  $(document).on("mousemove", function (e) {
    if (!__dragging.object || !__is_mousedown_on_draggable_object) return;

    if (__just_started_dragging) {
      __dragging.object.setAttribute("can-drop", true);
    }

    __just_started_dragging = false;

    let h = __dragging.object.clientHeight;
    let w = __dragging.object.clientWidth;
    let mouseX = e.pageX - document.documentElement.scrollLeft;
    let mouseY = e.pageY - document.documentElement.scrollTop;
    let is_mousemove_inside_droppable = false;
    let current_drop_area_size = null;

    __dragging.object.style.left = `${mouseX - w / 2}px`;
    __dragging.object.style.top = `${mouseY - h / 2}px`;

    if (__dragging.enabled) {
      if (__check_can_drop_counter === __check_can_drop_interval) {
        __check_can_drop_counter = 0;

        __dragging.droppable_areas.forEach(function (area) {
          let area_position = area.getBoundingClientRect();
          if (
            mouseX < area_position.right &&
            mouseX > area_position.left &&
            mouseY < area_position.bottom &&
            mouseY > area_position.top
          ) {
            __dragging.current_droppable_area = area;
            is_mousemove_inside_droppable = true;
            current_drop_area_size =
              area.closest("[editor-column]").getAttribute("editor-column") ==
              "small"
                ? 0
                : 1;
          }
        });

        let draggable_area_size =
          __dragging.object
            .closest("[editor-column]")
            .getAttribute("editor-column") == "small"
            ? 0
            : 1;
        let column_fits_current_draggable_object =
          draggable_area_size === current_drop_area_size;

        if (
          is_mousemove_inside_droppable &&
          column_fits_current_draggable_object &&
          !__dragging.can_drop
        ) {
          __dragging.object.setAttribute("can-drop", true);
          __dragging.can_drop = true;
        } else if (
          (!is_mousemove_inside_droppable ||
            !column_fits_current_draggable_object) &&
          __dragging.can_drop
        ) {
          __dragging.object.setAttribute("can-drop", false);
          __dragging.can_drop = false;
          __dragging.current_droppable_area = null;
        }
      }

      __check_can_drop_counter++;

      /**
       * Don't run the below code if we are dragging.
       */
      return;
    }

    __dragging.enabled = true;

    __dragging.column.activate();

    let placeholder = document.createElement("box-model");
    placeholder.setAttribute("filled", "darker");
    placeholder.setAttribute("rounded", "mid");
    placeholder.style.height = h + "px";
    placeholder.style.width = w + "px";

    __dragging.placeholder = placeholder;

    let parent = __dragging.object.parentElement;
    parent.insertBefore(placeholder, __dragging.object);

    __dragging.object.style.height = h + "px";
    __dragging.object.style.width = w + "px";
    __dragging.object.setAttribute("dragging", "");
  });

  /**
   * Releasing.
   */
  $(document).on("mouseup", function (e) {
    /**
     * Reset global variables.
     */
    __check_can_drop_counter = 0;
    __is_mousedown_on_draggable_object = false;
    __is_mousemove_inside_droppable = false;
    __just_started_dragging = true;

    if (!__dragging.enabled) return;

    if (
      __dragging.current_droppable_area &&
      __dragging.can_drop &&
      __dragging.current_droppable_area !==
        __dragging.object.closest("[editor-object=droppable]")
    ) {
      let area_dragging = __dragging.object.parentElement;
      let area_dropped_element = __dragging.current_droppable_area.find(
        "[editor-object=draggable]"
      );

      area_dragging.insertBefore(area_dropped_element, __dragging.object);
      __dragging.current_droppable_area.prepend(__dragging.object);
    }

    document.body.removeAttribute("disable-user-selection");

    setTimeout(() => {
      __dragging.object.removeAttribute("can-drop");
      __dragging.column.unactivate();
      __dragging.object.removeAttribute("dragging");
      __dragging.object.removeAttribute("style");
      __dragging.placeholder.remove();

      /**
       * Reset all keys of the dragging object.
       */
      __dragging.enabled = false;
      __dragging.object = null;
      __dragging.placeholder = null;
      __dragging.column = null;
      __dragging.starting_position = null;
      __dragging.can_drop = false;
      __dragging.droppable_areas = null;
      __dragging.current_droppable_area = null;
    }, 10);
  });

  /**
   * Serializes the object's input values to belong to their column
   * by adding the column id.
   */
  $(document).on("click", '[data-action="profiles:edit"]', function (e) {
    e.preventDefault();

    let button = this;
    let form = this.closest("form");
    let columns = form.find_all("[editor-column]");

    button.activate();
    button.set_loading();

    columns.forEach((column) => {
      let column_id = column.getAttribute("editor-column-id");
      let objects = column.find_all("input[type=hidden]");

      objects.forEach((object) => {
        let object_name = object.name;
        object.name = object_name.replace("[]", `[${column_id}]`);
      });
    });

    form.find("[submit-closest]").click();
  });

  /**
   * @action UPDATE
   * @controller ProfilesController
   */
  $(document).on("submit", '[data-form="profiles:edit"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let submit_button = this.find("[submit-closest]");
    let button = this.find("[data-action='profiles:edit']");

    button.activate();
    button.set_loading();

    $.ajax({
      url: "/profile/update",
      method: "POST",
      dataType: "JSON",
      data: formdata,
      processData: false,
      contentType: false,
      success: function (data) {
        button.unactivate();
        button.unset_loading();

        if (data.status && __current_user.id) {
          Page.get(`/u/${__current_user.id}`);
          stop_editor_mode();
        }

        Frontend.create_responder(data);
      },
      error: function (error) {
        button.unset_loading();
        button.unactivate();
        Frontend.ajax_error(error);
      },
    });
  });
});
