const axios = require("axios");
const jsCookie = require("js-cookie");

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import * as App from "./application";
import * as Settings from "./settings";
import * as Router from "./router";
import * as Request from "./requests";
import * as Audio from "./audio";
import * as Body from "./body";
import * as Cookie from "./cookies";
import * as Frontend from "./frontend";
import * as Jobs from "./jobs";
import * as Notifications from "./notification";
import * as Page from "./page";
import * as Relationships from "./relationships";
import * as Report from "./report";
import * as Session from "./session";
import * as URL from "./url";
import * as Utils from "./utils";
import * as Window from "./window";
import * as Comments from "./comments";
import * as HTMLElements from "./HTMLElements";
import * as Updates from "./updates";
import * as Feedback from "./feedback";

import * as Element from "./elements";

import * as Page from "./pages";

// Stylesheet entrypoint.
import "../scss/application.scss";
