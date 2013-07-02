<?php

class TwitterFeed extends DataExtension {

	// Default Keys - Use site specifc keys if possible: https://dev.twitter.com/apps
	private static $consumer_key = null;
	private static $consumer_secret = null;
	private static $user_token = null;
	private static $user_secret = null;
	
	private static $username = "twitter";
	private static $tweetcount = 3;
	
	// Getters
	public static function get_consumer_key() {
		return self::$consumer_key;
	}
	
	public static function get_consumer_secret() {
		return self::$consumer_secret;
	}
	
	public static function get_user_token() {
		return self::$user_token;
	}
	
	public static function get_user_secret() {
		return self::$user_secret;
	}
	
	public static function get_username() {
		return self::$username;
	}
	
	public static function get_tweetcount() {
		return self::$tweetcount;
	}
	
	// Setters
	public static function set_consumer_key($value) {
		self::$consumer_key = $value;
	}
	
	public static function set_consumer_secret($value) {
		self::$consumer_secret = $value;
	}
	
	public static function set_user_token($value) {
		self::$user_token = $value;
	}
	
	public static function set_user_secret($value) {
		self::$user_secret = $value;
	}
	
	public static function set_username($value) {
		self::$username = $value;
	}
	
	public static function set_tweetcount($value) {
		self::$tweetcount = $value;
	}
	
	 /**
* Function to convert links, mentions and hashtags: http://goo.gl/ciKGs
*/
    function tweetConvert($tweet_string) {
        $tweet_string = preg_replace("/((http(s?):\/\/)|(www\.))([\w\.]+)([a-zA-Z0-9?&%.;:\/=+_-]+)/i", "<a href='http$3://$4$5$6' target='_blank'>$2$4$5$6</a>", $tweet_string);
        $tweet_string = preg_replace("/(?<=\A|[^A-Za-z0-9_])@([A-Za-z0-9_]+)(?=\Z|[^A-Za-z0-9_])/", "<a href='http://twitter.com/$1' target='_blank'>$0</a>", $tweet_string);
        $tweet_string = preg_replace("/(?<=\A|[^A-Za-z0-9_])#([A-Za-z0-9_]+)(?=\Z|[^A-Za-z0-9_])/", "<a href='http://twitter.com/search?q=%23$1' target='_blank'>$0</a>", $tweet_string);
        return $tweet_string;
    }

    function getLatestTweets() {

        require(Director::baseFolder() . '/twitter-feed/libs/tmhOAuth.php');
        require(Director::baseFolder() . '/twitter-feed/libs/tmhUtilities.php');

        $tmhOAuth = new tmhOAuth(array(
            'consumer_key' => self::get_consumer_key(),
            'consumer_secret' => self::get_consumer_secret(),
            'user_token' => self::get_user_token(),
            'user_secret' => self::get_user_secret(),
            'curl_ssl_verifypeer' => false
        ));

        $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array(
            'screen_name' => self::get_username(),
            'count' => self::get_tweetcount()+10, // Load 10 extra tweets so that target count still possible after removing retweets
            'exclude_replies' => true
        ));

        $response = $tmhOAuth->response['response'];
        $tweets = json_decode($response, true);
        
        if($this->_errorCheck($tweets)){
            return false;
        }

        $output = new ArrayList();
        foreach ($tweets as &$tweet) {
            $tweet['text'] = $this->tweetConvert($tweet['text']);
            $time = SS_Datetime::create('SS_Datetime');
            $time->setValue($tweet['created_at']);
            $tweet['created_at'] = $time; //
            $tweet['username'] = self::get_username();
            $output->push(new ArrayData($tweet));
        }
         
        return $output->limit(self::get_tweetcount());
    }

    private function _errorCheck($tweets){
        if(array_key_exists('errors', $tweets)){
            $message = 'We have encountered '.count($tweets['errors']).' error(s): <br />';
            foreach ($tweets['errors'] as $error) {
                $message .= $error['message'].' Code:'.$error['code'].'<br />';
            }
            if(Director::isDev()){
                throw new Exception($message, 1);
            } else if (Email::getAdminEmail()){
            	$from = Email::getAdminEmail();
            	$to = Email::getAdminEmail();
            	$subject = "Twitter Feed Failure - ".Director::AbsoluteBaseURL();
            	$body = $message;
            	$body.= "<br /><br />Reported by ".Director::AbsoluteBaseURL();
	            $email = new Email($from, $to, $subject, $body);
				$email->send();
            }
            return true;
        }
    }
    
    public function TwitterCacheCounter() {
	    return (int)(time() / 60 / 5); // Returns a new number every five minutes
	}

}