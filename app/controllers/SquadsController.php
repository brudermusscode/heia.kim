<?php

namespace Heiakim\Controller;

use Heiakim\Model\Squad;
use Heiakim\Controller\Controller;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Utils\Str;
use Illuminate\Support\Facades\DB;

class SquadsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["tag", "name", "joinable",  "osu", "taiko", "mania", "ctb"],
    );

    $this->authorize();

    /**
     * @var Squad
     */
    $Squad = !CurrentUser->squad ? Squad::make() : die(error("!HAS_SQUAD"));
    $Squad->owner = CurrentUser->id;

    # ? Joinable
    $Squad->joinable = in_array($this->params->joinable, Squad::$joinable_map)
      ? (
        $this->params->joinable ? 2 : 1
      )  : 1;

    # ? Name
    $Squad->set_name_invalid($this->params->name);

    # ? Tag
    $Squad->set_tag_invalid($this->params->tag);

    # ? Modes
    $Squad->modes = [
      "osu" => $this->params->osu ? 1 : 0,
      "taiko" => $this->params->taiko ? 1 : 0,
      "mania" => $this->params->mania ? 1 : 0,
      "ctb" => $this->params->ctb ? 1 : 0,
    ];

    $Squad->save();

    # Create The SquadUser and set their performance contribution.
    $Squad->members()
      ->create([
        "user_id" => CurrentUser->id,

        # Sum up all privileges the Chief should have.
        "clan_priv" => SquadPrivilege::CHIEF->value
          + SquadPrivilege::MEMBER->value
          + SquadPrivilege::UNRESTRICTED->value,
      ])
      ->update_performance();

    # Updating performance of Squad after creating the SquadUser, as it will be cal-
    # culated from the SquadUser's performances.
    $Squad->update_performance();
    // $Squad->cache_performance();

    # Set the new squad id to the user as the game server requires this field.
    CurrentUser->update([
      "clan_id" => $Squad->id
    ]);

    # Delete all left over squad requests as the User should not join another Squad
    # after creating a new one.
    CurrentUser->squad_requests()
      ->delete();

    CurrentUser->reload_session();

    $Squad->logs()->create([
      "user_id" => CurrentUser->id,
      "type" => "__squad__/created",
    ]);

    return success("Welcome to »<a extern target='_blank' href='/squad/$Squad->id' sub>($Squad->tag) $Squad->name &nbsp; <i class='ri-link-unlink'></i></a>«! 🥰");
  }

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: [],
      optional: ["name", "owner", "leave", "notification", "tag", "joinable", "osu", "taiko", "mania", "ctb", "image_type", "MAX_FILE_SIZE", "files"],
    );

    # We need an image type if the any picture wants to be changed here.
    if (isset($this->params->files) && !isset($this->params->image_type))
      return error();

    $this->authorize(
      resource: CurrentUser?->squad_user,
      can: ["manage", "squad"],
    );
  }
}
