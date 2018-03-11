<?php

function make_array($json){ $data = json_decode($json, TRUE); return $data; }

function get_content($page){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $page);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);   
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);
	$dito = curl_exec($ch);
	curl_close($ch);
return $dito;
}

function insert_rates($symbol, $data){
		global $sql;
	$q = $sql->prepare("INSERT INTO rates (symbol, data, timestamp)  "
		. "VALUES (:symbol, :data, CURRENT_TIMESTAMP())");
	$q->execute(array(
		':symbol' => $symbol,
		':data' => $data,
	));
}

function getChart($graph){
		global $sql;
	$statement = $sql->prepare("select timestamp AS date, data AS rate from rates where symbol = :symbol ORDER BY timestamp desc LIMIT 2");
	$statement->execute(array(':symbol' => $graph));
	$data = $statement->fetchAll(PDO::FETCH_ASSOC);
return $data;
}

function real_hashrateto($data){
	$data = round($data, 2);

	if($data > '1000' && $data < '1000000'){ $result = round($data/1000, 2);}
	if($data > '1000000' && $data < '1000000000'){  $result = round($data/1000000, 2);}
	if($data > '1000000000' && $data < '1000000000000'){ $result = round($data/1000000000, 2); }
	if($data > '1000000000000' && $data < '1000000000000000'){   $result = round($data/1000000000000, 2); }
	if($data > '1000000000000000' && $data < '1000000000000000000'){  $result = round($data/1000000000000000, 2);  }

	if(!$result){$result='0';}
return $result;
}

function real_hashratetDivider($data){
	$data = round($data, 2);

	if($data > '1000' && $data < '1000000'){ $result = '1000';}
	if($data > '1000000' && $data < '1000000000'){  $result = '1000000';}
	if($data > '1000000000' && $data < '1000000000000'){ $result = '1000000000'; }
	if($data > '1000000000000' && $data < '1000000000000000'){   $result = '1000000000000'; }
	if($data > '1000000000000000' && $data < '1000000000000000000'){  $result = '1000000000000000';  }

	if(!$result){$result='0';}
return $result;

}

function real_hashrate_SI($data){
	$data = round($data, 2);

	if($data > '1000' && $data < '1000000'){ $result = ' KH/s';}
	if($data > '1000000' && $data < '1000000000'){  $result = ' MH/s';}
	if($data > '1000000000' && $data < '1000000000000'){ $result = ' GH/s';}
	if($data > '1000000000000' && $data < '1000000000000000'){   $result = ' TH/s';}
	if($data > '1000000000000000' && $data < '1000000000000000000'){  $result = ' PH/s';  }

	if(!$result){$result='H/s';}
return $result;
}


function real_hashrate($data){
	$data = round($data, 2);

	if($data > '1000' && $data < '1000000'){ $result = round($data/1000, 2) . ' KH/s';}
	if($data > '1000000' && $data < '1000000000'){  $result = round($data/1000000, 2) . ' MH/s';}
	if($data > '1000000000' && $data < '1000000000000'){ $result = round($data/1000000000, 2) . ' GH/s';}
	if($data > '1000000000000' && $data < '1000000000000000'){   $result = round($data/1000000000000, 2) . ' TH/s';}
	if($data > '1000000000000000' && $data < '1000000000000000000'){  $result = round($data/1000000000000000, 2) . ' PH/s';  }

	if(!$result){$result='0 H/s';}
return $result;
}



?>