<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
/*
 * A playground to do anything you like.
 */

$inputs = filter_var($_GET['email'], FILTER_CALLBACK, array(
    'options'=>'shit'
));
function shit($val){
    var_dump($val);
    echo 'oh shit!';
}
echo $inputs;