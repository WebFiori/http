<?php
require_once '../API.php';
require_once '../APIAction.php';
require_once '../APIFilter.php';
require_once '../RequestParameter.php';
foreach ($_REQUEST as $k => $v){
    echo $k .' = '.$v.'<br/>';
}
