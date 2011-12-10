# John Holdun's Instagram Feed Thing

Want a fast, self-hosted mirror of your Instagram feed? Here you go.

## Installation

1. Put this repo on your server.
2. Register a third-party app at http://instagram.com/developer/
3. Figure out your Instagram user ID (probably with the API)
4. Get an access token for your app and Instagram account (standard OAuth stuff)
5. Change the appropriate fields in config.php
6. Visit /your-repo/update to pull in your data and generate a static index page (for superfast loading)
7. Edit template.phtml and style.css as you wish
8. Set up a CRON job to call /your-repo/update every ten minutes or whatever

Almost too easy!!!

## Caching

The first page of your Instagram feed is baked for very quick loading. All other pages and permalinks are generated per request, but *all* your photo data is stored locally, in data.php. The only time the Instagram API is called is when you visit /your-repo/update, or if there's no local data at all.

This can be tricky when you're trying to edit your template. I suggest looking at page 2, then running the update script when you're done making changes.

Also, if your local data is up to date, index.html won't be regenerated, but the script is also not smart enough to fill in the gaps, so the only way to force a re-bake is to remove one or more items from ***the end*** of data.php (they are ordered chronologically, oldest to newest).