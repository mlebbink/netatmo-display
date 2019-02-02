<!DOCTYPE html>
<?php
	include "inc/icon.php";
  
  $lat = $obj["coord"]["lat"];
  $long = $obj["coord"]["lon"];
	date_default_timezone_set("GMT");
	$timeofday = dayOrNight($lat, $long);

	require_once("Netatmo/autoload.php");
  require_once ('Config.php');

	use Netatmo\Clients\NAWSApiClient;
	use Netatmo\Exceptions\NAClientException;
  $scope = Netatmo\Common\NAScopes::SCOPE_READ_STATION;
 
	$config = array (
		"client_id" => $clientId, 
		"client_secret" => $clientSecret, 
    "username" => $username,
    "password" => $password
	);
	$client = new NAWSApiClient($config);
	
	try
	{
		$tokens = $client->getAccessToken();
		$refresh_token = $tokens["refresh_token"];
		$access_token = $tokens["access_token"];
	}
	catch(Netatmo\Exceptions\NAClientException $ex)
	{
    echo "<b>An error occured while trying to retrieve your tokens </b><br>".$ex;
	}
	
	// load data and use first available device
	$data = $client->getData(NULL, TRUE);
	$device = $data["devices"][0];
	$indoor = $device["dashboard_data"];
	$outdoor = $device["modules"][0]["dashboard_data"];
	$rain = $device["modules"][1]["dashboard_data"];
	
	$css = "css/custom-medium.css";
	if (isset($_GET["bw"]))
	{
		$css = "css/custom-small-bw.css";
	}
	if (isset($_GET["s"]))
	{
		$css = "css/custom-small.css";
	}
	
	function dayOrNight($lat, $long)
	{
		$sunrise = date_sunrise(time(), SUNFUNCS_RET_DOUBLE, $lat, $long, 90.583333, 0);
		$sunset = date_sunset(time(), SUNFUNCS_RET_DOUBLE, $lat, $long, 90.583333, 0);
		$now = date("H") + date("i") / 60 + date("s") / 3600; 
		
		if ($sunrise < $sunset) 
		{
			if (($now > $sunrise) && ($now < $sunset)) return "day"; 
			else return "night";
		}
		else 
		{
			if (($now > $sunrise) || ($now < $sunset)) return "day"; 			
			else return "night";
		}
	} 
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Weather at <?php echo $device["station_name"]; ?></title>
	<link rel="icon" href="favicon.ico">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.10/css/weather-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $css; ?>">
	<!--[if lt IE 9]>
		<script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body class="<?php echo $timeofday; ?>"> 
	<div class="container-fluid">
		<div id="icon" class="row bg-primary" onclick="window.location.reload(true);">
			<div id="rain" class="col-xs-4 text-center">
<?php
	if (isset($rain["sum_rain_1"]))
	{
		$rainfall = $rain["sum_rain_1"];			
		if ($rainfall > 0)
		{
?>
				<span class="wi wi-raindrops" title="<?php echo round($rainfall, 1); ?> mm/h"></span>
<?php
		}
	}
?>
			</div>
			<div class="col-xs-4 text-center">
				<span class="wi <?php echo $class; ?>" title="<?php echo $alt; ?>"></span>
			</div>
			<div id="carbon" class="col-xs-4 text-center">
<?php
	$carbon = $indoor["CO2"];
	if ($carbon > 2000)
	{
?>
				<span class="wi wi-smoke" title="<?php echo $carbon; ?> ppm"></span>
<?php
	}
	
	// split integer and fraction digits of temperature
	$outdoorTemp = explode(".", $outdoor["Temperature"]);
	$outdoorTempInt = $outdoorTemp[0];
	$outdoorTempFrac = "";
	if (count($outdoorTemp) > 1) 
	{
		$outdoorTempFrac = "." . $outdoorTemp[1];
	}

  $outdoorMinTemp = explode(".", $outdoor["min_temp"]);
	$outdoorMinTempInt = $outdoorMinTemp[0];
	$outdoorMinTempFrac = "";
	if (count($outdoorMinTemp) > 1) 
	{
		$outdoorMinTempFrac = "." . $outdoorMinTemp[1];
	}
  
  $outdoorMaxTemp = explode(".", $outdoor["max_temp"]);
	$outdoorMaxTempInt = $outdoorMaxTemp[0];
	$outdoorMaxTempFrac = "";
	if (count($outdoorMaxTemp) > 1) 
	{
		$outdoorMaxTempFrac = "." . $outdoorMaxTemp[1];
	}

	$indoorTemp = explode(".", $indoor["Temperature"]);
	$indoorTempInt = $indoorTemp[0];
	$indoorTempFrac = "";
	if (count($indoorTemp) > 1) 
	{
		$indoorTempFrac = "." . $indoorTemp[1];
	}
?>
			</div>
		</div>
    <div class="row">
      <div class="col-xs-6 text-center">
        <small>OUTDOOR</small>
        <div class="row">
          <div class="col-xs-8 text-right"><h1><?php echo $outdoorTempInt; ?><span><?php echo $outdoorTempFrac; ?></span>&#176;</h1></div>
          <div class="col-xs-4">
            <div class="row">
              <div class="col text-left"><small><i class="fa fa-sort-up"></i>&nbsp;<?php echo $outdoorMaxTempInt; ?><span style="font-size: 75%;"><?php echo $outdoorMaxTempFrac; ?></span>&#176;</small></div>
            </div>
            <div class="row">
              <div class="col text-left"><small><i class="fa fa-sort-down"></i>&nbsp;<?php echo $outdoorMinTempInt; ?><span style="font-size: 75%;"><?php echo $outdoorMinTempFrac; ?></span>&#176;</small></div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6 text-center">
        <small>INDOOR</small>
        <div class="row">
          <div class="col-xs-8 text-right"><h1><?php echo $indoorTempInt; ?><span><?php echo $indoorTempFrac; ?></span>&#176;</h1></div>
          <div class="col-xs-4>
            <div class="row">
              <div class="col text-left"><small><i class="fa fa-sort-up"></i><?php echo $indoor["max_temp"]; ?>&#176;</small></div>
            </div>
            <div class="row">
              <div class="col text-left"><small><i class="fa fa-sort-down"></i><?php echo $indoor["min_temp"]; ?>&#176;</small></div>
            </div>
          </div>
        </div>
      </div>
    </div>


		<div class="row">
			<div class="col-xs-3 text-center"><small>PRESSURE</small><h4><?php echo round($indoor["Pressure"]); ?> <small>mbar</small></h4></div>
			<div class="col-xs-3 text-center"><small>HUMIDITY</small><h4><?php echo $indoor["Humidity"]; ?> <small>%</small></h4></div>
			<div class="col-xs-3 text-center"><small>NOISE</small><h4><?php echo round($indoor["Noise"]); ?> <small>dB</small></h4></div>
			<div class="col-xs-3 text-center"><small>CO2</small><h4><?php echo $indoor["CO2"]; ?> <small>ppm</small></h4></div>
		</div>
	</div>	
<?php  
	if (isset($_GET["data"]))
	{
?>
	<pre style="margin-top: 50px; width: 1280px;">
<?php 
		echo "Netatmo: \n";
		print_r($data);
		echo "OpenWeatherMap: \n";
		print_r($obj);
?>
	</pre>
<?php 
	} 
?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script>
		setInterval(function() { window.location.reload(true); }, 300000);  // refresh page every 15 minutes
	</script>
</body>
</html>
