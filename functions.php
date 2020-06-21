<?php

/**
 * Generates a port.hu API request URL for the specified date.
 * 
 * @param string $date
 * @return string
 */
function generateQueryURL(string $date = '2020-06-20'): string
{
    $API_URL = "https://port.hu/tvapi";

    $tvChannels = [
        5,
        3,
        21,
        6,
        103
    ];
    
    $querifiedTvChannels = array_map(function ($id) {
        return "tvchannel-" . $id;
    }, $tvChannels);
    
    $params = http_build_query([
        "channel_id"  => $querifiedTvChannels,
        "date"        => $date
    ]);
    
    return $API_URL . "?" . $params;
}

/**
 * Sends a cURL request to a specified API url.
 */
function sendApiRequest(string $url, string $method = "GET") {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            "Cookie: INX_CHECKER2=1"
        ],
    ]);

    $curlResponse = curl_exec($curl);
    curl_close($curl);

    return $curlResponse;
}