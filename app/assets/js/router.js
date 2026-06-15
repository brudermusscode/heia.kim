export const APP_NAME = "heia.kim";
export const SCRIPT = "/assets/javascript";

export const routes = {
  "not-found": {},
  download: {},
  unlock: {},
  connect: {},
  reconnect: {},
  login: {},
  register: {},
  "password-reset": {},
  begin: {},
  legal: {},
  manage: {},

  home: {
    mark: "home",
    page_navigator: "home",
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
    mark: "home",
    page_navigator: "home",
    execute: async () => {
      routes.home.execute();
    },
  },

  leaderboard: {
    mark: "leaderboard",
    page_navigator: "home",
  },

  beatmaps: {
    mark: "beatmaps",
    page_navigator: "home",
  },

  "beatmap-set": {
    mark: "beatmaps",
    page_navigator: "home",
  },

  artists: {
    page_navigator: "home",
  },

  artist: {
    mark: "artists",
    page_navigator: "home",
  },

  squads: {
    mark: "squads",
    page_navigator: "home",
  },

  squad: {
    mark: "squads",
    page_navigator: "squad",
  },

  u: {
    page_navigator: "user",
  },

  editor: {
    page_navigator: "editor",
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
