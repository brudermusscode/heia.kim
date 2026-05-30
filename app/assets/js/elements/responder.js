import * as Utils from "../utils.js";

let __responder_close_timeout;
let __responder_close_timeout_time = 8000;

export class Responder {
  /**
   * Finds a responder in the DOM and manipulates it with the
   * given .
   *
   * @param {HTMLElement} append
   * @param {string} message
   * @param {string} type
   * @returns
   */
  add(append, message, type = "std") {
    let array;
    let current_responder = append.querySelector("responder");

    // if (current_responder && !current_responder.hasAttribute("deleted")) {

    clearTimeout(__responder_close_timeout);

    return this.manipulate(current_responder, message, type);

    // } else {
    //   append.insertAdjacentHTML(
    //     "beforeend",
    //     `
    //     <responder ${type} fl alic>
    //       <div fl alic jucsb flexone>
    //         <p message text std>${message}</p>
    //         <mi close-responder>close</mi>
    //       </div>
    //     </responder>
    //     `
    //   );

    //   array = {
    //     responder: null,
    //     append: append,
    //   };

    //   responder = append.querySelector("responder:not([deleted])");

    //   setTimeout(() => {
    //     responder.activate();
    //   }, 20);

    //   __responder_close_timeout = setTimeout(() => {
    //     this.close(responder);
    //   }, __responder_close_timeout_time);

    //   array.responder = responder;
    //   return array;
    // }
  }

  /**
   * Manipulates an active responder instead of creating a new one.
   *
   * @param {HTMLElement} responder
   * @param {string} message
   * @param {string} type
   * @returns
   */
  manipulate(responder, message, type) {
    let h, w, inr, bg;
    let message_p = responder.find("[message]");

    responder.unactivate();
    responder.find("responder-bg").removeAttribute("style");

    setTimeout(() => {
      responder.setAttribute(type, true);
      message_p.innerHTML = message;

      inr = responder.find("responder-inr");
      bg = responder.find("responder-bg");
      h = inr.clientHeight;
      w = inr.clientWidth;

      responder.activate();

      setTimeout(() => {
        bg.style.height = h + "px";
        bg.style.width = w + "px";
      }, 200);
    }, 300);

    __responder_close_timeout = setTimeout(() => {
      this.close(responder);
    }, __responder_close_timeout_time);

    return true;
  }

  close(responder) {
    let array;
    let bg = responder.find("responder-bg");

    responder.unactivate();
    responder.setAttribute("deleted", true);
    bg.removeAttribute("style");

    setTimeout(() => {
      responder.removeAttribute("deleted");
      responder.removeAttribute("success");
      responder.removeAttribute("error");
    }, 400);

    array = {
      responder: responder,
    };

    return array;
  }
}
