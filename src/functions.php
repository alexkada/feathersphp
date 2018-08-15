<?php
//require_once "../vendor/autoload.php";

use \Feathers\Error;

function loadHook(string $hookName)
{
	$path = "hooks".DIRECTORY_SEPARATOR.$hookName.".php";
	return require($path);
}



function error(int $code, string $text)
{

}