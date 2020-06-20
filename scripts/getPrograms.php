 <?php

$queryDate = "2020-06-20";

function generateQueryURL(string $date) {
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

$url = generateQueryURL($queryDate);

echo $url . "\n";

exit;