<?php

/**
 * Heading
 */
include TEMPLATE . "/login/_header.php";

?>

<toggle-header></toggle-header>
<disguised></disguised>

<login>

  <?php include SNOW; ?>

  <div disguised-content content-width=smolest fl fldircol gap>

    <!--- FLEX: MAIN CONTENT --->
    <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
      <sign-container fl fldircol gap=mid>
        <div tac color=light fl fldircol gap alic>

          <div style=height:6.2em;width:6.2em; background=light color=dark circled fl alic jucc>
            <mi wider>pageless</mi>
          </div>

          <div fl fldircol gap=smoler>
            <p text widest bold>Nothing</p>
            <p text mid>This page is unavailable</p>
          </div>
        </div>
      </sign-container>
    </div>

  </div>
</login>