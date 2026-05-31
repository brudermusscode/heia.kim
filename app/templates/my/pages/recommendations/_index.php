<?php



?>

<div content-width=smol>
  <div mt=wide tac mb=mid>
    <p text bold mid>Recommendations</p>
    <p text>Settings we recommend for you to set</p>
  </div>

  <div fl fldircol gap>

    <?php if (!filter_var(CurrentUser->email, FILTER_VALIDATE_EMAIL)) { ?>
      <a href="/my/personal/mail">
        <box-model filled hoverable>
          <bm-inr size=mid fl gap fl gap alistart>
            <mi wide>alternate_email</mi>
            <div fl fldircol gap=smoler>
              <p text bold>Add an e-mail address</p>
              <div fl gap=smol slight>
                <p text smol>You have not yet added your e-mail address. You need it to recover your account, in case you
                  lose your credentials.</p>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </a>
    <?php } ?>

  </div>
</div>