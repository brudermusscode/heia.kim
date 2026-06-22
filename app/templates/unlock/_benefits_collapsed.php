<div expand-more fl fldircol gap=smoler>
  <div outlined rounded p10>
    <div fl alic gap=smol+>
      <mi color=green>check_circle</mi>
      <p text bold>Custom profile headlines</p>
    </div>
  </div>

  <div outlined rounded p10>
    <div fl alic gap=smol+>
      <mi color=green>check_circle</mi>
      <p text bold class=premium-txt-peach>Unique Name Designs</p>
    </div>
  </div>

  <div outlined rounded p10>
    <div fl alic gap=smol+>
      <mi color=green>check_circle</mi>
      <p text bold><span color=company>+1</span> &nbsp; Name Change</p>
    </div>
  </div>

  <div expand-more-hidden fl fldircol gap=smoler>
    <div outlined rounded p10>
      <div fl alic gap=smol+>
        <mi color=green>check_circle</mi>
        <p text bold><span color=company>+1</span> &nbsp; Account Restart</p>
      </div>
    </div>

    <div outlined rounded p10>
      <div fl alic gap=smol+>
        <mi color=green>check_circle</mi>
        <p text bold>Upload GIFs</p>
      </div>
    </div>

    <div outlined rounded p10>
      <div fl alic gap=smol+>
        <div posrel has-tooltip=bottom>
          <mi color=green>check_circle</mi>
          <div ttooltip>Requires Discord connection.</div>
        </div>
        <p text fl alic><strong>Discord Role &nbsp;&nbsp;</strong>
          <?php if (!CurrentUser->discord) : ?>
            <a disib pinline8 pblock2 rounded clickable background=slight curpo in-user-manager open="security:discord" close-overlay fl alic color=company>
              Connect Discord &nbsp; <mi smol>open_in_new</mi></a>
          <?php endif; ?>
        </p>
      </div>
    </div>
  </div>

  <div fl jucc mt=smol>
    <mbutton smol expand-more-show filled has-icon=right>
      <p text expand-more-text></p>
      <mi>unfold_more</mi>
    </mbutton>
  </div>
</div>