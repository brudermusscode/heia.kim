<?php

use Bruder\Application\Feature;

?>

<!--- SIGN UP USING EMAIL --->
<?php if (Feature::is_enabled("signup")) { ?>

  <form request="authentication:create" redirect="/login" responder fl fldircol gap>
    <input type=hidden name=type value="user:create" />
    <div fl fldircol gap=smol+>
      <div fl gap=smol alic>
        <div input material has-icon flexone>
          <i class=mi size=std>alternate_email</i>
          <input type="text" data-action="input,toggle-extra" autofocus name="email" placeholder="E-Mail" enter-submitable />
        </div>

        <div fl jucsb>
          <mbutton ripple-effect tabindex=5 submit-closest icon-only material size=mid filled=darker>
            <i class=mi>arrow_forward</i>
          </mbutton>
        </div>
      </div>
    </div>
  </form>

<?php } else { ?>

  <div>
    <p text std><?= __("The sign up has temporarily been disabled.") ?></p>
  </div>

<?php } ?>