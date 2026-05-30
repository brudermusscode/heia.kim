/**
 * Vendor
 */
const axios = require("axios");
const jsCookie = require("js-cookie");

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Base
 */
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

/**
 * Elements
 */
import { Header } from "./elements/header";
import Overlay from "./elements/Overlay";
import * as Responder from "./elements/responder";
import * as GlobalSearch from "./elements/global-search";
import * as MSelect from "./elements/mselect";
import * as ProfileEditor from "./elements/ProfileEditor";
import * as PostingMachine from "./elements/PostingMachine";

/**
 * Pages
 */
import * as Artists from "./pages/artists";
import * as Beatmaps from "./pages/beatmaps";
import * as Legal from "./pages/legal";
import * as My from "./pages/my";
import * as Register from "./pages/register";
import * as Squads from "./pages/squads";
import * as Users from "./pages/users";
import * as Premuim from "./pages/premium";
import * as Authentication from "./pages/authentications";
import * as Thread from "./pages/threads";

/**
 * Vendor
 */
import * as Vendor from "./vendor/vendor";
import * as Discord from "./vendor/discord";
import * as Google from "./vendor/google";
import * as Osu from "./vendor/osu";

/**
 * SCSS
 */
import "../scss/application.prod.scss";
