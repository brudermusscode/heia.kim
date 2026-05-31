<div tac>
  <p text bold wide><?= APP_SETTING->premium_feature_name; ?></p>
  <p text std><?= __("The awesome extra area, personally just for you!") ?></p>
</div>

<div grid-repeat=smol gap=smol>
  <div grid-keeper>
    <box-model outlined style=max-width:28em;>
      <bm-inr size=std>
        <div pblock12 mb fl fldircol style="gap:.2em;">
          <p text midler bold><?= __("Name appearance") ?></p>
        </div>
        <a href="<?= "/my/premium/name"; ?>" sub>
          <div hoverable p12 rounded=wide>
            <div fl gap align-items=center justify-content=space-between>
              <div fl gap align-items=center>
                <p>
                  <i class="mi" size=mid>format_shapes</i>
                </p>
                <p text std>
                  <?= CurrentUser->name(); ?>
                </p>
              </div>
              <p text midler>
                <i class="mi">east</i>
              </p>
            </div>
          </div>
        </a>
      </bm-inr>
    </box-model>
  </div>
</div>