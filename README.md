# universal-analytics-bots
Google Universal Analytics for Bots

1. Create a new ‘bots only’ Web Property in your Google Analytics account using Universal Analytics. Remember to grab your new Web Property ID (i.e. UA-XXXXXX-YY)
2. Unzip and place the ‘/ua-searchbots/’ folder on your website (example: www.domain.com/ua-searchbots/)
3. Copy the UA for Search Bots Tracking Code found in sample.php and place it in your PHP source code (example: in your common ‘header’ include file)
4. Edit the UA for Search Bots Tracking code for the following:
  - Set the $UA_SB_ACCOUNT_ID variable to the new GA Web Property ID.
  - Set the $UA_SB_PATH to the location of the ‘/ua-searchbots/’ folder. (Depending on your PHP setup, you may or may not run into issues with setting the location value. Depends on your include_path setting)

One thing to point out in this custom code library is that ‘source’ is set as the user agent, not the traditional campaign source. I found it easier to drill down to the different bots with this method. I would also pay a little more attention to Pageviews rather than Visits to better analyze how the bots crawl your site.

Another thing is that this will only track bots that execute a page with the UA for SB tracking script. If a bot hits a URL that only generates a generic server response (like maybe a 500 status code), then it will NOT be tracked in your new UA profile. Just a little disclaimer.

- See more at: http://www.adrianvender.com/universal-analytics-for-search-bots/
