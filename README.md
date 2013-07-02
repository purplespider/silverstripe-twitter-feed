# Twitter Feed Module

## Introduction

Allows you to add a custom styled Twitter feed to any page on your SilverStripe website.

The module uses the new Twitter v1.1 API to grab a specified number of tweets from a specified user, allowing you to output them to custom HTML in a template.

## Maintainer Contact ##
 * James Cocker (ssmodulesgithub@pswd.biz)
 
## Requirements
 * Silverstripe 3.1+
 
## Installation Instructions

1. Add the twitter-feed directory to the root of your SS install.
2. Go to [https://dev.twitter.com/apps](https://dev.twitter.com/apps) and **Create a new application**. Then click **Create my access token**, you'll then have a key, a token, and a secret for each.
2. Add the following lines to your _config.php:
		
		// Allows you to have a Twitter feed on any page.
        Object::add_extension('Page', 'TwitterFeed'); 
	
        // Fill in the following from your Twitter Application:
        TwitterFeed::set_consumer_key('XXXXX');
        TwitterFeed::set_consumer_secret('XXXXX');
        TwitterFeed::set_user_token('XXXXX');
        TwitterFeed::set_user_secret('XXXXX');
	
        TwitterFeed::set_username('twitter'); // The Twitter username to get the feed from
        TwitterFeed::set_tweetcount('4'); // The max number of tweets to display
		
3. Use the included TwitterFeed.ss as an example for creating your HTML for the template. **Important: This basic example is designed to be used as a starting point, and does not meet Twitter's new strict Developer Display Requirements: https://dev.twitter.com/terms/display-requirements Please make sure the way you display the Twitter feed meets these requirments or your Twitter app may be banned.**

## Acknowledgments

Thanks to **Matt Bailey's SilverStripe Widget**: [https://github.com/matt-bailey/](https://github.com/matt-bailey/silverstripe-widget-latesttweets)

And **Matt Harris's Twitter OAuth Library**: [https://github.com/themattharris/tmhOAuth](https://github.com/themattharris/tmhOAuth)