<div fl gap=mid alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>

  <label size="mid" has-secondary>
    <div class="label__main">
      <p bold>Delete account</p>
    </div>
  </label>
</div>

<form
  data-form="authentication:create"
  data-type="user:delete"
  data-redirect="/home">
  <div fl fldircol gap>
    <tipp-box outlined rounded=mid clickable disabled>
      <div fl jucsb alic flexone>
        <div fl alic gap=smol+>
          <mi size=midler>download</mi>
          <div>
            <p text bold>Download your data first</p>
            <p text>Not yet available</p>
          </div>
        </div>
        <mi size=midler>east</mi>
      </div>
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
      <p text>When authentication is done, your account will be <strong>irreversible deleted</strong>. It's
        permanent, so be really sure
        about it. We won't be able to recover anything!</p>
    </tipp-box>

    <div fl jucend>
      <mbutton background="besure" color=dark-orange size=mid has-icon=left material submit-closest>
        <mi>fingerprint</mi>
        <p text bold>Authenticate for deletion</p>
      </mbutton>
    </div>
  </div>
</form>