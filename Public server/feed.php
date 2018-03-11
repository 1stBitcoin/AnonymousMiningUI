<?php

ini_set('display_errors', false);
require_once("cfg/config.php");


try {
    $sql = new PDO($dbdsn, $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true,
                                                   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						   PDO::ATTR_TIMEOUT => 5));
} catch(PDOException $e) {
    die("Can't connect to database.");
}

include("functions.php");


$default_data_query = <<<QUERY
create table if not exists pools (
  `name` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `option` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `symbol` text COLLATE utf8mb4_bin NOT NULL,
  `algorithm` text COLLATE utf8mb4_bin NOT NULL,
  `validShares` text COLLATE utf8mb4_bin NOT NULL,
  `validBlocks` text COLLATE utf8mb4_bin NOT NULL,
  `invalidShares` text COLLATE utf8mb4_bin NOT NULL,
  `invalidRate` text COLLATE utf8mb4_bin NOT NULL,
  `totalPaid` decimal(32,8) NOT NULL,
  `pending` text COLLATE utf8mb4_bin NOT NULL,
  `confirmed` text COLLATE utf8mb4_bin NOT NULL,
  `orphaned` text COLLATE utf8mb4_bin NOT NULL,
  `hashrate` text COLLATE utf8mb4_bin NOT NULL,
  `realhashrate` text COLLATE utf8mb4_bin NOT NULL,
  `global` text COLLATE utf8mb4_bin NOT NULL,
  `realglobal` text COLLATE utf8mb4_bin NOT NULL,
  `workerCount` text COLLATE utf8mb4_bin NOT NULL,
  `status` text COLLATE utf8mb4_bin NOT NULL,
  `diff` text COLLATE utf8mb4_bin NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

create table if not exists workers (
	`id` float NOT NULL AUTO_INCREMENT,
	`address` varchar(112) COLLATE utf8_bin NOT NULL,
	`symbol` varchar(12) COLLATE utf8_bin NOT NULL,
	`shares` float NOT NULL,
	`invalidshares` float NOT NULL,
	`hashrate` float NOT NULL,
	`score` float NOT NULL,
	`diff` float NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

create table if not exists wallet_status (
	`name` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`status` text COLLATE utf8mb4_bin NOT NULL,
	`global` decimal(65,0) NOT NULL,
	`local` decimal(65,0) NOT NULL,
	`diff` decimal(65,3) NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

create table if not exists lifestats (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`raw` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`raw1` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`raw2` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`raw3` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`raw4` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
QUERY;

	$sql->exec($default_data_query);

# $_GET['life']   .php?life=
if(!$_GET['life']){

	$datastring = $_POST['customer'];
	$raw = json_decode( urldecode( $datastring));
	$raw = json_encode($raw , TRUE);

	$rawhash = $_POST['hash'];
	$rawhash = json_decode( urldecode( $rawhash ));
	$rawhash = json_encode($rawhash , TRUE);


	$ports = $_POST['port'];
	$ports = json_decode( urldecode( $ports ));
	$ports = json_encode($ports , TRUE);


	$rawstatus = $_POST['status'];
	$rawstatus = json_decode( urldecode( $rawstatus ));
	$rawstatus = json_encode($rawstatus , TRUE);


	$diff = $_POST['diff'];
	$diff = json_decode( urldecode( $diff ));
	$diff = json_encode($diff , TRUE);

}else{

	$raw=$_GET['life'];
	$rawhash = $_GET['hash'];
	$ports = $_GET['port'];
	$rawstatus = $_GET['status'];
	$diff = $_GET['diff'];

}

#print_r($rawhash);

$q = $sql->prepare("INSERT INTO lifestats (raw, raw1, timestamp)  "
	. "VALUES (:raw, :raw1, CURRENT_TIMESTAMP())");

$q->execute(array(
	':raw' => $raw,
	':raw1' => $diff,
));

function key_compare_func($key1, $key2){
    if ($key1 == $key2)
        return 0;
    else if ($key1 > $key2)
        return 1;
    else
        return -1;
}

function workers_from_pool($data, $poolkey){
$noworkers=array_diff_ukey($data[$poolkey], array('workers'=> 0), 'key_compare_func');
$workers=array_diff_ukey($data[$poolkey], $noworkers, 'key_compare_func');
return $workers;
}


//  for inserting worker data every time we update
function insert_worker($address, $shares, $invalidshares, $hashrate, $sys, $dif, $sql){

$statement = $sql->prepare("select * from workers where address = :address ORDER BY id DESC LIMIT 1");
$statement->execute(array(':address' => $address));
$data = $statement->fetch();


$oldscore = $data["score"];
/////////////
/// SCORE ///
/////////////
$newscore = $oldscore + $shares;



	$q = $sql->prepare("INSERT INTO workers (address, symbol, shares, invalidshares, hashrate, score, diff, timestamp)  "
		. "VALUES (:address, :symbol, :shares, :invalidshares, :hashrate, :score, :diff, CURRENT_TIMESTAMP())");
	$q->execute(array(
            ':address' => $address,
            ':symbol' => $sys,
            ':shares' => $shares,
            ':invalidshares' => $invalidshares,
            ':hashrate' => $hashrate,
            ':score' => $newscore,
            ':diff' => $dif
        ));
}


// This function serves for inserting pools data every time we update
function insert_pool($name, $symbol, $algorithm, $validShares, $validBlocks, $invalidShares, $invalidRate, $totalPaid, $pending, $confirmed, $orphaned, $hashrate, $workerCount, $global, $status, $diff, $sql){


$realglobal=real_hashrate($global);

//UPDATE OR INSERT THE POOL:


$statement = $sql->prepare("select * from pools where name = :name");
$statement->execute(array(':name' => $name));
$ispool = $statement->fetch(PDO::FETCH_ASSOC);
$namename = $ispool["name"];

echo $namename . ':' . $name . ', ' . ' ' . $diff. ', ';


$realhashrate=real_hashrate($hashrate);


$hashrate = round($hashrate, 0);


if($namename == $name){

$stmt = $sql->prepare("UPDATE pools SET symbol=?, algorithm=?, validShares=?, validBlocks=?, invalidShares=?, invalidRate=?, totalPaid=?, pending=?, confirmed=?, orphaned=?, hashrate=?, realhashrate=?, workerCount=?, global=?, realglobal=?, status=?, diff=?, `timestamp` = CURRENT_TIMESTAMP() WHERE name=?");

$stmt->execute(array($symbol, $algorithm, $validShares, $validBlocks, $invalidShares, $invalidRate, $totalPaid, $pending, $confirmed, $orphaned, $hashrate, $realhashrate, $workerCount, $global, $realglobal, $status, $diff, $name));

}else{
	$q = $sql->prepare("INSERT INTO pools (name, symbol, algorithm, validShares, validBlocks, invalidShares, invalidRate, totalPaid, pending, 
					confirmed, orphaned, hashrate, realhashrate, workerCount, global, realglobal, status, diff, timestamp)  "
		. "VALUES (:name, :symbol, :algorithm, :validShares, :validBlocks, :invalidShares, :invalidRate, :totalPaid, :pending, 
					:confirmed, :orphaned, :hashrate, :realhashrate, :workerCount, :global, :realglobal, :status, :diff, CURRENT_TIMESTAMP())");
	$q->execute(array(
            ':name' => $name,
            ':symbol' => $symbol,
            ':algorithm' => $algorithm,
            ':validShares' => $validShares,
            ':validBlocks' => $validBlocks,
            ':invalidShares' => $invalidShares,
            ':invalidRate' => $invalidRate,
            ':totalPaid' => $totalPaid,
            ':pending' => $pending,
            ':confirmed' => $confirmed,
            ':orphaned' => $orphaned,
            ':hashrate' => $hashrate,
            ':realhashrate' => $realhashrate,
            ':workerCount' => $workerCount,
            ':global' => $global,
            ':realglobal' => $realglobal,
            ':status' => $status,
            ':diff' => $diff,
        ));


	}


$q = $sql->prepare("INSERT INTO wallet_status (name, status, global, local, diff, timestamp)  "
		. "VALUES (:name, :status, :global, :local, :diff, CURRENT_TIMESTAMP())");
	$q->execute(array(
            ':name' => $name,
            ':status' => $status,
            ':global' => $global,
            ':local' => $hashrate,
            ':diff' => $diff,
        ));
}


function track_workers($pools_array, $coin, $symbool, $diff, $sql){

$data=workers_from_pool($pools_array, $coin);
$data=array_values($data)['0'];

$hi=count(array_keys($data));
for ($i=0;$i<$hi;$i++) {
$address1 = array_keys($data)[$i];
$shares1 = array_values($data)[$i]['shares'];
$invalidshares1 = array_values($data)[$i]['invalidshares'];
$hashrate1 = array_values($data)[$i]['hashrate'];

insert_worker($address1,$shares1,$invalidshares1,$hashrate1,$symbool,$diff, $sql);

}


}


//////////////////////////////////
//				//
//	END OF FUNCTIONS	//
//				//
//////////////////////////////////

echo '<br><br>';

$page1array=make_array($raw);  //makes array from json
$pools=array_values($page1array)[3];

//find pools
$hipools=count(array_keys($pools));
for ($i=0;$i<$hipools;++$i) {
#$name1= array_keys($pools)[$i];
$name1= array_values($pools)[$i]['name'];
$symbol1= array_values($pools)[$i]['symbol'];
$algorithm1= array_values($pools)[$i]['algorithm'];
$validShares1= array_values($pools)[$i]['poolStats']['validShares'];
$validBlocks1= array_values($pools)[$i]['poolStats']['validBlocks'];
$invalidShares1= array_values($pools)[$i]['poolStats']['invalidShares'];
$invalidRate1= array_values($pools)[$i]['poolStats']['invalidRate'];
$totalPaid1= array_values($pools)[$i]['poolStats']['totalPaid'];
$pending1= array_values($pools)[$i]['blocks']['pending'];
$confirmed1= array_values($pools)[$i]['blocks']['confirmed'];
$orphaned1= array_values($pools)[$i]['blocks']['orphaned'];
$hashrate1= array_values($pools)[$i]['hashrate'];
$workerCount1= array_values($pools)[$i]['workerCount'];

$hash=make_array($rawhash);
$hash=$hash[$name1];

$diff1=make_array($diff);
$diff1=$diff1[$name1];

$status=make_array($rawstatus);
$status1=$status[$name1];
echo '<br>';


insert_pool($name1, $symbol1, $algorithm1, $validShares1, $validBlocks1, $invalidShares1, $invalidRate1, $totalPaid1, $pending1, $confirmed1, $orphaned1, $hashrate1, $workerCount1, $hash, $status1, $diff1, $sql);

#$pooool = array_values($pools)[$i];

track_workers($pools, $name1, $symbol1, $diff1, $sql);

}

$dbhost = null;
$dbuser = null;
$dbpass = null;
$dbname = null;
$dbname2 = null;
$dbdsn2 = null;
$dbdsn = null;
$q  = null;
$sql  = null;
$statement = null;


die();

?>







