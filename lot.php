<?php
require_once __DIR__ . '/init.php';

if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php',['error' => $error]);
}
else{
    $categories = getCategories($link);

    if( !isset($_GET['id']) )
    {
        header( "Location:/404.php", true,302 );
        exit();
    }
    if ( empty($_GET['id']) )
    {
        header( "Location:/404.php", true,302 );
        exit();
    }
    $lot = getLot($link, $_GET['id']);
    if(!$lot){
        header( "Location:/404.php", true,302 );
        exit();
    }
}
//var_dump($_GET['id']);
//var_dump($_SESSION);
//var_dump($lot);
//проверку что последний добавленная ставка не текущего пользователя

//текущая ставка это ставка последнего пользователя или цена
$bet = getBet($link, $_GET['id']);
//проверка если пустое значение
if(empty($bet)){
    $bet = $lot[0]['begin_price'];
}
$bidStep = $bet + $lot[0]['bid_step'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //проверить что пользователь другой, не тот который создал лот
    $price = $_POST['price'];
    $error = validateBet($price, $lot, $bidStep, $link);
    var_dump($error);
    //exit();
    
    //записать значение
    //if (empty($error)){

//    }

}


$page_content = include_template('lot.php',[
    'lot' => $lot[0],
    'bet' => $bet,
    'bidStep' => $bidStep,
    'error' => $error
]);

$layout_content = include_template('layout.php',[
    'categories' => $categories,
    'content'    => $page_content,
    'title'      => $lot[0]['name']
]);

print($layout_content);
