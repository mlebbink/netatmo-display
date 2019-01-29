<?php
  $cityId = "2747720";
  $apiKey="dd73216eac24fa4f319a0374a655ae38";
  $openWeatherMap="https://api.openweathermap.org/data/2.5/weather?id=".$cityId."&appid=".$apiKey;
  // init curl object
  $ch = curl_init();

  // define options
  $optArray = array(
    CURLOPT_URL => $openWeatherMap,
    CURLOPT_RETURNTRANSFER => true
  );

  // apply those options
  curl_setopt_array($ch, $optArray);

  // execute request and get response
  $result = curl_exec($ch);
  $obj = array();
  $obj = json_decode($result, true);

	//if ($count != 1) {
	//	$class = "wi-cloud-refresh";
	//	$alt = "Loading...";
	//	//header("Refresh:1");
	//} else {
		//$condition = $obj["query"]["results"]["channel"]["item"]["condition"];
		$condition = $obj["weather"][0];
		$code = $condition["id"];
		//$icon = "http://l.yimg.com/a/i/us/we/52/".&code.".gif";
		$alt = $condition["main"];
		
		// determine day or night icon
		$icon = $timeofday;
		if ($icon == "night")
		{
			// night-alt icons are prettier
			$icon .= "-alt";
		}

		// https://developer.yahoo.com/weather/documentation.html#codes
		// http://erikflowers.github.io/weather-icons/	
		// http://erikflowers.github.io/weather-icons/api-list.html
		switch ($code) {
      case 200: // thunderstorm with light rain	 11d
      case 201: // thunderstorm with rain	 11d
      case 202: // thunderstorm with heavy rain	 11d
      case 210: // light thunderstorm	 11d
      case 211: // thunderstorm	 11d
      case 212: // heavy thunderstorm	 11d
      case 221: // ragged thunderstorm	 11d
      case 230: // thunderstorm with light drizzle	 11d
      case 231: // thunderstorm with drizzle	 11d
      case 232: // thunderstorm with heavy drizzle	 11d
        $class = "wi-".$icon."-thunderstorm";
        break;
      case 300: // light intensity drizzle	 09d
      case 301: // drizzle	 09d
      case 302: // heavy intensity drizzle	 09d
      case 310:	// light intensity drizzle rain	 09d
      case 311:	// drizzle rain	 09d
      case 312: // heavy intensity drizzle rain	 09d
      case 313: // shower rain and drizzle	 09d
      case 314: // heavy shower rain and drizzle	 09d
      case 321: // shower drizzle
      case 500: // light rain
      case 501: // moderate rain
      case 502: // heavy intensity rain
      case 503: // very heavy rain
      case 504: // extreme rain
        $class = "wi-".$icon."-rain";
        break;
      case 511: // freezing rain
        $class = "wi-hail";
        break;
      case 520: // light intensity shower rain
      case 521: // shower rain
      case 522: // heavy intensity shower rain
      case 531: // ragged shower rain
        $class = "wi-showers";
        break;
      case 600: // light snow
      case 601: // snow
      case 602: // heavy snow
      case 615: // light rain and snow
      case 616: // rain and snow
        $class = "wi-rain-mix";
        break;
      case 620: // light shower snow
      case 621: // shower snow
      case 622: // heavy shower snow
        $class = "wi-".$icon."-snow";
        break;
      case 611: // sleet
      case 612: // shower sleet
        $class = "wi-sleet";
        break;
      case 701: // mist
      case 711: // smoke
      case 721: // haze
      case 731: // sand, dust whirls
      case 741: // fog
      case 751: // sand
      case 761: // dust
      case 762: // volcanic ash
      case 771: // squalls
        $class = "wi-dust";
        break;
      case 781: // tornado
        $class = "wi-tornado";
        break;
      case 800: //clear sky
        $class = "wi-day-sunny";
        break;
      case 801: //few clouds
      case 802: //scattered clouds
        $class = "wi-cloud";
        break;
      case 803: //broken clouds
      case 804: //overcast clouds
        $class = "wi-cloudy";
        break;
			case 0:
				$class = "wi-tornado";
				break;
			case 1:
			case 37:
			case 38:
			case 39:
			case 45:
			case 47:
				$class = "wi-".$icon."-storm-showers";
				break;
			case 2:
				$class = "wi-hurricane";
				break;
			case 3:
			case 4:
				$class = "wi-thunderstorm";
				break;
			case 5:
			case 6:
			case 7:
			case 18:
			case 35:
				$class = "wi-rain-mix";
				break;
			case 8:
			case 10:
			case 17:
				$class = "wi-hail";
				break;
			case 9:
			case 11:
			case 12:
			case 40:
				$class = "wi-showers";
				break;
			case 13:
			case 16:
			case 42:
			case 46:
				$class = "wi-snow";
				break;
			case 14:
				$class = "wi-".$icon."-snow";
				break;
			case 15:
			case 41:
			case 43:
				$class = "wi-snow-wind";
				break;
			case 19:
				$class = "wi-dust";
				break;
			case 11:
			case 12:
			case 40:
				$class = "wi-showers";
				break;
			case 13:
			case 16:
			case 41:
			case 43:
				$class = "wi-snow";
				break;
			case 17:
				$class = "wi-hail";
				break;
			case 19:
				$class = "wi-dust";
				break;
			case 20:
				$class = "wi-fog";
				break;
			case 21:
				$class = "wi-windy";
				break;
			case 22:
				$class = "wi-smoke";
				break;
			case 23:
			case 24:
				$class = "wi-strong-wind";
				break;
			case 25:
				$class = "wi-snowflake-cold";
				break;
			case 26:
				$class = "wi-cloudy";
				break;
			case 27:
			case 28:
			case 29:
			case 30:
				$class = "wi-".$icon."-cloudy";
				break;
			case 31:
				$class = "wi-night-clear";
				break;
			case 32:
				if ($icon == "day") $class = "wi-day-sunny";
				else $class = "wi-night-clear";
				break;
			case 33:
				$class = "wi-".$icon."-partly-cloudy";
				break;
			case 34:
			case 44:
				if ($icon == "day") $class = "wi-day-sunny-overcast";
				else $class = "wi-night-alt-partly-cloudy";
				break;				
			case 36:
				$class = "wi-hot";
				break;				
			case 44:
				$class = "wi-cloud";
				break;
			case 3200:
				$class = "wi-na";
				break;
			default: 
				$class = "wi-day-cloudy-gusts";
		}
	//}
?>
