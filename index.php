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

// Проверка корректного ответа сервера
$headers = get_headers($requestUrl);
$responseStatusCode = (int) explode(' ', $headers[0])[1];

if ($responseStatusCode < 200 || $responseStatusCode > 299) {
    exit("Не корректный код ответа сервера: $responseStatusCode");
}

$responseJSON = file_get_contents($requestUrl);
$responseErrorMessages = [];


if ($responseJSON !== false) {
    $responseArray = json_decode($responseJSON, true);

} else {
    exit('Невозможно получить данные с сервера');
}

if ($responseArray === null) {
    exit('Не возможно преобразовать данные из JSON');
}

$weather = $responseArray['weather'][0]['main'];
$temp = $responseArray['main']['temp'];
$windSpeed = $responseArray['wind']['speed'];
$windDeg = $responseArray['wind']['deg'];

$responseData = [];
$responseData[] = labelDataFactory('Погода', $weather);
$responseData[] = labelDataFactory('Температура', $temp);
$responseData[] = labelDataFactory('Ветер (скорость)', $windSpeed);
$responseData[] = labelDataFactory('Ветер (направление)', $windDeg);


function taginator($tagname, $data,  $label = '') {
    if (empty($data)) {
        return;
    }

    echo "<$tagname>";
    
    if ($label) {
        echo "$label";
    }

    if (($tagname === 'ol' || $tagname === 'ul') && gettype($data) === 'array') {
        foreach ($data as $dataPice) {
            if (empty($dataPice)) {
                break;
            }

            echo '<li>', $dataPice['label'], ': ', $dataPice['data'], '</li>';
        }

    } else {
      echo $data;
    }

    echo "</$tagname>";
}


function labelDataFactory($label, $data) {
    if (empty($data)) {
        return;
    }

    return [
        'label' => $label,
        'data' => $data,
    ];
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
        if (count($responseErrorMessages) === 0) {
            taginator('h2', $responseArray['name'], 'Город: ');
            taginator('ul', $responseData);
        } else {
            taginator('h2', 'Возникли следующие ошибки:');
            taginator('ul', $responseErrorMessages);
        }
    ?>
</body>
</html>