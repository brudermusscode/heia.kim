import * as Cookie from "./cookies";
import * as Page from "./page";
import * as Utils from "./utils";
import * as Frontend from "./frontend";
import * as Responder from "./elements/responder";

Utils.delegate(document, "click", "[more-menu]", async function (e) {
  this.querySelector("[data-react='more-menu']").setAttribute("active", "");
});
