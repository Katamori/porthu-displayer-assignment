<?php

require realpath('.') . '/app/functions.php';
require realpath('.') . '/app/database.php';

// parameter handling
$queryDate = "2020-06-20";

if (isset($argv[1]) && $argv[1] === "--date" && isset($argv[2])) {
    $queryDate = $argv[2];
}

// build API url
$apiUrl = generateQueryURL($queryDate);

// send request
echo "Fetching API response...\n";

$curlResponse = sendApiRequest($apiUrl);

// in case of invalid response, end
if (!($jsonResponse = json_decode($curlResponse, true))) {
    echo "No valid JSON response arrived from Port.hu API.\n";
    exit;
}

// initialize and use database handler
$dbHandler = new DatabaseHandler(realpath(".") . "/database/" .DB_NAME. ".sqlite3");

if (in_array($queryDate, $dbHandler->getAvailableDays())) {
    echo "You've already fetched ${queryDate}!\n";
    exit;
}

$existingChannels = $dbHandler->getChannels();
$existingAgeResctrictions = $dbHandler->getAgeRestrictions();

// process response 1: add new channels and age restriction rules
echo "Processing channels and age restrictions...\n";

foreach ($jsonResponse['channels'] as $channel) {
    // if channel doesn't exist in DB, we add it
    $existingKey = arrayMultiSearch($channel['name'], $existingChannels, 'name');

    if (!$existingKey && !is_numeric($existingKey)) {
        $isAdded = $dbHandler->addChannel($channel);

        echo "[" . ($isAdded ? "SUCCESS" : "FAILED") . "] Add channel: " . $channel['name']."\n";

        // update existing list in case something was added
        if ($isAdded) {
            $existingChannels = $dbHandler->getChannels();
        }
    }

    foreach ($channel['programs'] as $program) {
        // apply same for age restriction
        $ageRestrictionName = $program['restriction']['ageLimitName'];
        $existingKey = arrayMultiSearch($ageRestrictionName, $existingAgeResctrictions, 'name');

        if (!$existingKey && !is_numeric($existingKey)) {
            $isAdded = $dbHandler->addAgeRestriction([
                'name'  => $ageRestrictionName,
                'limit' => (int)$program['restriction']['age_limit'],
                'icon'  => $program['restriction']['ageLimitImage'],
            ]);
    
            echo "[" . ($isAdded ? "SUCCESS" : "FAILED") . "] Add age restriction: ${ageRestrictionName} \n";

            // update existing list in case something was added
            if ($isAdded) {
                $existingAgeResctrictions = $dbHandler->getAgeRestrictions();
            }
        }
    }
}

// process response 2: add programs
echo "Processing programs...\n";

foreach ($jsonResponse['channels'] as $channel) {
    foreach ($channel['programs'] as $program) {

        // date check
        $startDateTime = date('Y-m-d h:i:s', strtotime($program['start_datetime']));
        $dateMin = date('Y-m-d h:i:s', strtotime($queryDate . 'T00:00:00+01:00'));
        $dateMax = date('Y-m-d h:i:s', strtotime($queryDate . 'T23:59:59+01:00'));

        if (!(($startDateTime > $dateMin) && ($startDateTime < $dateMax))) {
            echo "[OUT OF DATE RANGE] " . $dateMin ." < " . $startDateTime . " < ". $dateMax ."\n";
            continue;
        }

        $channelId = null;

        if (is_numeric($existingKey = arrayMultiSearch($channel['name'], $existingChannels, 'name'))) {
            $channelId = $existingChannels[$existingKey]['id'];
        }


        $ageLimitId = null;
        $ageRestrictionName = $program['restriction']['ageLimitName'];

        if (is_numeric($existingKey = arrayMultiSearch($ageRestrictionName, $existingAgeResctrictions, 'name'))) {
            $ageLimitId = $existingAgeResctrictions[$existingKey]['id'];
        }

        $isAdded = $dbHandler->addProgram([
            'title'             => $program['title'],
            'short_description' => $program['short_description'],
            'start_datetime'    => $program['start_datetime'],
            'channel'           => $channelId,
            'age_restriction'   => $ageLimitId,
        ]);

        $t = $program['title'];
        echo "[" . ($isAdded ? "SUCCESS" : "FAILED") . "] Add program: ${t} \n";
    }
}

exit;