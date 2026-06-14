<?php

use Heiakim\Model\Image;

?>

<div page-structure=user>
  <div column=large fl fldircol gap=mid flexone>

    <?php

    /**
     * Photos tab disabled?
     */
    if (!$Profile->bool_value("tabs_visibility", "photos") && !$is_my_profile) { ?>

      <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap>
        <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
          <mi wide>hide_image</mi>
        </div>

        <div tac>
          <p text bold wide>Disabled</p>
          <p text std><?= $User->name; ?> has disabled their photo gallery</p>
        </div>
      </box-model>

    <?php

    } else {

    ?>

      <?php

      /**
       * @var ?Image
       */
      $Images = $User->images()
        ->orderBy("created_at", "DESC")
        ->get();

      if (!$Images->count()) { ?>

        <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap>
          <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
            <mi wide>hide_image</mi>
          </div>

          <div tac>
            <p text bold wide>Nothing</p>
            <p text std><?= $is_my_profile ? "You have" : $User->name . " has"; ?> not uploaded any images</p>
          </div>

          <?php if ($is_my_profile) { ?>
            <div fl justify-content="center">
              <a href="/editor">
                <mbutton mid has-icon=left background="dynamic">
                  <mi><?= EDITOR_ICON; ?></mi>
                  <p text bold>Start Profile Editor</p>
                </mbutton>
              </a>
            </div>
          <?php } ?>

        </box-model>

      <?php } else { ?>

        <image-gallery ovhid rounded=mid>
          <ig-wrapper>

            <?php foreach ($Images as $Image) {

              /**
               * @var string
               */
              $imagePath = AVATAR_HISTORY_DIR . "/$Image->url";

              /**
               * File is missing?
               */
              if (!file_exists($imagePath)) : ?>
                <ig-object posrel filled=lighter rounded animation=fade-in fl fldircol alic jucc gap=smol pblock42>
                  <mi size=wide>sentiment_worried</mi>
                  <p text>Image is missing</p>
                </ig-object>
              <?php else :

                // list($width, $height) = @getimagesize($imagePath);

                // if (!$width || !$height) continue;

                // /**
                //  * Determine the attribute to pass to the wrapper
                //  * object.
                //  */
                // if ($height + $width < 300)
                //   $attribute = "";
                // else if (($width - $height) > 40)
                //   $attribute = 'wide';
                // else if (($height - $width) > 40)
                //   $attribute = 'tall';
                // else
                //   $attribute = 'big';

              ?>

                <ig-object data-id="<?= $Image->id; ?>">
                  <?php if ($is_my_profile || SUPER_USER) : ?>
                    <div object-actions menu-outer>
                      <mbutton open-more-menu icon-only filled>
                        <mi>more_vert</mi>
                      </mbutton>

                      <jump-menu menu-more filled=lighter elevated color=dynamic>
                        <form data-form="image:delete">
                          <input type=hidden name=id value=<?= $Image->id ?> />
                          <div submit-closest class=jm__option hoverable>
                            <mi>delete</mi>
                            <p text std>Delete</p>
                          </div>
                        </form>
                      </jump-menu>
                    </div>
                  <?php endif; ?>

                  <img src="<?= AVATAR_HISTORY . "/$Image->url"; ?>" clickable />
                </ig-object>

            <?php
              endif;
            }
            ?>

          </ig-wrapper>
        </image-gallery>

    <?php

      } // end if images count
    } // end if photos disabled

    ?>

  </div>
</div>