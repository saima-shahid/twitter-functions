<?php

// Require TwitterOAuth files. (Downloadable from : https://github.com/abraham/twitteroauth)
require_once("twitteroauth/twitteroauth.php");

 // Twitter keys (You'll need to visit https://dev.twitter.com and register to get these.
define('CONSUMERKEY','<CONSUMERKEY>');
define('CONSUMERSECRET','<CONSUMERSECRET>');
define('ACCESSTOKEN','<ACCESSTOKEN>');
define('ACCEESSTOKENSECRET','<ACCEESSTOKENSECRET>');

function get_user($username)
{

  	$connection = new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, ACCESSTOKEN, ACCEESSTOKENSECRET);
    $users= $connection->post('users/lookup', array('screen_name' => $username));
    foreach ($users as $user) 
    {
        $founds[$user->screen_name] = true;
    }
 
    return $founds;
}

function get_followers($username)
{
  	$connection = new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, ACCESSTOKEN, ACCEESSTOKENSECRET);
	$content = $connection->get('https://api.twitter.com/1.1/followers/ids.json?cursor=-1&screen_name='.$username.'&count=5000');
	$total_followers = count($content->ids);

	return $total_followers;
}

function get_followings($username)
{
  	$connection = new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, ACCESSTOKEN, ACCEESSTOKENSECRET);
	$content = 	$connection->get("https://api.twitter.com/1.1/friends/ids.json?screen_name=".$username);
	$following_count = count($content->ids);

	return $following_count;
}

function get_tweet_count($username)
{
  	$connection = new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, ACCESSTOKEN, ACCEESSTOKENSECRET);
	$content = $connection->get('users/show', array('screen_name' => $username));
    $tweet_count = $content->statuses_count;

    return $tweet_count;
}

function display_latest_tweets(
    $twitter_user_id,
    $tweets_to_display   = 5)
{           

	$twitter_wrap_open   = '<ul>';
    $twitter_wrap_close  = '</ul>';
    $tweet_wrap_open     = '<li><p>';
    $tweet_wrap_close    = '</p></li>';

    $tweet_found         = false;

  	$connection = new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, ACCESSTOKEN, ACCEESSTOKENSECRET);
    
    if($connection)
    {
    	$user = get_user($twitter_user_id);

    	if(array_key_exists($twitter_user_id, $user))
    	{
        	$get_tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitter_user_id."&count=".$tweets_to_display);

        	if (count($get_tweets)) 
        	{
            	$tweet_count = 0;

            	ob_start();

            	$twitter_html = $twitter_wrap_open;

            	foreach($get_tweets as $tweet) 
            	{
                    $tweet_found = true;
                    $tweet_count++;
                    $tweet_desc = $tweet->text;
                    
                    $twitter_html .= $tweet_wrap_open.html_entity_decode($tweet_desc).'<a href="http://twitter.com/'.$twitter_user_id.'"></a>'.$tweet_wrap_close;                  

                	if ($tweet_count > $tweets_to_display)
                	{
                    	break;
                	}

            	}

            	$twitter_html .= $twitter_wrap_close;
            	echo $twitter_html;

            	ob_end_flush();
        	}

        }
        else
        {

        	echo "Twitter username does not exist. \n";
        }

    }

}
?>