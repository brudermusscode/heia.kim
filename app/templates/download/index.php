<?php

$from = filter_input(INPUT_GET, "from", FILTER_SANITIZE_SPECIAL_CHARS);

?>

<toggle-header></toggle-header>

<verify-container fl fldircol alic jucc>

  <?php include SNOW; ?>

  <box-model elevated=wide rounded=wide style="width:100%;max-width:480px;">
    <bm-inr size=mid fl fldircol gap=mid alic>

      <div fl jucc>
        <div background=invert style="width:4.2em;height:8px;" rounded=wide elevated></div>
      </div>

      <div tac animation=fade-in fl fldircol gap=smoler alic>
        <div main-logo class="main__logo">
          <a href="/home" z>
            <picture quadrat style="height:3.2em;width:3.4em;" loading>
              <img src="<?= IMAGE . '/logo/painted/100.webp'; ?>" loaded=true />
            </picture>
          </a>

          <div class="path-loader">
            <svg class="circular" viewBox="25 25 50 50">
              <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4 " stroke-miterlimit="10" />
            </svg>
          </div>
        </div>

        <p text wide bold trimt>Setup easily</p>
        <p text>Get our Setup App to connect to {app-name}</p>
      </div>

      <tipp-box outlined=darker rounded=mid>
        <mi>verified_user</mi>
        <p text>
          Signing apps so that Windows will recognize it as trusted is expensive. We haven't, but our App is safe to
          use. You can checkout the <a href="https://www.virustotal.com/gui/file/67eb3237845e0f98d612226496ed21331bf3119fb8b2fc59ac9ce4633f3cc81d/detection" normal extern target="_blank">VirusTotal scan</a>, if you are unsure.
        </p>
      </tipp-box>

      <div fl gap=smol alic>
        <?php if (LOGGED) : ?>
          <a href="<?= CurrentUser->link(); ?>">
            <mbutton mid>
              <p text>Later</p>
            </mbutton>
          </a>
        <?php else : ?>
          <a href="/">
            <mbutton mid>
              <p text>Back</p>
            </mbutton>
          </a>
        <?php endif; ?>

        <a href="/Apps/Setup/heiakimSetup.exe" target="_blank" extern>
          <mbutton mid has-icon=left background=follow color=dark-green>
            <mi>download</mi>
            <p text bold>Download</p>
          </mbutton>
        </a>
      </div>

      <div fl gap=smol alic>
        <mi class="ri-windows-fill"></mi>
        <p text smol slight>Available for Windows only</p>
      </div>
    </bm-inr>
  </box-model>

</verify-container>

<?php if (LOGGED) { ?>
  <a href="<?= CurrentUser->link(); ?>" page sub>
    <div next elevated=wide>
      <mi wide color=green>check</mi>
    </div>
  </a>
<?php } ?>