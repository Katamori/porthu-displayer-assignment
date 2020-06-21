<?php

require realpath('.') . '/functions.php';

// todo: make it a parameter
$queryDate = "2020-06-20";

// build API url
$apiUrl = generateQueryURL($queryDate);

// send request
$curlResponse = sendApiRequest($apiUrl);

// in case of invalid response, end
if (!($jsonResponse = json_decode($curlResponse, true))) {
    echo "No valid JSON response arrived from Port.hu API.\n";
    exit;
}

$existingChannels = [];
$existingAgeResctrictions = [];

$channels = [];
$programs = [];
$ageRestrictions = [];

// process response
foreach ($jsonResponse['channels'] as $channel) {
    $channels[] = [
        'name' => $channel['name']
    ];
    //echo "channel added: " . $channel['name']."\n";

    foreach ($channel['programs'] as $program) {
        $programs[] = [
            'title'             => $program['title'],
            'short_description' => $program['short_description'],
            'start_datetime'    => $program['start_datetime'],
            // todo: convert to existing channel and restriction ID
            'channel'           => $channel['name'],
            'age_restriction'   => $program['restriction']['ageLimitName'],
        ];
        //echo $program['title']."\n";


        $ageRestrictions[] = [
            'name'  => $program['restriction']['ageLimitName'],
            'limit' => $program['restriction']['age_limit'],
            'icon'  => $program['restriction']['ageLimitImage'],
        ];
        //echo $program['restriction']['ageLimitName']."\n";
    }
}

exit;