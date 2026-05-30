import * as Page from "../page";
import * as Cookie from "../cookies";
import * as Utils from "../utils";
import * as Responder from "../elements/responder";

let __consent_step_loading = false;

/**
 * Steps for policies consent space tour
 */
Utils.delegate(
  document,
  "click",
  "[action=policies-consent-prompt]",
  async function (e) {
    let consent_step = Cookie.get("POLICIES_CONSENT_STEP");
    let prompt = document.body.querySelector("[prompt=policies-consent]");
    let steps = prompt.querySelectorAll(
      "box-model:not(.spacer):not(.changed-mind)"
    );
    let spacer = prompt.querySelector(".spacer");
    let step_count = steps.length;
    let current_step;
    let next_step;

    if (consent_step == "true" || consent_step == "false") return;

    if (parseInt(consent_step) === step_count - 2) {
      return;
    }

    if (parseInt(consent_step) === step_count - 3) {
      prompt.querySelector("[more]").style.opacity = "0";
      prompt.querySelector("[more]").style.visibility = "hidden";
    }

    if (!__consent_step_loading) {
      __consent_step_loading = true;

      if (!consent_step) {
        Cookie.set("POLICIES_CONSENT_STEP", 1, 365);
        current_step = steps[0];
        next_step = steps[1];
      } else {
        current_step = steps[parseInt(consent_step)];
        next_step = steps[parseInt(consent_step) + 1];
        Cookie.set("POLICIES_CONSENT_STEP", parseInt(consent_step) + 1, 365);
      }

      current_step.removeAttribute("visible");
      await Utils.sleep(600);
      spacer.innerHTML = next_step.innerHTML;
      next_step.setAttribute("visible", "");
      next_step.style.height =
        next_step.querySelector("bm-inr").clientHeight + "px";
      await Utils.sleep(400);
      next_step.style.height = "auto";

      __consent_step_loading = false;
    }
  }
);

/**
 * Give consent or decline
 *
 * When done with the awesome space tour, the user will be asked
 * for their privacy consent. This is handled by this function.
 */
Utils.delegate(
  document,
  "click",
  "[action='policies-consent-prompt,decide'] buttonmon",
  async function (e) {
    let data = new FormData();
    let decision = this.dataset.letAction === "accept" ? 1 : 0;

    data.append("accepts_policies", decision);

    axios.post("/legal/give_consent", data).then(async (data) => {
      if (data.data.status) {
        Page.get("/legal/" + Cookie.get("LEGAL_LANG"));
        Cookie.set("POLICIES_CONSENT_STEP", decision ? true : false, 365);
      }

      new Responder.Responder().add(
        document.body,
        data.data.message,
        data.data.status ? "success" : "error",
        "privacy"
      );
    });
  }
);
