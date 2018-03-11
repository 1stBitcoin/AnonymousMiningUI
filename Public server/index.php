<?php
ini_set('display_errors', false);
$walletgraph=$_GET['graph'];
$address=$_GET['worker'];
if($_GET['worker']==''){ $address=$_GET['address']; }

require_once("cfg/config.php");

try {
    $sql = new PDO($dbdsn, $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true,
                                                   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						   PDO::ATTR_TIMEOUT => 5));
} catch(PDOException $e) {
    die("Can't connect to database.");
}

$salt = sha1('Love');
include("functions.php");

////////////// Getting POOLS from DB ////////////// 
$statement = $sql->prepare("select * from pools where option = :option");
$statement->execute(array(':option' => $pooloption));
$pools = array();
$pools = $statement->fetchAll();

$sortArray = array();
foreach($pools as $pool){
    foreach($pool as $key=>$value){
        if(!isset($sortArray[$key])){
            $sortArray[$key] = array();
        }
        $sortArray[$key][] = $value;
    }
}

$orderby = "name"; //change this to whatever key you want from the array
array_multisort($sortArray[$orderby],SORT_ASC,$pools); 

if($address){
////////////// Getting WORKER DATA from DB
$statement = $sql->prepare("select timestamp AS date, hashrate, invalidshares from workers where address = :address ORDER BY timestamp desc LIMIT 2160");
$statement->execute(array(':address' => $address));
$worker_data = $statement->fetchAll(PDO::FETCH_ASSOC);

////////////// Getting WORKER DATA from DB
$statement = $sql->prepare("select timestamp AS date, hashrate, diff from workers where address = :address ORDER BY timestamp desc LIMIT 2160");
$statement->execute(array(':address' => $address));
$worker_dataDiff = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement = $sql->prepare("select hashrate from workers where address = :address ORDER BY timestamp desc LIMIT 100");
$statement->execute(array(':address' => $address));
$worker_data2 = $statement->fetchAll();
}

if($walletgraph){
////////////// Getting WORKER DATA from DB
$statement = $sql->prepare("select timestamp AS date, global, local, diff from wallet_status where name = :name ORDER BY timestamp desc LIMIT 2160");
$statement->execute(array(':name' => $walletgraph));
$walletgraph_data = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement = $sql->prepare("select global from wallet_status where name = :name ORDER BY timestamp desc LIMIT 100");
$statement->execute(array(':name' => $walletgraph));
$walletgraph_data2 = $statement->fetchAll();

$statement = $sql->prepare("select local from wallet_status where name = :name ORDER BY timestamp desc LIMIT 100");
$statement->execute(array(':name' => $walletgraph));
$walletgraph_dataLocal = $statement->fetchAll();
}

$salty = sha1('Love');
if($salt == $salty){
	function walletstatus($name2, $sql){
		$statement = $sql->prepare("select status from wallet_status where name = :name ORDER BY timestamp asc LIMIT 45");
		$statement->execute(array(':name' => $name2));
		$status = $statement->fetchAll(PDO::FETCH_ASSOC);
	return $status;
	}

	$hipools=count(array_keys($pools));
	for ($i=0;$i<=$hipools;++$i) {
		$name1= array_values($pools)[$i]['name'];
		$walletstatusi[$name1]= walletstatus($name1, $sql);
	}
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Annonymous mining server">
<meta name="keywords" content="mining, server">
<meta name="author" content="Terminal1" >

<title>Annonymous mining server</title>

<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">

   <!-- Add to homescreen <link rel="manifest" href="manifest.json">-->


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type='text/css'/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
.faucet{line-height: 34px;width: 600px;}
.faucetbrandheadertop {margin-top: -15px;width: 104%;margin-left: -12px;font-size: 19px;}
.minu6{margin-left: -6px;}
.faucet {width: 584px;}
.faucet_info {width: 108%;font-weight: normal;}

<?php if(!$_SERVER["QUERY_STRING"]){ echo 'canvas{width:66px !important;height:66px !important;}'; } ?>

</style>

<script type="text/javascript">
$(document).ready(function(){

    $('#buttonmax').click(function(e) {  
        var inputvalue = $("#worker").val();
        window.location.replace(" index.php?worker="+inputvalue);

    });
});
</script> 

<?php if(!$_SERVER["QUERY_STRING"]){
	echo '<script type="text/javascript" src="https://files.coinmarketcap.com/static/widget/currency.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>';

}else{	echo '

<link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.21/c3.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.9/d3.min.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.21/c3.min.js"></script>

<style>
#chart{ height:777px; }
#chart .c3-line-global{stroke-width: 1.2px;}
#chart .c3-line-local {stroke-width: 1.2px;}
#chart .c3-line-hashrate{stroke-width: 1.2px;}
#chart .c3-line-invalidshares {stroke-width: 1.1px;}
#chart .c3-line-diff {stroke-width: 1.2px;}
</style>';
} ?>

<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" style="text-align: center;"> </div>
<div class="" style="margin: -18px -1px;"><div>
<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" style="margin: -16px -7px;">

<?php if($worker_data or $walletgraph_data){
	echo'<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" style="text-align: center;">';
}else{
echo'<div class="col-md-8 col-xs-12 col-sm-12 col-lg-9" style="text-align: center;">';
} ?>
<div class='whitebgo'>
<div>

<div class="" style="display: inline-block;vertical-align: top;padding:2px 0 2px;">
<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" >
<div style="margin-top:-21px;text-align: center;">
<div class='whitebgg'>
<h1 style="padding:3px;"><span style="font-size: larger;">  Welcome to crypto mining.  </span></h1>
</div></div>

<?php

if(!$_SERVER["QUERY_STRING"]){
	echo '
<div style="margin-top:-21px;text-align: center;">

<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
Stratum URL: mine.1stbitco.in</span></h3>

</div></div>
<div style="margin-top:-21px;text-align: center;">

<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
POW mining only. POS is disabled.</span></h3>

</div></div>

<div style="margin-top:-21px;text-align: center;">

<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
Variable difficulties are set to submit new share at least every 3rt second.</span></h3>
</div></div>

<div style="margin-top:-21px;text-align: center;">
<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
Payments are calculated with PROP method. 
Automatic Payouts are every &#8776; 90 Minutes.</span></h3>
</div></div>

<div style="margin-top:-21px;text-align: center;">
<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
Do not use Bitcoin address here. You have to use coin address of what you want to mine.</span></h3>
</div></div>

<div style="margin-top:-21px;text-align: center;">
<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">
Pools fee: 0%  <a href="?tos" style="font-size: 18px;"> More info in F.A.Q.</a></span></h3>
</div></div>

<div style="margin-top:-21px;text-align: center;">
<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">

<form>
<input value="" style="width:71%;color: darkcyan;margin-top: 3px;height: 44px;" type="text" name="address" id="worker">
<button type="buttonmax" style="margin-top: -4px;" id="buttonmax" class="btn btn-primary btn-lg" ><i class="fa fa-lg fa-user-secret" aria-hidden="true"></i> Check Worker stats</button>
</form>

</span></h3>

</div></div>

'; }

echo '</div></div><br>';

if($worker_data){ 
	echo '

<div style="margin-top:-21px;text-align: center;">

<div class="whitebgg">
<h2 style="padding:3px;margin-top: 10px;"><span style="font-size: 21px;">
Statistics.</span></h2>

</div></div>

<div style="margin-top:-21px;text-align: center;">
<div class="whitebgg">
<h3 style="padding:3px;margin-top: 22px;"><span style="font-size: 21px;">

<form>
<input value="" style="width:71%;color: darkcyan;margin-top: 3px;height: 44px;" type="text" name="address" id="worker">
<button type="buttonmax" style="margin-top: -4px;" id="buttonmax" class="btn btn-primary btn-lg" ><i class="fa fa-lg fa-user-secret" aria-hidden="true"></i> Check Worker stats</button>
</form>
</span></h3>
</div></div>

';} ?>

<div id="visualisation" style="margin-top: -22px;"></div>

<?php
if(!$_SERVER["QUERY_STRING"]){

$hipools=count(array_keys($pools));
for ($i=0;$i<$hipools;$i++) {
echo '<div class="item">
<div class="faucet col-md-2 col-xs-2 col-sm-2 col-lg-2" ><div class="faucetbrandheadertop">

<div class="poolchart hashing" style="margin-top: 163px;margin-left: 511px;">
<canvas id="hashrate' . $pools[$i]['symbol'] . '" width="55" height="55"></canvas></div>

<div class="poolchart hashblocks" style="margin-top: -47px;margin-left: 216px;">
<canvas id="blocks' . $pools[$i]['symbol'] . '" width="55" height="55"></canvas></div>

<span class=""><div style="margin-top: -245px;"><h1>' . ucfirst($pools[$i]['name']) . '<a href="https://coinmarketcap.com/currencies/'; 

if ($pools[$i]['name']!='denarius'){ echo $pools[$i]['name']; }else{ echo 'denarius-dnr'; }

echo '/" target="_blank"> <i class="fa fa-external-link fa-2x"></i></a></h1></div> </span>

<table class="table table-bordered" style="">
<tr><td><b class="minu6">Symbol:</b> ' . $pools[$i]['symbol'] . '</td> <td style="width: 255px;"><b class="minu6">Algorithm:</b> ' . $pools[$i]['algorithm'] . '</td>
 <td style=""><b class="minu6">Graph:</b>&nbsp;&nbsp;<a href="?graph=' . $pools[$i]['name'] . '" style="color:gold;"><i class="fa fa-lg fa-area-chart" aria-hidden="true"></i></a></td>
</tr></table>

<table class="table table-bordered" style="margin-top: -21px;">
<tr><td style="text-align: left;"><b class="minu6">Pending Blocks:</b> ' . $pools[$i]['pending'] . '</td> <td><b class="minu6">Workers:</b> ' . $pools[$i]['workerCount'] . '</td> 
<td><b class="minu6">Wallet status:</b> ';
if($pools[$i]['global']=='0' or $pools[$i]['global']=='' or (strpos($pools[$i]['global'],'Failed') !== false)){
    echo ' <i class="fa fa-lg fa-frown-o" aria-hidden="true"></i> Offline..';
}else{

$walleti = $pools[$i]['name'];
$wallett= $walletstatusi[$walleti];
$hinow = count($wallett);

for ($row = $number_of_ok[$walleti] = 0; $row < $hinow; $row++) {
    if($wallett[$row]["status"]=="ok") {
         $number_of_ok[$walleti]++;
    }
}

for ($row = $number_of_ok5[$walleti] = 0; $row < 5; $row++) {
    if($wallett[$row]["status"]=="ok") {
         $number_of_ok5[$walleti]++;
    }
}

$now = time();
$before = strtotime($pools[$i]['timestamp']);
$diff_seconds = $now - $before;
$diff_minutes = $diff / 60;
$last_update=round($diff_seconds , 0);

if($number_of_ok[$walleti] < 10) {
    echo '<b style="color:red;"> <i class="fa fa-lg fa-frown-o" aria-hidden="true"></i> Syncing..</b>';
}elseif($number_of_ok[$walleti] > 9 && $number_of_ok5[$walleti] < 3 or $last_update > 180){
    echo '<b style="color:gold;"> <i class="fa fa-lg fa-meh-o" aria-hidden="true"></i> Syncing..</b>';
}elseif($number_of_ok[$walleti] < 40 && $number_of_ok5[$walleti] > 3){
    echo '<b style="color:gold;"> <i class="fa fa-lg fa-smile-o" aria-hidden="true"></i> Syncing...</b>';
}elseif($number_of_ok[$walleti] >= 41 && $number_of_ok5[$walleti] > 3){
    echo '<b style="color:green;"> <i class="fa fa-lg fa-smile-o" aria-hidden="true"></i> Fine</b>';
}

}
echo '</td></tr></table>


<table class="table table-bordered" style="text-align: left;margin-top: -21px;">
<tr><td style="width: 50%;">'; ?>
<script type="text/javascript">
var oilCanvas = document.getElementById("blocks<?php echo $pools[$i]['symbol']; ?>");
var Data<?php echo $pools[$i]['symbol']; ?> = {
    datasets: [
        {
            data: [<?php echo round($pools[$i]['confirmed'], 0); ?>, <?php echo round($pools[$i]['orphaned'], 0); ?>],
            backgroundColor: [
                "#63FF84",
                "#FF6384"
            ]
        }]
};

var pieChart = new Chart(oilCanvas, {
  type: 'pie',
  data: Data<?php echo $pools[$i]['symbol']; ?>,
                options: {
	responsive: true,
                }
});
</script>
<?php

echo '<b class="minu6">Confirmed Blocks:</b> ' . $pools[$i]['confirmed'] . '</td> <td style="position: absolute;margin: -1px;width: 49.3%;"><b class="minu6">Hashrate:</b> ' . $pools[$i]['realhashrate']; 
echo ' &#8776; ';
if($pools[$i]['global']!='0'){
echo round(round(($pools[$i]['hashrate'] / $pools[$i]['global'] * 100), 3)+0.0001, 3);
}
echo ' %';?>

<script type="text/javascript">
var oilCanvas = document.getElementById("hashrate<?php echo $pools[$i]['symbol']; ?>");
var Data<?php echo $pools[$i]['symbol']; ?> = {
    datasets: [
        {
            data: [<?php echo round($pools[$i]['hashrate'], 0); ?>, <?php echo round($pools[$i]['global'] - round($pools[$i]['hashrate'], 0), 0); ?>],
            backgroundColor: [
                "#FF6384",
                "#63FF84"
            ]
        }]
};

var pieChart = new Chart(oilCanvas, {
  type: 'pie',
  data: Data<?php echo $pools[$i]['symbol']; ?>,
                options: {
	responsive: true,
                }
});
</script>
</td></tr>
<tr><td style="position: absolute;margin: -1px;width: 49.4%;"><b class="minu6">Orphaned Blocks:</b><?php echo $pools[$i]['orphaned'] . ' &#8776; ' . '
';
echo round($pools[$i]['orphaned'] / $pools[$i]['confirmed'] * 100, 2);
echo ' % </td> <td><b class="minu6">Globalrate:</b> ' . $pools[$i]['realglobal'] . '</td></tr>
</table>

<table class="table table-bordered" style="font-size: 21px;margin-top: -21px;">
<tr><td><b style="font-size: 18px;">Updated:  ';
echo $last_update . ' Sec. ago </b></td>
<td><b style="font-size: 18px;">Global Difficulty:  ';
echo round($pools[$i]['diff'], 3) . '</b></td></tr>
</table>

<table class="table table-bordered" style="margin-top: -21px;">
<tr><td><b style="font-size: 27px;">Total Paid: ' . round($pools[$i]['totalPaid'], 8) . ' ' . $pools[$i]['symbol'] . '</b> </td></tr>
</table> 

<table class="table table-bordered" style="margin-top: -21px;">';
if($pools[$i]['name']=='denarius'){
	echo '<tr><td><b>Stratum port:</b> </td> <td><b>Difficulty:</b></td> <td><b>RIG:</b></td></tr>
	<tr><td>2041</td> <td>Variable</td> <td>Low end GPU & CPU</td></tr>
	<tr><td>2042</td> <td>Variable</td> <td>High end GPU</td></tr>';
}

if($pools[$i]['name']=='htmlcoin'){
	echo '<tr><td><b>Stratum port:</b> </td> <td><b>Difficulty:</b></td> <td><b>RIG:</b></td></tr>
	<tr><td>2001</td> <td>Variable</td> <td> >4x GPU or ASIC</td></tr>
	<tr><td>2002</td> <td>Solid 0.005</td> <td>High end GPU</td></tr>';
}

if($pools[$i]['name']=='netcoin'){
	echo '<tr><td><b>Stratum port:</b> </td> <td><b>Difficulty:</b></td> <td><b>RIG:</b></td></tr>
	<tr><td>2381</td> <td>Variable</td> <td>ASIC >1 MH/s</td></tr>
	<tr><td>2382</td> <td>Variable</td> <td>ASIC &#8776;50 MH/s</td></tr>';
}
 ?></table></div> 



<div class="faucet_info"><div style="text-align:left;padding-top:85px;">




<div style="background: white;-moz-border-radius:13px;-webkit-border-radius:13px;border-radius:12px;margin-left:-14px;margin-top: -36px;width:571px;" class="coinmarketcap-currency-widget" data-currency="<?php 

if ($pools[$i]['name']!='denarius'){
echo $pools[$i]['name']; 
}else{
echo 'denarius-dnr';
}
?>" data-base="USD" data-secondary="BTC" data-ticker="true" data-rank="true" data-marketcap="true" data-volume="true" data-stats="USD" data-statsticker="true"></div</div></div></div></div><?php 
	}
} ?>


<div class='diss whitebgg flist' style='margin-top: 23px;'>
<div class="flistcon">
<?php if($worker_data){
$wallethack = md5($address.$salt);
if((strpos($_SERVER["QUERY_STRING"],'diff=true') !== false)){
 $data = $worker_dataDiff;
}else{ 
$data = $worker_data; 
}

$data = json_encode($data);
$filenamejson = $wallethack . '_workerstats.json';
$filedir = 'worker/';
$csvfile = fopen($filedir . $filenamejson, 'w+');
fwrite($csvfile, $data);
fclose($csvfile);

$filenamecvs = $wallethack . '_workerstats.csv';
$array = json_decode($data, true);
$f = fopen($filedir . $filenamecvs, 'w');
$firstLineKeys = false;
foreach ($array as $line){
	if (empty($firstLineKeys)){
		$firstLineKeys = array_keys($line);
		fputcsv($f, $firstLineKeys);
		$firstLineKeys = array_flip($firstLineKeys);
	}
	fputcsv($f, array_merge($firstLineKeys, $line));
}
fclose($f);

$average = array_sum($worker_data2['0'])/100;


echo '<p> Download statistics in raw json format: <a href="downloads.php?file=' . $filenamejson . '"> <i class="fa fa-download" aria-hidden="true"></i> </a>
 / csv: <a href="downloads.php?file=' . $filenamecvs . '"> <i class="fa fa-download" aria-hidden="true"></i> </a>
<div style="margin-top: -43px;margin-left: 60%;">';
if((strpos($_SERVER["QUERY_STRING"],'diff=true') !== false)){
echo 'Statistics with Invalid Shares: <a href="?address=' . $address . '"> <i class="fa fa-lg fa-align-right" aria-hidden="true"></i> </a>';
}else{
echo 'Statistics with Difficulty: <a href="?address=' . $address . '&diff=true"> <i class="fa fa-lg fa-align-right" aria-hidden="true"></i> </a>';
}
echo '</div></p>

<div id="chart" class="chart"></div>
<script>
var chart = c3.generate({

bindto: "#chart",
point: { show: false },
bar: { width: { ratio: 1 }},
data: { x: "date",
       	xFormat: "%Y-%m-%d %H:%M:%S",
       	json: ' . $data . ',
	types: { hashrate: "area-spline",
		 diff: "spline"
		 // "line", "spline", "step", "area", "area-step" are also available to stack
	},
        colors: {
            hashrate: "#103eff",
            diff: "#00ae22",
            invalidshares: "#ff1010"
        },
	axes: {	';
		if((strpos($_SERVER["QUERY_STRING"],'diff=true') !== false)){
	echo 'diff';
	}else{
	echo 'invalidshares';	}
	echo ': "y2" },
	keys: {	x: "date",
		value: [ "hashrate", "';
			if((strpos($_SERVER["QUERY_STRING"],'diff=true') !== false)){
			echo 'diff';
			}else{
			echo 'invalidshares'; 
			}
		echo '" ]
		}
	},
	zoom: { enabled: true },
	legend: { position: "right" },
	axis: {
	x: {	type: "timeseries",
		tick: {	format: function (x) {	if (x.getDate() === 1) {	return x.toLocaleDateString();	}	
		},
		count : 200,
		rotate : 50,
		format: "%e %b %Y %H:%M:%S"
		},
		height: 120,
		label: {text: "Date",
			position: "outer-center"
			}
       	},
	y: {	min: 0,
		tick: {	format: function (d) {	return  Math.round(d/' . real_hashratetDivider($average) . ') + "' . real_hashrate_SI($average) . '"; }
		},
		padding: {top: 80, bottom: 0},
		label: { // ADD
		text: "Hashrate",
		position: "outer-middle"
		}
	},
	y2: {	min: 0,
		show: true,
		padding: {top: 80, bottom: 0},
		label: {text: "';
			if((strpos($_SERVER["QUERY_STRING"],'diff=true') !== false)){
			echo 'Difficulty';
			}else{
			echo 'Invalid shares';
			}	echo '",
			position: "outer-middle"
			}
	},
},
subchart: {	show: true	}

});
 

</script>';
} 


if($walletgraph_data){
$data = $walletgraph_data;
$data = json_encode($data);
$filenamejson = $walletgraph . '_stats.json';
$filedir = 'worker/';
$csvfile = fopen($filedir . $filenamejson, 'w+');
fwrite($csvfile, $data);
fclose($csvfile);

$filenamecvs = $walletgraph . '_stats.csv';
$filetoshow = $filedir . $filenamecvs;
$array = json_decode($data, true);
$f = fopen($filedir . $filenamecvs, 'w');
$firstLineKeys = false;
foreach ($array as $line){
	if (empty($firstLineKeys)){
		$firstLineKeys = str_replace('"', '',array_keys($line));
		fputcsv($f, $firstLineKeys);
		$firstLineKeys = str_replace('"', '',array_flip($firstLineKeys));
	}
	fputcsv($f, array_merge($firstLineKeys, $line));
}
fclose($f);

$average = array_sum($walletgraph_data2['0'])/88;
$averageLocal = array_sum($walletgraph_dataLocal['0'])/16;

echo '<h2>' . ucfirst($walletgraph) . '</h2> Download statistics in raw json format: <a href="downloads.php?file=' . $filenamejson . '"> <i class="fa fa-download" aria-hidden="true"></i> </a>
 / csv: <a href="downloads.php?file=' . $filenamecvs . '"> <i class="fa fa-download" aria-hidden="true"></i> </a>
<div style="margin-top: -43px;margin-left: 60%;">';
if((strpos($_SERVER["QUERY_STRING"],'diff=false') !== false)){
echo 'Statistics with Difficulty: <a href="?graph=' . $walletgraph . '"> <i class="fa fa-lg fa-align-right" aria-hidden="true"></i> </a>';
}else{
echo 'Statistics with Local Hashrate: <a href="?graph=' . $walletgraph . '&diff=false"> <i class="fa fa-lg fa-align-right" aria-hidden="true"></i> </a>';
}
echo '</div></p>


<div id="chart" class="chart"></div>
<script>
var chart = c3.generate({
	bindto: "#chart",
	point: {
		show: false
	},
	bar: {
		width: {
		ratio: 1
		}
	},
	data: {
        	x: "date",
        	xFormat: "%Y-%m-%d %H:%M:%S",
        	json: ' . $data . ',
		types: {
			global: "area-spline",
			local: "area-spline",
			diff: "spline"
			// "line", "spline", "step", "area", "area-step" are also available to stack
		},
		keys: {
			x: "date",
			value: [ "global", "local"';
			if((strpos($_SERVER["QUERY_STRING"],'diff=false') !== false)){
			echo '';
				}else{
			echo ', "diff"';
			}
		echo ']
		},
        	colors: {	global: "#103eff",
            			diff: "#00ae22",
            			local: "#ff9627"
        	},     	
		axes: {';
			if((strpos($_SERVER["QUERY_STRING"],'diff=false') !== false)){
			echo 'local: "y2"';
			}else{
			echo 'diff: "y2"';
			}
		echo '},
		},
		zoom: {	enabled: true },
		legend: { position: "right" },
		axis: {	x: {
				type: "timeseries",
				tick: {	format: function (x) {
					if (x.getDate() === 1) { return x.toLocaleDateString();	}
					},
					count : 200,
					rotate : 50,
					format: "%e %b %Y %H:%M:%S"
				},
				height: 120,
				label: {text: "Date",
					position: "outer-center"
				}
         		},
			y: {	min: 0,
				show: true,
				tick: { format: function (d) { 
						return  Math.round(d/' . real_hashratetDivider($average) . ') + "' . real_hashrate_SI($average) . '";
						}
					},
				padding: {top: 50, bottom: 0},

				label: {text: "Global Hashrate",
					position: "outer-middle"
				}
			},
			y2: {	min: 0,
				show: true,';
				if((strpos($_SERVER["QUERY_STRING"],'diff=false') !== false)){
				echo '
				tick: {	format: function (d) { 
					return  Math.round(d/' . real_hashratetDivider($averageLocal) . ') + "' . real_hashrate_SI($averageLocal) . '";
					}
				},
				padding: {top: 80, bottom: 0},
				label: {text: "Local Hashrate",
					position: "outer-middle"
				}';
				}else{	echo '

				tick: {
				format: function (d) { 
				return  Math.round(d/1);
					}
				},
				padding: {top: 80, bottom: 0},
				label: {text: "Difficulty",
					position: "outer-middle"
				}';
				}
				echo '
			},
		},
		subchart: {	show: true	}

      });

</script>';
} ?>
</div></div> </div> </div>   
<br>
<?php

if($worker_data){
} else {
?>
<div class='whitebgg'>
<div class='diss'>
<br></div>
</div>
<?php } ?>

<br>


<div class="whitebgg"><h2>News and announcements: </h2>
<div class="annon" style="text-align: left">
<tr><td><div >
<li><h4></h4></li></div></td>
<li><h4></h4></li></div></td>
</tr></div></div>

<?php if($worker_data or $walletgraph_data){
}else{ ?>

<div class='col-md-2 col-xs-12 col-sm-12 col-lg-3' style="margin-left:-5px;">
<div class='diss whitebgg w300'>
<h2>ccminer:</h2>
<br>
<b>Example for mining HTML coins with Nvidia GPU:</b><br>
Create new file: "<b>start.bat</b>" with this contains:<br>
ccminer-x64 -a x15 -o stratum+tcp://mine.1stbitco.in:2002 -u HBLkBhaneQuPVpmateAvZF3xLwnq1vMYjK --no-extranonce <br>
Save the file and run it. Make sure you change the payoutaddress to your own.
<br><a href="https://github.com/tpruvot/ccminer/releases" target="_blank"><b>https://github.com/tpruvot/ccminer/releases</b></a>
<br>
<h2>sgminer:</h2>
<b>Example for AMD mining with sgminer:</b>
Create new file: "start.bat" with this contains:<br>
@echo off<br>
setx GPU_FORCE_64BIT_PTR 0<br>
setx GPU_MAX_HEAP_SIZE 100<br>
setx GPU_MAX_USE_SYNC_OBJECTS 1<br>
setx GPU_MAX_ALLOC_PERCENT 100<br>
setx GPU_MAX_SINGLE_ALLOC_PERCENT 100<br>
<br>
:loop<br>
sgminer -c sgminer-myown.conf --gpu-reorder<br>
echo restart miner...<br>
goto loop<br>
<br><br>

Create new file: "<b>sgminer-myown.conf</b>" with this contains:
<br>
{<br>
"pools" : [<br>
{<br>
"name" : "mine_my_1stbitcoin",<br>
"url": "stratum+tcp://mine.1stbitco.in:2002",<br>
"user": "HBLkBhaneQuPVpmateAvZF3xLwnq1vMYjK",<br>
"pass": "x",<br>
"priority": "0",<br>
"profile": "x15"<br>
}<br>
],<br>
"profiles":<br>
[<br>
{<br>
"name": "x15",<br>
"algorithm": "x15",<br>
"worksize": "512",<br>
"gpu-threads": "2"<br>
}<br>
],<br>
"default-profile": "x15",<br>
"no-extranonce": true,<br>
"gpu-platform": "-1"<br>
}<br>
<br>
Then you can run <b>start.bat</b>
<br><br>This sgminer-myown.conf is <b>optimised</b> for ATI GPU with 1024 MB RAM. 
Change the worksize & gpu-threads to give amount of RAM after multiplying.
<br><a href="https://github.com/carsenk/sph-sgminer-tribus/releases" target="_blank"><b>https://github.com/carsenk/sph-sgminer-tribus/releases</b></a>
<br>
<h2>cpuminer:</h2>
<b>Example for mining HTML coins with CPU:</b>
Create new file: "start.bat" with this contains:
cpuminer -a x15 -o stratum+tcp://mine.1stbitco.in:2002 -u HBLkBhaneQuPVpmateAvZF3xLwnq1vMYjK --no-extranonce

<a href="https://github.com/tpruvot/cpuminer-multi/releases" target="_blank"><b>https://github.com/tpruvot/cpuminer-multi/releases</b></a>
<br>



<h2>What is Crypto mining?</h2>

Mining is the process of adding transaction records to Bitcoin's public ledger of past transactions (and a "mining rig" is a colloquial metaphor for a single computer system that performs the necessary computations for "mining"). This ledger of past transactions is called the block chain as it is a chain of blocks. The block chain serves to confirm transactions to the rest of the network as having taken place. Bitcoin nodes use the block chain to distinguish legitimate Bitcoin transactions from attempts to re-spend coins that have already been spent elsewhere.

Mining is intentionally designed to be resource-intensive and difficult so that the number of blocks found each day by miners remains steady. Individual blocks must contain a proof of work to be considered valid. This proof of work is verified by other Bitcoin nodes each time they receive a block. Bitcoin uses the hashcash proof-of-work function.

The primary purpose of mining is to allow Bitcoin nodes to reach a secure, tamper-resistant consensus. Mining is also the mechanism used to introduce Bitcoins into the system: Miners are paid any transaction fees as well as a "subsidy" of newly created coins. This both serves the purpose of disseminating new coins in a decentralized manner as well as motivating people to provide security for the system.

Bitcoin mining is so called because it resembles the mining of other commodities: it requires exertion and it slowly makes new currency available at a rate that resembles the rate at which commodities like gold are mined from the ground. <br>

Source: https://en.bitcoin.it/wiki/Mining



<br>


<div style="margin-top:10px;text-align: center;display: inline-block;">

<div class=''>

 </div>
 </div>
<!--<br><b>Advertisement:</b>
-->
<br>
<br> 
<br>

 </div>
  
    </div></div>
</div>



<?php } ?>


<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" style="text-align: center;">

<div class="col-md-10 col-xs-10 col-sm-10 col-lg-10"><br>

</div>


</div>





<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12 center" style="margin-top:-10px;">

<div  id="footer" class="whitebg">


</div></div>

<script type="text/javascript">
$(".itemo").mouseover(function() {
    $(this).children(".description").show();
}).mouseout(function() {
    $(this).children(".description").hide();
});



$(function () {
  $('[data-toggle="popover"]').popover()
});


$(function(){
	$("a.vote_up").click(function(){
	//get the id
	the_id = $(this).attr('id');
	
	// show the spinner
	$(this).parent().html("<i class='fa fa-linux fa-spin'></i>");
	
	//fadeout the vote-count 
	$("span#votes_count"+the_id).fadeOut("fast");
	
	//the main ajax request
		$.ajax({
			type: "POST",
			data: "action=vote_up&id="+$(this).attr("id"),
			url: "list/votes.php",
			success: function(msg)
			{
				$("div#votes_count"+the_id).html(msg);
				//fadein the vote count
				$("div#votes_count"+the_id).fadeIn();
				//remove the spinner
				$("span#vote_buttons"+the_id).remove();
			}
		});
	});
	
	$("a.vote_down").click(function(){
	//get the id
	the_id = $(this).attr('id');
	
	// show the spinner
	$(this).parent().html("<i class='fa fa-linux fa-spin'></i>");
	
	//the main ajax request
		$.ajax({
			type: "POST",
			data: "action=vote_down&id="+$(this).attr("id"),
			url: "list/votes.php",
			success: function(msg)
			{
				$("div#votes_count"+the_id).fadeOut();
				$("div#votes_count"+the_id).html(msg);
				$("div#votes_count"+the_id).fadeIn();
				$("span#vote_buttons"+the_id).remove();
			}
		});
	});
});

</script>

</body>
</html>