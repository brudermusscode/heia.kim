import * as Utils from "./utils.js";
import * as Frontend from "./frontend.js";

export const play = (sound) => {
  let audio = document.body.querySelector(sound);

  if (!audio) return;

  audio.play();
  audio.volume = 0.38;

  audio.onended = () => {
    // audio.pause();
    audio.currentTime = 0;
  };
};

export const stop = (sound) => {
  let audio = document.body.querySelector(sound);

  if (!audio) return;

  audio.pause();
  audio.currentTime = 0;
};

const __remove_audio_player_timeout_time = 30000;
let __remove_audio_player_timeout;

/**
 * Audio player start
 */
export const start = async (id) => {
  let set_id = id;
  let url = "/ui/audio_player";
  let appendix = document.body
    .querySelector("app")
    .querySelector("menu-js-content");
  let add_active;
  let audio_player = appendix.querySelectorAll("audio-player");
  let audio;
  let audio_player_controls;

  Frontend.load();

  await axios.get(url + "?set_id=" + set_id).then((data) => {
    /**
     * Remove all existing audio players from appendix list
     */
    if (audio_player[0]) {
      audio_player.forEach((ap) => {
        ap.remove();
      });
    }

    // clear timeout for removing audio player
    clearTimeout(__remove_audio_player_timeout);

    Frontend.unload();

    if (data.data) {
      appendix.querySelector("audio-player");
      appendix.innerHTML = data.data;
      audio_player = appendix.querySelector("audio-player");
      add_active = audio_player.querySelectorAll("[add-active]");
      audio_player_controls = audio_player.querySelector(
        "[audio-player-controls]"
      );

      setTimeout(() => {
        add_active.forEach((a) => {
          a.setAttribute("active", true);
        });

        audio = appendix.querySelector("audio");
        audio.volume = 0.38;
        audio.play();

        __current_audio_element = audio;

        __remove_audio_player_timeout = setTimeout(() => {
          audio_player.style.opacity = 0;

          setTimeout(() => {
            audio_player.remove();
          }, 200);
        }, __remove_audio_player_timeout_time);

        setTimeout(() => {
          add_active.forEach((a) => {
            a.removeAttribute("active");
          });
        }, 4000);

        audio.onended = () => {
          audio.pause();
          audio.currentTime = 0;
          audio_player_controls
            .querySelector('[control="pause"]')
            .setAttribute("active", false);

          let play_buttons = document.querySelectorAll(
            '[data-action="beatmaps:set,play"]'
          );

          play_buttons.forEach((button) => {
            button.removeAttribute("active");
          });
        };
      }, 200);
    } else {
      new Responder.Responder().add(
        document.body,
        "Could not load map audio. Try again"
      );
    }
  });
};

/**
 * Audio player controls
 */
Utils.delegate(document, "click", "[audio-player-control]", function (e) {
  let audio_player = this.closest("audio-player");
  let audio = audio_player.querySelector("audio");

  clearTimeout(__remove_audio_player_timeout);

  if (this.getAttribute("control") === "pause") {
    if (this.getAttribute("active") === "true") {
      this.setAttribute("active", false);
      audio.pause();
    } else {
      audio.play();
      this.setAttribute("active", true);
    }
  }

  __remove_audio_player_timeout = setTimeout(() => {
    audio_player.style.opacity = 0;

    setTimeout(() => {
      audio_player.remove();
    }, 200);
  }, __remove_audio_player_timeout_time);
});
