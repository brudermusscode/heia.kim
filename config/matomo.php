<?php

use Bruder\Application\Cookie;

?>

<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  (function() {
    var u = "//analytics.heia.kim/";

    <?php

    /**
     * Disable matomo when cookie consent wasn't given.
     */
    if (!Cookie::get('COOKIE_CONSENT'))
      echo "_paq.push([\"disableCookies\"]);";

    ?>

    _paq.push(['setTrackerUrl', u + 'matomo.php']);
    _paq.push(['setSiteId', '1']);
    _paq.push(["setDoNotTrack", true]);
    _paq.push(['enableLinkTracking']);
    _paq.push(['trackPageView']);
    var d = document,
      g = d.createElement('script'),
      s = d.getElementsByTagName('script')[0];
    g.async = true;
    g.src = u + 'matomo.js';
    s.parentNode.insertBefore(g, s);
  })();
</script>

<noscript>
  <p>
    <img src="//analytics.heia.kim/matomo.php?idsite=1&amp;rec=1" style="border:0;" alt="noscript analytics heia.kim" />
  </p>
</noscript>