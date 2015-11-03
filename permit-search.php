<?php

// Open permit API
define("URL_BASE", "http://mdc.openpermit.org/op/permits");

// Simple utility method to make API call.
function getPermitData($num) {
	$url = URL_BASE . "?number=" . $num;
	$permit_data = file_get_contents($url);
	return json_decode($permit_data);
}

function formatOutput($text, $channel) {
	if ($channel == "VOICE") {
		return implode(" ", str_split($text));
	}
	else {
		return substr($text,0,4) . "-" . substr($text,4,3) . "-" . substr($text,7,3);
	}
}

// SMS
if($currentCall->initialText) {
	$permit_number = $currentCall->initialText;
	$permit_data = getPermitData($permit_number);
}

// Phone
else {
	$permit = ask("Please enter your 10 digit permit number.", array("choices" => "[10 DIGITS]", "mode" => "dtmf", "attempts" => 3, "timeout" => 7));
	$permit_number = $permit->value;
	$permit_data = getPermitData($permit_number);
}

// Render output to user
if(count($permit_data) > 0) {
	say('The current status of permit number ' . formatOutput($permit_number, $currentCall->channel) . ' is ' . $permit_data[0]->statusCurrent);

}
else {
	say('No permit found with number: ' . formatOutput($permit_number, $currentCall->channel));
}

// Be nice.
if($currentCall->channel == "VOICE") {
	say('Thank you.');
}
hangup();

