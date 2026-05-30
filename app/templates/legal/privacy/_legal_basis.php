<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold color=white><?= __("Relevant legal bases") ?></p>
  </div>

  <box-model rounded=wide elevated>
    <bm-inr size=wide>
      <p text mt=std><?= __("legal.relevant_legal_bases.1") ?></p>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Performance of Contracts and Pre-Contractual Inquiries") ?></strong>
        <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. b) GDPR") ?></div>
        <p>
          <?= __("The processing is necessary for the performance of a contract to which the data subject is party or for the implementation of pre-contractual measures that are taken at the request of the data subject.") ?>
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Legitimate Interests") ?></strong>
        <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
        <p>
          <?= __("legal.legitimate_interests.text") ?>
        </p>
      </div>

      <div text mt=std>
        <?= __("legal.relevant_legal_bases.2") ?>
      </div>
    </bm-inr>
  </box-model>
</div>