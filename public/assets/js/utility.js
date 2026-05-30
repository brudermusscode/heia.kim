const is_numeric = (val) => {
  return !isNaN(val) && !isNaN(parseFloat(val));
};

const echo = (msg) => {
  console.log(msg);
};

const pdie = (msg) => {
  console.log(msg);
};

document.find = (selector) => {
  return document.querySelector(selector);
};

document.find_all = (selector) => {
  return document.querySelectorAll(selector);
};

Element.prototype.find = function (selector) {
  return this.querySelector(selector);
};

Element.prototype.find_all = function (selector) {
  return this.querySelectorAll(selector);
};

Element.prototype.disable = function (selector) {
  return this.setAttribute("disabled", "");
};

Element.prototype.disabled = function (selector) {
  return this.hasAttribute("disabled", "");
};

Element.prototype.enable = function (selector) {
  return this.removeAttribute("disabled");
};

Element.prototype.activate = function (selector) {
  return this.setAttribute("active", true);
};

Element.prototype.unactivate = function (selector) {
  return this.removeAttribute("active");
};

Element.prototype.deactivate = function (selector) {
  return this.removeAttribute("active");
};

Element.prototype.set_inactive = function (selector) {
  return this.setAttribute("inactive", "");
};

Element.prototype.unset_inactive = function (selector) {
  return this.removeAttribute("inactive");
};

Element.prototype.set_not_active = function (selector) {
  return this.setAttribute("active", false);
};

Element.prototype.set_loading = function (selector) {
  return this.setAttribute("loading", "");
};

Element.prototype.unset_loading = function (selector) {
  return this.removeAttribute("loading");
};

Element.prototype.unstyle = function (selector) {
  return this.removeAttribute("style");
};

Element.prototype.make_tag = function (selector) {
  this.setAttribute("tag", "");
  this.enable();
  this.removeAttribute("submit-closest");
  this.removeAttribute("confirm-button");
  this.removeAttribute("submit");
  return;
};

Element.prototype.is_activated = function (selector) {
  return this.hasAttribute("active");
};

Element.prototype.fade_out = function (selector) {
  return this.setAttribute("invisible", "");
};

HTMLDivElement.prototype.set_done = () => {
  this.setAttribute("done", "");
};

HTMLElement.prototype.set_done = () => {
  this.setAttribute("done", "");
};

const subst = (str) => {
  return "/" + str.replaceAll(":", "/");
};
