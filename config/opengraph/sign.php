<?php

if (CURRENT_PAGE === 'register') {
  $og->image = IMAGE . "/pages/sc-signup.png";
  $og->desc = "Create a free account on heia.kim and start climbing the leaderboard!";
}

if (CURRENT_PAGE === 'login') {
  $og->image = IMAGE . "/pages/sc-login.png";
  $og->desc = "Already have an account? Login to heia.kim!";
}
