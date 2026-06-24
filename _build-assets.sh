#!/bin/sh

# For building production assets, we need to exchange the theme bundle with the prod-
# uctiion one that includes all themes.
echo '@forward "prod";' >app/assets/scss/themes/theme.scss

# Then we can build production assets.
pnpm run prod

# Aaaaand we exchange it back to dev!
echo '@forward "dev";' >app/assets/scss/themes/theme.scss
