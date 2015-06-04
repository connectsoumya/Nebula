<?php
	/*
	*	!!! Before testing this file, make sure this file exists: /includes/cache/twitter-cache
	*	Change the username below to be the desired Twitter username, and change the number of tweets as needed.
	*	Either run this file directly, or trigger it via JavaScript (the next file).
	*/

	if ( file_exists('../../../../wp-load.php') ) { require_once('../../../../wp-load.php'); } //@TODO: Remove this conditional. It is only needed for the Nebula example! Update May 30, 2015: this might be needed regardless- using 2 custom functions: nebula_settings_conditional_text() and nebula_need_updated_cache()

	error_reporting(0); //Prevent PHP errors from being cached.


	/*** Settings ***/

	//@TODO "Nebula" 0: Use query strings to set these parameters- including feed type! (except bearer token)

	$username = 'Great_Blakes';
	$listname = 'nebula'; //Only used for list feeds
	$number_tweets = 5;
	$include_retweets = 1; //1: Yes, 0: No

	//Feed Type. Comment or delete undesired feed types.
	//$feed = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&count=$number_tweets&include_rts=$include_retweets"; //Single Username Feed
	$feed = "https://api.twitter.com/1.1/lists/statuses.json?slug=$listname&owner_screen_name=$username&count=$number_tweets&include_rts=$include_retweets"; //List

	$bearer = nebula_settings_conditional_text('nebula_twitter_bearer_token', '');

	$cache_file = dirname(__FILE__) . '/cache/twitter-cache';

	if ( nebula_need_updated_cache($cache_file, 600) ) { //600 is a 10 minute cache
		$context = stream_context_create(array(
			'http' => array(
				'method'=>'GET',
				'header'=>"Authorization: Bearer " . $bearer
			)
		));

		$json = file_get_contents($feed, false, $context);

		if ( $json ) {
			$cache_static = fopen($cache_file, 'w');
			fwrite($cache_static, $json);
			fclose($cache_static);
		}
	}

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	$json = file_get_contents($cache_file);
	echo $json;