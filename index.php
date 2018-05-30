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
$responseArray = json_decode($responseJSON, true);
$responseData = [
    [
        'label' => 'Погода',
        'data' => $responseArray['weather'][0]['main'],
    ],
    [
        'label' => 'Температура',
        'data' => $responseArray['main']['temp'],
    ],
    [
        'label' => 'Ветер (скорость)',
        'data' => $responseArray['wind']['speed'],
    ],
    [
        'label' => 'Ветер (направление)',
        'data' => $responseArray['wind']['deg'],
    ],
];


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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weather</title>
</head>
<body>
    <?php
        taginator('h2', $responseArray['name'], 'Город: ');
        taginator('ul', $responseData);
    ?>
</body>
</html>