import * as Frontend from "../frontend";
import * as Request from "../requests";

export default class Overlay {
  constructor(data = null) {
    let overlay = document.createElement("overlay");
    let loader = `
    <overlay-loader class="linear-progress-material" in-overlay>
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </overlay-loader>`;

    document.body.setAttribute("toggled", true);
    document.body.prepend(overlay);
    overlay.insertAdjacentHTML("afterbegin", loader);

    this.overlay = overlay;
    this.loader = overlay.find("overlay-loader");

    __page.overlay = this;

    // Need a timeout, or the fade-in animation is not being played.
    setTimeout(() => {
      this.overlay.setAttribute("visible", true);
      this.loader.setAttribute("visible", true);

      if (data) this.append(data);
    }, 100);
  }

  /**
   * Appends given HTML to the overlay, hides the loader and
   * reloads all images.
   */
  append(data) {
    // A timeout to show all animations.
    setTimeout(() => {
      this.loader.setAttribute("visible", "false");
      this.overlay.insertAdjacentHTML("afterbegin", data);
      this.overlay.find("[autofocus]")?.focus();
      Frontend.reload_images();
      Frontend.slide_in_prompt(this.overlay.find("[prompt]"));
    }, 200);
  }

  /**
   * Remove the overlay.
   */
  delete() {
    this.overlay.setAttribute("visible", false);
    document.body.setAttribute("toggled", false);

    __page.overlay = null;

    setTimeout(() => {
      this.overlay.remove();
    }, 200);
  }
}
