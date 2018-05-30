<?php
// API приложения принимает следующие queryString-параметры из GET-запроса:
// ?cityID=(int) - осуществляется поиск по ID города, 
// по-умолчанию подставляется ID Москвы;
//
// ?cityName=(string) - осуществляется поиск по латинскому названию города.
// 
// Если указано название, поиск по ID игнорируется


// require $apiConfig
require_once('apiConfig.php');

if (isset($_GET['cityID'])) {
    $cityID = $_GET['cityID'];
} else {
    $cityID = $apiConfig['cityID'];
}

if (isset($_GET['cityName'])) {
    $cityName = $_GET['cityName'];
}


$queryParams = [
  'appid' => $apiConfig['key'],
];


if (isset($cityName) && $cityName) {
    $queryParams['q'] = $cityName;
} else {
    $queryParams['id'] = $cityID;
}

$requestUrl = $apiConfig['url'] . '?' . http_build_query($queryParams);

$responseJSON = file_get_contents($requestUrl);
$responseArray = (array) json_decode($responseJSON);
$responseData = [
    [
        'label' => 'Погода',
        'data' => $responseArray['weather'][0] -> main,
    ],
    [
        'label' => 'Температура',
        'data' => $responseArray['main'] -> temp,
    ],
    [
        'label' => 'Ветер (скорость)',
        'data' => $responseArray['wind'] -> speed,
    ],
    [
        'label' => 'Ветер (направление)',
        'data' => $responseArray['wind'] -> deg,
    ],
];


taginator('h2', $responseArray['name'], 'Город: ');
taginator('ul', $responseData);


function taginator($tagname, $data,  $label = '') {
    echo "<$tagname>";
    
    if ($label) {
        echo "$label";
    }

    if (($tagname === 'ol' || $tagname === 'ul') && gettype($data) === 'array') {
        foreach ($data as $dataPice) {
            echo '<li>', $dataPice['label'], ': ', $dataPice['data'], '</li>';
        }
    } else {
      echo $data;
    }

    echo "</$tagname>";
}