<?php

#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

### Set refresh-time
$refreshTime = '29';

### Path to local mining server
$page1 = 'http://192.168.1.102:8080/api/stats';

### Path to local server to get RPC requests (info) from wallets
$Lserver = 'https://192.168.1.103/';

### Path to public server to insert RPC requests (info) from wallets and info from mining pool
$Pserver = 'https://192.168.1.104/feed.php';

function get_content($page){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 11);
	$dito = curl_exec($ch);
	curl_close($ch);
return $dito;
}

function make_array($json){
	$data = json_decode($json, TRUE);
return $data;
}

function getmininghash($coin){
	global $Lserver;

	$page = $Lserver . $coin . '/?q=gethashrate';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 11);
	$dito = curl_exec($ch);
	curl_close($ch);

	if($coin!='netcoin'){
		$dito = $dito * 1000000;
	}
return $dito;
}

function getblockcount($coin){
	global $Lserver;
	$page = $Lserver . $coin . '/?q=getblockcount';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 11);
	$dito = curl_exec($ch);
	curl_close($ch);
return $dito;
}

function getlastblock($coin){
	global $Lserver;
	$page = $Lserver . $coin . '/?q=getlastblock';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 11);
	$dito = curl_exec($ch);
	curl_close($ch);
return $dito;
}

function getlastdiff($coin){
	global $Lserver;
	$page = $Lserver . $coin . '/?q=getdifficulty';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 11);
	$dito = curl_exec($ch);
	curl_close($ch);
return $dito;
}

function insert_content($datan, $hash, $st, $diff){
	global $Pserver;

	$url = $Pserver;
	$data = make_array($datan);
	$hash = make_array($hash);
	$status = make_array($st);
	$diff = make_array($diff);
	$ch=curl_init($url);
	$data_string = urlencode(json_encode($data));
	$hash_string = urlencode(json_encode($hash));
	$status_string = urlencode(json_encode($status));
	$diff_string = urlencode(json_encode($diff));
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, array("customer"=>$data_string, "hash"=>$hash_string, "status"=>$status_string, "diff"=>$diff_string,));
	$result = curl_exec($ch);
	curl_close($ch);
return $result;
}

$data1 = get_content($page1);

$data1_array = make_array($data1);

$pools = array_values($data1_array)['3'];
$hipools=count(array_keys($pools));

for ($i=0;$i<$hipools;$i++) {
	$name1= array_keys($pools)[$i];
	$hasharray[$name1]= getmininghash($name1);
	$diffarray[$name1]= getlastdiff($name1);
	$now[$name1]=getblockcount($name1);
	$before[$name1]=getlastblock($name1);
	$status[$name1] = 'ok';

	if($before[$name1] !== $now[$name1]){ $status[$name1] = 'syncing'; }
	$walletstatus[$name1]= $status[$name1];
}


$hashjson=json_encode($hasharray);
$diffjson=json_encode($diffarray);
$walletstatusjson=json_encode($walletstatus);

insert_content($data1, $hashjson, $walletstatusjson, $diffjson);


echo '<head>
  <meta http-equiv="refresh" content="' . $refreshTime . '">
</head>';


?>