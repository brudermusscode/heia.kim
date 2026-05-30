<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold color=white><?= __("Safety measures") ?></p>
  </div>

  <box-model rounded=wide elevated>
    <bm-inr size=wide>
      <p text mt=std><?= __("legal.safety_measures.1") ?></p>
      <p text mt=std><?= __("legal.safety_measures.2") ?></p>

      <div text mt=std>
        <div class="legal_para" inline><?= __("IP address truncation") ?></div>
        <p>
          <?= __("legal.safety_mesaures.ip_address_truncation.text") ?>
        </p>
      </div>

      <div text mt=std>
        <div class="legal_para" inline><?= __("TLS encryption (https)") ?></div>
        <p><?= __("legal.safety_measures.tls.text") ?></p>
      </div>
    </bm-inr>
  </box-model>
</div>