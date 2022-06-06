<?php

add_action( 'wp_ajax_special_offer', 'sendSpecialOfferEmail' );
add_action( 'wp_ajax_nopriv_special_offer', 'sendSpecialOfferEmail' );

function sendSpecialOfferEmail() {

	if ( isset( $_POST["afternoon_morning"][0] ) ) {
		$morning_after = $_POST["afternoon_morning"][0];
		$morning_after = "<p><strong>Morning / Afternoon</strong>: " . $morning_after . "\n" . "</p>";
	} else {
		$morning_after = "";
	}

	$message = "<h4>New reading request from AnnaKovach.com</h4>" .

	           "<p><strong>Her Email</strong>: " . $_POST["her_email"] . "\n" . "</p>" .
	           "<p><strong>Her Name</strong>: " . $_POST["her_name"] . "\n" . "</p>" .
	           "<p><strong>Her Exact Date of Birth</strong>: " . $_POST["her_date"] . "\n" . "</p>" .
	           "<p><strong>Her Exact Place of Birth</strong>: " . $_POST["her_place"] . "\n" . "</p>" .
	           "<p><strong>Her Exact Time of Birth</strong>: " . $_POST["her_time"] . "\n" . "</p>" .
	           "<p><strong>His Name</strong>: " . $_POST["his_name"] . "\n" . "</p>" .
	           "<p><strong>His Exact Date of Birth</strong>: " . $_POST["his_date"] . "\n" . "</p>" .
	           "<p><strong>His Exact Place of Birth</strong>: " . $_POST["his_place"] . "\n" . "</p>" .
	           "<p><strong>His Exact Time of Birth</strong>: " . $_POST["his_time"] . "\n" . "</p>" .
	           $morning_after .
	           "<p><strong>As much as she can say about their relationship</strong>: " . $_POST["message_1"] . "\n" . "</p>" .
	           "<p><strong>Her biggest concern</strong>: " . $_POST["message_2"] . "</p>";

	$subject = 'Anna Kovach Compatibility Reading Request | ' . $_POST["her_email"];

	$headers   = [];
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	if ( ! wp_mail( 'readings@annakovach.com', $subject, $message, $headers ) ) {
		echo 'Message could not be sent.';
	}

	if ( isset( $_POST["afternoon_morning"][0] ) ) {
		$morning_after = $_POST["afternoon_morning"][0];
		$morning_after = "<p><strong>Morning / Afternoon</strong>: " . $morning_after . "\n" . "</p>";
	} else {
		$morning_after = "";
	}


	$message = "<h4>Hi there! </h4>
<p>I just received your request for a compatibility reading and I'll start working on it right away! </p>
<p>Here's the data you left me. Please check if it's all correct, and, in case there's anything you'd like to add or change, please do so by replying to this email.<p>" .

	           "<p><strong>Your Email</strong>: " . $_POST["her_email"] . "\n" . "</p>" .
	           "<p><strong>Your Name</strong>: " . $_POST["her_name"] . "\n" . "</p>" .
	           "<p><strong>Your Exact Date of Birth</strong>: " . $_POST["her_date"] . "\n" . "</p>" .
	           "<p><strong>Your Exact Place of Birth</strong>: " . $_POST["her_place"] . "\n" . "</p>" .
	           "<p><strong>Your Exact Time of Birth</strong>: " . $_POST["her_time"] . "\n" . "</p>" .
	           "<p><strong>His Name</strong>: " . $_POST["his_name"] . "\n" . "</p>" .
	           "<p><strong>His Exact Date of Birth</strong>: " . $_POST["his_date"] . "\n" . "</p>" .
	           "<p><strong>His Exact Place of Birth</strong>: " . $_POST["his_place"] . "\n" . "</p>" .
	           "<p><strong>His Exact Time of Birth</strong>: " . $_POST["his_time"] . "\n" . "</p>" .
	           $morning_after .
	           "<p><strong>As much as you can say about your relationship</strong>: " . $_POST["message_1"] . "\n" . "</p>" .
	           "<p><strong>Your biggest concern</strong>: " . $_POST["message_2"] . "</p>" .

	           "<p>May the stars be on your side, </p>
<p>Anna</p>";

	$subject = 'Your Compatibility Reading Request';

	if ( ! wp_mail( $_POST["her_email"], $subject, $message, $headers ) ) {
		echo 'Message could not be sent.';
	} else {
		echo 200;
		die();
	}
}