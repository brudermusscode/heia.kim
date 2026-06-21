<?php if (
  CurrentUser->sqcan_take_action_in($Squad)
  && CurrentUser->squad->is($Squad)
) : ?>
  <mode-menu>
    <jump-menu mm-menu filled="lighter" elevated color="dynamic">
      <div jm-inr>
        <p dno text bold smol pinline4 pblock14 ttup slighter>Compose new</p>
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

    <mbutton wide mm-open has-icon=left elevated=mid background=company color=company-text>
      <div mm-open-loading>
        <?php include COMPONENT . "/dot-loader.html"; ?>
      </div>
      <mi>add</mi>
      <p text bold>Compose</p>
    </mbutton>
  </mode-menu>
<?php endif; ?>