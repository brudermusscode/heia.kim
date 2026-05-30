import * as Page from "../page";
import * as Frontend from "../frontend";
import * as Responder from "../elements/responder";

$(function () {
  /**
   * ? Squad
   */

  /**
   * Create squad
   *
   * @action CREATE
   * @controller SquadsController
   */
  $(document).on("submit", '[data-form="squad:create"]', function (e) {
    e.preventDefault();

    let button = this.querySelector("[submit-closest]");
    let formdata = new FormData(this);

    Frontend.load();
    button.disable();

    $.ajax({
      url: "/squad/create",
      data: formdata,
      method: "POST",
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          Page.get("/manage/squad");
        } else {
          button.enable();
        }

        new Responder.Responder().add(
          document.body,
          data.message,
          data.status ? "success" : "error"
        );
      },
    });
  });

  /**
   * ? SquadPost
   */

  /**
   * Create SquadPostPollAnswer
   *
   * @action CREATE
   * @controller SquadPostPollAnswersController
   */
  $(document).on(
    "click",
    '[data-action="squad:post:poll-answer:create"]',
    function (e) {
      e.preventDefault();

      let button = this;
      let formdata = new FormData();
      let post = this.closest("[post]");
      let id = post.dataset.id;
      let answer_key = this.dataset.answerKey;

      formdata.append("id", id);
      formdata.append("answer_key", answer_key);

      Frontend.load();
      post.disable();

      $.ajax({
        url: subst(this.dataset.action),
        data: formdata,
        method: "POST",
        success: function (data) {
          Frontend.unload();
          post.enable();

          if (data.status) {
            post.outerHTML = data.data;
            Frontend.reload_images();
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }
  );

  /**
   * Remove SquadPostPollAnswer
   *
   * @action DELETE
   * @controller SquadPostPollAnswersController
   */
  $(document).on(
    "click",
    '[data-action="squad:post:poll-answer:delete"]',
    function (e) {
      e.preventDefault();

      let button = this;
      let formdata = new FormData();
      let post = this.closest("[post]");
      let id = this.dataset.answerKey;

      formdata.append("id", id);

      Frontend.load();
      post.disable();

      $.ajax({
        url: subst(this.dataset.action),
        data: formdata,
        method: "POST",
        success: function (data) {
          Frontend.unload();
          post.enable();

          if (data.status) {
            post.outerHTML = data.data;
            Frontend.reload_images();
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }
  );

  /**
   * Create SquadPostComment
   *
   * @action CREATE
   * @controller SquadsPostCommentsController
   */
  $(document).on(
    "submit",
    '[request-do="squad/post/comment/create"]',
    function (e) {
      e.preventDefault();

      let button = this.querySelector("[submit-closest]");
      let formdata = new FormData(this);
      let post;
      let comments_count;

      Frontend.load();
      button.disable();

      $.ajax({
        url: "/" + this.getAttribute("request-do"),
        data: formdata,
        method: "POST",
        success: function (data) {
          Frontend.unload();

          if (data.status) {
            post = document.find(`[post][data-id="${data.id}"]`);
            post
              ?.find("[comments]")
              .insertAdjacentHTML("afterbegin", data.data);

            comments_count = post?.find("[comments-count]");
            if (comments_count) {
              comments_count.innerHTML = parseInt(comments_count.innerHTML) + 1;
            }

            Frontend.close_composer();
            Frontend.reload_images();
          } else {
            button.enable();
          }

          new Responder.Responder().add(
            document.body,
            data.message,
            data.status ? "success" : "error"
          );
        },
      });
    }
  );

  /**
   * Create SquadPostVote
   *
   * @action CREATE
   * @controller SquadPostVotesController
   */
  $(document).on(
    "click",
    '[data-action="squad:post:vote:create"]',
    function (e) {
      e.preventDefault();

      let button = this;
      let formdata = new FormData();
      let post = this.closest("[post]");
      let id = post.dataset.id;
      let vote_count = post.find("[vote-count]");
      let type = parseInt(this.dataset.type);
      let new_vote_count;
      let other_button;
      let other_button_type;

      formdata.append("id", id);
      formdata.append("type", type);

      button.disable();

      $.ajax({
        url: subst(this.dataset.action),
        data: formdata,
        method: "POST",
        success: function (data) {
          button.enable();

          if (data.status) {
            // Activate the current button.
            button.activate();
            button.dataset.action = "squad:post:vote:delete";

            // Unactivate the other vote button.
            other_button_type = button.dataset.type == 1 ? -1 : 1;
            other_button = vote_count
              ?.closest("[votes]")
              .find(`[data-type="${other_button_type}"]`);

            if (other_button) {
              console.log(other_button);
              other_button.unactivate();
              other_button.dataset.action = "squad:post:vote:create";
            }

            // Set the new vote count.
            // If the user has voted before and sets the other
            // vote button, it has to substract or add 2 the type.
            if (vote_count) {
              new_vote_count =
                parseInt(vote_count.innerHTML.replace(",", "")) +
                (data.has_voted ? type + type : type);
              vote_count.innerHTML = new_vote_count.toLocaleString("en-US");

              // Set vote count color.
              vote_count.removeAttribute("slighter");

              if (new_vote_count > 0) vote_count.setAttribute("color", "green");
              else if (new_vote_count < 0)
                vote_count.setAttribute("color", "red");
              else {
                vote_count.removeAttribute("color");
                vote_count.setAttribute("slighter", "");
              }
            }
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }
  );

  /**
   * Delete SquadPostVote
   *
   * @action DELETE
   * @controller SquadPostVotesController
   */
  $(document).on(
    "click",
    '[data-action="squad:post:vote:delete"]',
    function (e) {
      e.preventDefault();

      let button = this;
      let formdata = new FormData();
      let post = this.closest("[post]");
      let id = post.dataset.id;
      let type = parseInt(this.dataset.type);
      let vote_count = post.find("[vote-count]");
      let new_vote_count;

      formdata.append("id", id);

      button.disable();

      $.ajax({
        url: subst(this.dataset.action),
        data: formdata,
        method: "POST",
        success: function (data) {
          button.enable();

          if (data.status) {
            button.unactivate();
            button.dataset.action = "squad:post:vote:create";

            if (vote_count) {
              new_vote_count =
                parseInt(vote_count.innerHTML.replace(",", "")) - type;
              vote_count.innerHTML = new_vote_count.toLocaleString("en-US");

              // Set vote count color.
              vote_count.removeAttribute("slighter");

              if (new_vote_count > 0) vote_count.setAttribute("color", "green");
              else if (new_vote_count < 0)
                vote_count.setAttribute("color", "red");
              else {
                vote_count.removeAttribute("color");
                vote_count.setAttribute("slighter", "");
              }
            }
          } else {
            Frontend.create_responder(data);
          }
        },
      });
    }
  );

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * @controller SquadsController ,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * Update pictures
   *
   * @action UPDATE
   * @controller SquadsController
   */
  $(document).on("change", '[data-form="squad:update:image"]', function (e) {
    e.preventDefault();

    let button = this.querySelector("a");
    let formdata = new FormData(this);

    button.disable();
    Frontend.load();

    $.ajax({
      url: "/squad/update",
      data: formdata,
      method: "POST",
      success: function (data) {
        console.log(data);
        Frontend.unload();

        if (data.status) {
          Page.reload();
        }

        Frontend.create_responder(data);
        button.enable();
      },
    });
  });
});
