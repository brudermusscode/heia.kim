<div fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>
  <p text mid bold>Image history</p>
</div>

<form request="user:setting:update" delay=20 responder=error no-loader radio fl fldircol gap=smol>
  <box-model filled p32 fl fldircol gap=smol+>
    <p text midler bold>Enable history</p>
    <div fl jucsb alistart gap>
      <div flexone>
        <p text>Save images you upload in a history like a photo gallery, which can be displayed on your profile.</p>
      </div>

      <toggle-switch submit-closest mt=smol toggled=<?= CurrentUser->privacy->image_history ? "true" : "false"; ?>>
        <div class="toggle_switch__inr">
          <div class="toggle_switch__switcher"></div>
          <input type="hidden" name="image_history" value="<?= CurrentUser->privacy->image_history; ?>" />
          <div fl fldirrow justify-content="center">
            <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
            </div>
          </div>
        </div>
      </toggle-switch>
    </div>
  </box-model>

  <box-model filled p32>
    <div fl fldircol gap=smol+>
      <p text midler bold>Photos Tab</p>
      <div fl jucsb alistart gap>
        <div flexone>
          <p text>Show a tab on the left sidebar on your profile which leads to a photo gallery. This can be disabled in the {profile-editor-link}.</p>
        </div>
      </div>
    </div>

    <div mt>
      <a href="/editor" sub>
        <div p12 rounded="std" hoverable>
          <div fl gap alic jucsb>
            <div fl gap="smol+" alic>
              <mi mid>shape_line</mi>
              <p text>Open Profile Editor</p>
            </div>
            <mi midler>east</mi>
          </div>
        </div>
      </a>
    </div>
  </box-model>
</form>