<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('easybitcoin.php');

$bitcoin = new Bitcoin('htMl5rpc','12345PasswordHere','192.168.1.51','1912');






if(isset($_GET['q'])){

$q = $_GET['q'];
  
  switch ($q) {
    case 'getdifficulty': ////////////////////////////////////////////
	  $mining_info = $bitcoin->getmininginfo();
	  $result = $mining_info['difficulty']['proof-of-work'];
      break;
    case 'gethashrate': ////////////////////////////////////////////
	  $mining_info = $bitcoin->getmininginfo();
	  $result = $mining_info['netmhashps'];
      break;
    case 'getblockcount': ////////////////////////////////////////////
	  $mining_info = $bitcoin->getmininginfo();
      $result = $mining_info['blocks'];
      break;
    case 'getlastblock': ////////////////////////////////////////////
	  $getinfo = $bitcoin->getinfo();
      $result = $getinfo['blocks'];
      break;

	}
    echo $result;
}
?>