<?php if (
  CurrentUser->sqcan_take_action_in($Squad)
  && CurrentUser->squad->is($Squad)
) : ?>
  <mode-menu>
    <jump-menu mm-menu filled="lighter" elevated color="dynamic">
      <div jm-inr>
        <p text smol pinline14 pblock8 ttup slighter>Compose New post</p>
        <div
          request-get="ui:posting-machine"
          data-type=squad:post
          data-sub-type=text
          ripple-effect class="jm__option" hoverable>
          <mi>format_quote</mi>
          <p text>Text</p>
        </div>
        <div
          request-get="ui:posting-machine"
          data-type=squad:post
          data-sub-type=poll
          ripple-effect class="jm__option" hoverable>
          <mi>ballot</mi>
          <p text>Poll</p>
        </div>
        <div disabled ripple-effect class="jm__option" hoverable>
          <mi>diversity_3</mi>
          <div>
            <p text>Group</p>
            <p text smoler>Not yet available</p>
          </div>
        </div>
      </div>
    </jump-menu>

    <div has-tooltip=left>
      <mbutton wide mm-open icon-only elevated=mid background=green color=light>
        <div mm-open-loading>
          <?php include COMPONENT . "/dot-loader.html"; ?>
        </div>
        <mi>add_notes</mi>
      </mbutton>
      <div ttooltip>Compose</div>
    </div>
  </mode-menu>
<?php endif; ?>