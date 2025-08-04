<?php 
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$stderr = fopen('php://stderr', 'w');
$testsDirName = 'tests';
$rootDir = substr(__DIR__, 0, strlen(__DIR__) - strlen($testsDirName));
$DS = DIRECTORY_SEPARATOR;
$rootDirTrimmed = trim($rootDir,'/\\');
fwrite($stderr,'Include Path: \''.get_include_path().'\''."\n");

if (explode($DS, $rootDirTrimmed)[0] == 'home') {
    //linux.
    $rootDir = $DS.$rootDirTrimmed.$DS;
} else {
    $rootDir = $rootDirTrimmed.$DS;
}
define('ROOT', $rootDir);
fwrite($stderr,'Root Directory: \''.$rootDir.'\'.'."\n");
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'TestServiceObj.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'SampleServicesManager.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'AbstractNumbersService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'AddNubmersService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'SumNumbersService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'GetUserProfileService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'NoAuthService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'NotImplService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'TestUserObj.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'CreateUserProfileService.php';
require_once $rootDir.'tests'.$DS.'webfiori'.$DS.'tests'.$DS.'http'.$DS.'testServices'.$DS.'MulNubmersService.php';