<?php

require realpath('.') . '/app/functions.php';
require realpath('.') . '/app/database.php';

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

$programs = [];

// initialize and use database handler
$dbHandler = new DatabaseHandler(realpath(".") . "/database/" .DB_NAME. ".sqlite3");

$existingChannels = $dbHandler->getChannels();
$existingAgeResctrictions = $dbHandler->getAgeRestrictions();

// process response 1: add new channels and age restriction rules
foreach ($jsonResponse['channels'] as $channel) {
    // if channel doesn't exist in DB, we add it
    $existingKey = array_search($channel['name'], array_column($existingChannels, 'name'));

    if (!$existingKey && !is_numeric($existingKey)) {
        $isAdded = $dbHandler->addChannel($channel);

        echo "[" . ($isAdded ? "SUCCESS" : "FAILED") . "] Add channel: " . $channel['name']."\n";
    }

    foreach ($channel['programs'] as $program) {
        // apply same for age restriction
        $ageRestrictionName = $program['restriction']['ageLimitName'];
        $existingKey = array_search($ageRestrictionName, array_column($existingAgeResctrictions, 'name'));

        if (!$existingKey && !is_numeric($existingKey)) {
            $isAdded = $dbHandler->addAgeRestriction([
                'name'  => $ageRestrictionName,
                'limit' => (int)$program['restriction']['age_limit'],
                'icon'  => $program['restriction']['ageLimitImage'],
            ]);
    
            echo "[" . ($isAdded ? "SUCCESS" : "FAILED") . "] Add age restriction: ${ageRestrictionName} \n";
        }
    }
}

// process response 2: add programs
foreach ($jsonResponse['channels'] as $channel) {
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
    }
}

exit;