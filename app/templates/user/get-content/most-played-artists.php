<?php

use Heiakim\Model\User;
use Heiakim\Model\Artist;
use Heiakim\Model\Gamemode;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$gumode = filter_input(INPUT_GET, "gumode", FILTER_VALIDATE_INT) ?? 0;
$limit = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 24;

$fetch_limit = 7;

/**
 * Limit in range?
 */
if ($limit > 24 || $limit < 1)
  $limit = 24;

/**
 * Validate gumode.
 */
if (!in_array($gumode, Gamemode::$modes))
  $gumode = 0;

/**
 * @var object
 */
$mode_mod = Gamemode::convert_gumode_to_mode_mod($gumode);

/**
 * @var ?User
 */
$User = User::find($id);

/**
 * @var bool
 */
$is_my_profile = $User->is(CurrentUser);

/**
 * User doesn't exist?
 * ! Error
 */
if (!$User) :
  include GET_CONTENT_NOTHING;
else :

  /**
   * @var Artist
   */
  $Artists = Artist::join('maps', 'artists.name', '=', 'maps.artist')
    ->join('scores', 'scores.map_md5', '=', 'maps.md5')
    ->selectRaw('COUNT(scores.id) as counter, maps.*, artists.name as artist_name, artists.id as artist_id')
    ->where('scores.userid', $User->id)
    ->groupBy('maps.artist')
    ->orderByDesc('counter')
    ->get();

?>


  <div fl fldircol gap=smoler>
    <div fl jucsb alic>
      <div fl gap=smoler>
        <p text midler bold>Artists &middot; </p>
        <p text midler color=company><?= number_format($Artists->count()); ?></p>
      </div>
      <?php if ($Artists->count()) { ?>
        <mbutton material icon-only mr=smol hoverable>
          <mi size=midler>stars</mi>
        </mbutton>
      <?php } ?>
    </div>

    <?php if (!$Artists->count()) { ?>

      <box-model rounded=mid outlined p32 fl fldircol alic gap mt=smol>
        <div style="height:3.2em;width:3.2em;" fl alic jucc circled filled>
          <mi mid>artist</mi>
        </div>
        <div tac>
          <p text bold midler>Empty m8</p>
          <?php if ($is_my_profile) : ?>
            <a href="/beatmaps" normal text>Discover beatmaps</a>
          <?php endif; ?>
        </div>
      </box-model>


    <?php } else { ?>

      <div fl fldircol>
        <?php

        foreach ($Artists->take($limit) as $key => $Artist) {

          /**
           * @var Artist $Artist
           */

        ?>
          <a href="<?= "/artist/$Artist->artist_id"; ?>">
            <div fl alic gap=smol+ rounded=wide hoverable p4>
              <picture circled size=std>
                <img src="<?= "https://assets.ppy.sh/beatmaps/$Artist->set_id/covers/cover.jpg"; ?>" loading="lazy" />
              </picture>
              <div>
                <p text><?= $Artist->artist_name; ?></p>
                <p text smol color=company><?= number_format($Artist->counter); ?> plays</p>
              </div>
            </div>
          </a>
        <?php } ?>
      </div>

    <?php } ?>
  </div>

<?php endif;
