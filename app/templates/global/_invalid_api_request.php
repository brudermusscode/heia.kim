<div class=container>
  <div class="profile">
    <div style="font-size:6.2em;">
      <i class="mi icon-lock-rotate">api</i>
    </div>
  </div>

  <div tac>
    <p text wider bold>Oh no!</p>
    <p text std><?= $return->message ?? "An invalid API request has been sent."; ?></p>
  </div>
</div>