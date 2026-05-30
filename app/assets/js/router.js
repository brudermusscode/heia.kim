import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";

export const APP_NAME = "heia.kim";
export const SCRIPT = "/assets/javascript";

export const routes = {
  "not-found": {},

  download: {
    mark: "download",
    disguised: true,
    hide_header: true,
  },

  unlock: {
    hide_header: true,
    disguised: true,
  },

  connect: {
    hide_header: true,
    // disguised: true,
  },

  reconnect: {
    hide_header: true,
    // disguised: true,
  },

  home: {
    hide_header: true,
    is_main_page: true,
    execute_once: () => {
      const words = ["Circles", "Drums", "Fruits", "Pianos", "You"];
      const textContainer = document.querySelector(".text-container");
      let currentWord = 0;
      let currentLetter = 0;
      let timer;

      function typeWord() {
        if (!textContainer) return;

        const word = words[currentWord];
        textContainer.textContent = word.slice(0, currentLetter + 1);
        currentLetter++;

        if (currentLetter < word.length) {
          timer = setTimeout(typeWord, 120); // Time between each letter appearance
        } else {
          setTimeout(eraseWord, 2000); // Time before starting to erase the word
        }
      }

      function eraseWord() {
        if (!textContainer) return;

        const word = words[currentWord];
        textContainer.textContent = word.slice(0, currentLetter);

        if (currentLetter > 0) {
          currentLetter--;
          timer = setTimeout(eraseWord, 100); // Time between each letter disappearance
        } else {
          currentWord = (currentWord + 1) % words.length;
          setTimeout(typeWord, 200); // Time before starting to type the next word
        }
      }

      typeWord();
    },
  },

  "": {
    hide_header: true,
    mark: "home",
    is_main_page: true,
    execute: async () => {
      routes.home.execute();
    },
  },

  login: {
    hide_header: true,
    disguised: true,
    execute_once: async () => {
      let shift_title = document.body.querySelector(".login__title");
      if (shift_title) Utils.shift_text(shift_title, 3000);
    },
  },

  register: {
    hide_header: true,
    disguised: true,
  },

  "password-reset": {
    disguised: true,
    hide_header: true,
  },

  begin: {
    hide_header: true,
  },

  legal: {
    disguised: true,
    hide_header: true,
  },

  leaderboard: {
    mark: "leaderboard",
    is_main_page: true,
  },

  beatmaps: {
    mark: "beatmaps",
    is_main_page: true,
  },

  "beatmap-set": {
    mark: "beatmaps",
    is_main_page: true,
  },

  artists: {
    is_main_page: true,
  },

  artist: {
    mark: "artists",
    is_main_page: true,
  },

  squads: {
    is_main_page: true,
  },

  squad: {
    mark: "squads",
    hide_header: true,
  },

  scores: {
    is_main_page: true,
  },

  score: {},

  u: {
    hide_header: true,
  },

  my: {
    // hide_header: true,
    // execute_once: (url) => {
    //   let loading_extras = document.find("loading-extras");
    //   document.find("main").setAttribute("my", "");
    //   $.get("/ui/my/sub-menu", (data) => {
    //     loading_extras.innerHTML = data.data;
    //     Frontend.reload_images();
    //     Frontend.activate_current_anchor(url);
    //   });
    // },
  },

  editor: {
    hide_header: true,
  },

  manage: {
    body_attribute: "my",
    execute: () => {
      document.find("main").setAttribute("my", "");
    },
  },
};

export const router = (route) => {
  let match;
  let matches_any = route in routes ? true : null;

  // Check for any matches or fallback to not_found.
  match = !matches_any ? routes["not-found"] : routes[route];

  // Append the key.
  match["key"] = route;

  return match;
};
