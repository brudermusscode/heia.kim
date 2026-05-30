import * as Responder from "./elements/responder.js";
import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";

const JOBS_PATH = "/jobs/";

/**
 * Execute a job.
 */
Utils.delegate(document, "click", '[data-action="jobs:execute"]', function (e) {
  execute_job(this.dataset.url);
});

/**
 * Executes a job given by url.
 */
const execute_job = (url) => {
  let str_split = url.split("/");
  let name = str_split[str_split.length - 1].replace(/_/g, " ");

  new Responder.Responder().add(
    document.body,
    `Job <strong>${name}</strong> has been started...`,
    "success",
    "jobs"
  );

  Frontend.load();

  axios.post(JOBS_PATH + url + ".php").then((data) => {
    Frontend.unload();

    new Responder.Responder().add(
      document.body,
      data.data.message,
      data.data.status ? "success" : "error",
      "jobs"
    );
  });
};
