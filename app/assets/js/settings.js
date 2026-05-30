let Cookie = require("js-cookie");

/**
 * If logs should be send to the console or not.
 */
export const LOG = true;

/**
 * A timeout, to show the loading screen longer.
 */
export const INIT_TIMEOUT = 1;

/**
 * The time, general CSS animations will take to finish.
 */
export const ANIMATION_TIME = 610;

/**
 * A number specified to delay the showing of the page navigator.
 */
export const PAGE_NAVIGATOR_SHOW_DELAY = 20;

/**
 * The max width of the window which will hide the user menu when
 * opening another UI element that fits to the bottom.
 */
export const HIDE_USER_MENU_MAX_WIDTH = 1800;

/**
 * Determine, if a cookie is set and if the boolean
 * value of it is true.
 */
export const cookie_enabled = (name) => {
  let cookie = Cookie.get(name);
  return (cookie !== undefined && cookie == 1) || cookie === undefined;
};
