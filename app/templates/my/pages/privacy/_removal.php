<div fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>
  <p text mid bold>Delete account</p>
</div>

<form data-action="authentication:create" full-redirect="/home">

  <input type=hidden name="type" value="user:delete" />

  <div fl fldircol gap>
    <tipp-box outlined rounded=mid clickable disabled fl jucsb alic flexone>
      <div fl alic gap=smol+>
        <mi size=midler>download</mi>
        <div>
          <p text bold>Download your data first</p>
          <p text color=red>Not yet available</p>
        </div>
      </div>
      <mi size=midler>east</mi>
    </tipp-box>

    <div fl fldircol gap=smol+>
      <div>
        <p text midler bold>Some things you need to know</p>
        <p text>When deleting your account, all of the following things will go with you</p>
      </div>

      <div fl fldircol gap=smol>
        <get-content from="/user/get-content/deletion-information" fl fldircol gap=smol>
          <?php include CIRLOADER; ?>
        </get-content>
      </div>
    </div>

    <tipp-box outlined rounded=mid>
      <mi>info</mi>
      <p text>When authentication is done, your account will be <strong>irreversible deleted</strong>.</p>
    </tipp-box>

    <div fl jucend>
      <mbutton mid background="besure" color=dark-orange has-icon=left submit-closest>
        <mi>fingerprint</mi>
        <p text bold>Authenticate</p>
      </mbutton>
    </div>
  </div>
</form>