/**
 * Init ripple effect elements
 *
 * Queries the DOM for all buttons from element type `mbutton` and
 * adds an event listener on mouse leave to it. This way we can
 * add a ripple effect to it.
 */
export const init = () => {
  const __material_buttons = document.querySelectorAll("[ripple-effect]");

  __material_buttons.forEach((button) => {
    button.addEventListener("mouseleave", (e) => {
      remove_material_button_ripple(e.target);
    });
  });
};

/**
 * Remove ripple effect
 */
export const remove_material_button_ripple = (target) => {
  let ripple = target.querySelector(".ripple-effect");
  let interval;

  if (ripple) {
    interval = setInterval(() => {
      if (__material_button_ripple_effect_done) {
        clearInterval(interval);

        ripple.setAttribute("active", "false");

        setTimeout(() => {
          ripple.remove();
        }, __material_button_ripple_effect_remove_interval);
      }
    }, 100);
  }
};

$(function () {
  /**
   * Creates a new element of class `.ripple-effect` and adds it to
   * the material button. We want the animation to finish, so we set
   * a timeout and set the effects_done variable beanth to true,
   * when the timeout has finished.
   */
  $(document).on("mousedown", "[ripple-effect]", function (e) {
    let button = this;
    let rect = button.getBoundingClientRect();
    let offsetX = e.clientX - rect.left;
    let offsetY = e.clientY - rect.top;
    let computedStyle = getComputedStyle(button);
    let borderRadius = computedStyle.getPropertyValue("border-radius");

    if (this.querySelector(".ripple-effect")) return;

    __material_button_ripple_effect_done = false;

    /**
     * Create the ripple element and set it's attributes to be shown
     * at the clicked position.
     */
    let ripple = document.createElement("span");

    ripple.classList.add("ripple-effect");
    ripple.style.top = `${offsetY}px`;
    ripple.style.left = `${offsetX}px`;

    if (borderRadius == "50%") ripple.style.borderRadius = "50%";
    else ripple.style.borderRadius = "200px";

    this.insertBefore(ripple, this.firstChild);

    setTimeout(() => {
      ripple.setAttribute("active", true);
      ripple.style.top = "-.6px";
      ripple.style.left = "-.6px";

      if (borderRadius != "50%") ripple.style.borderRadius = borderRadius;

      setTimeout(() => {
        __material_button_ripple_effect_done = true;
      }, 400);
    }, 10);
  });

  /**
   * Removes the ripple effect from a button.
   * Respects the effects_done variable on top.
   */
  $(document).on("mouseup", "[ripple-effect]", function (e) {
    let ripple = this.querySelector(".ripple-effect");

    if (ripple && __material_button_ripple_effect_done) {
      ripple.setAttribute("active", "false");

      setTimeout(() => {
        ripple.remove();
      }, 400);
    }
  });
});
