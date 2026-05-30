export class Header {
  get() {
    return document.querySelector("header[main]");
  }

  /**
   * Toggles the header.
   *
   * Evaluates, if the header has class of toggled already and
   * removes it correspondingly. Also adds or removes an attribute
   * "normalize" to the main container.
   */
  toggle() {
    if (this.get() && this.get().hasAttribute("toggled")) {
      this.get().removeAttribute("toggled");
      document.body.querySelector("main").removeAttribute("normalize");
    } else {
      this.get().setAttribute("toggled", true);
      document.body.querySelector("main").setAttribute("normalize", "");
    }
  }

  is_toggled() {
    return this.get() && this.get().hasAttribute("toggled");
  }

  get_background() {
    return this.get()
      ? window.getComputedStyle(this.get(), null).getPropertyValue("background")
      : "";
  }
}
