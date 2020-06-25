<?php

/* 
 * The MIT License
 *
 * Copyright 2018 Ibrahim.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
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
require_once $rootDir.'vendor'.$DS.'webfiori'.$DS.'jsonx'.$DS.'src'.$DS.'JsonI.php';
require_once $rootDir.'vendor'.$DS.'webfiori'.$DS.'jsonx'.$DS.'src'.$DS.'JsonX.php';
require_once $rootDir.'src'.$DS.'WebService.php';
require_once $rootDir.'src'.$DS.'APIAction.php';
require_once $rootDir.'src'.$DS.'APIFilter.php';
require_once $rootDir.'src'.$DS.'RequestParameter.php';
require_once $rootDir.'src'.$DS.'WebServices.php';
require_once $rootDir.'tests'.$DS.'SampleService.php';
fwrite($stderr,"Classes Loaded.\n");
