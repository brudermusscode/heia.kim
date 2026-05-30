<?php

if ($rank_development->global > 0) {
  $amount = $rank_development->global;
  echo <<<TEXT
    <div falling class="additions_option">
      <p>$amount</p>
      <p>
        <i class=mi>expand_more</i>
      </p>
    </div>
  TEXT;
} else if ($rank_development->global < 0) {
  $amount = -1 * $rank_development->global;
  echo <<<TEXT
    <div rising class="additions_option">
      <p>$amount</p>
      <p>
        <i class=mi>expand_less</i>
      </p>
    </div>
  TEXT;
}
