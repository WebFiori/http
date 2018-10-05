<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require '../jsonx-1.3/JsonI.php';
require '../jsonx-1.3/JsonX.php';
require '../API.php';
require '../APIFilter.php';
require '../RequestParameter.php';
require '../APIAction.php';
/*
 * A playground to do anything you like.
 */

$string = "hello mr \"ali\" \"";
for($x = 0 ; $x < strlen($string) ; $x++){
    if($string[$x] == '"'){
        echo 'true<br/>';
    }
}

?><pre><?php print_r(APIFilter::filterArray('[44,"7656757",875,4,5]')) ?></pre><?php