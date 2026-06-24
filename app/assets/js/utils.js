export const removeAttributes = (element, ...attrs) => {
  attrs.forEach((a) => {
    element.removeAttribute(a);
  });
};

/**
 * Let's the application sleep a little. Everyone needs some time to rest,
 * even applications on the internet :)
 */
export const sleep = async (ms) => {
  // console.log("%cSleeping a little...", "color:#c5cae9;");
  return new Promise((resolve) => setTimeout(resolve, ms));
};

/**
 * Gets the meta tag from CSRF token inside of the head element of the DOM
 * and returns it's value
 */
export const get_csrf_token = () => {
  return document.querySelector("meta[name=csrf_token]").getAttribute("content");
};

export const serialize_cookie_domain = () => {
  const domain = window.location.hostname;
  const cleanDomain = domain.replace(/^www\./i, "");

  return cleanDomain;
};

export const shift_text = async (elem, timeout) => {
  let the_interval;

  if (!elem) {
    clearInterval(the_interval);
    return;
  }

  the_interval = setInterval(async () => {
    let first_elem = elem.firstElementChild;

    await elem.firstElementChild.remove();
    await elem.append(first_elem);
    setTimeout(() => {
      elem.lastElementChild.classList.add("visible");
    }, 20);

    setTimeout(() => {
      elem.lastElementChild.classList.remove("visible");
    }, timeout);
  }, 3200);
};

export const clear_all_intervals = () => {
  const interval_id = window.setInterval(function () {}, Number.MAX_SAFE_INTEGER);

  for (let i = 1; i < interval_id; i++) {
    window.clearInterval(i);
  }
};

export const autofocus = (elem) => {
  const input = elem.querySelector("[autofocus]");

  if (input) {
    console.log("y");
    input.focus();
  }
};

export const delegate = (el, evt, sel, handler) => {
  el.addEventListener(evt, function (event) {
    var t = event.target;
    while (t && t !== this) {
      if (t.matches(sel)) {
        handler.call(t, event);
      }
      t = t.parentNode;
    }
  });
};

export const random = (audios) => {
  var rand = Math.floor(Math.random() * audios.length);

  return audios[rand];
};
