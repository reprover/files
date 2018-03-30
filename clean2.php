<?php

function load_json($file){
	$result = file_get_contents($file.".json");
	return json_decode($result, true);
}

//第一个长数据，第二个短数据
function judge_equal($first,$second){
	preg_match_all("/./u", $second, $arr);
	$judge = $arr[0];
	foreach ($judge as $one) {
		//分成数组一个一个匹配 有一个字不存在即不合理返回false
		if(strpos($first, $one)===false){
			return false;
		}
	}
	return true;
}

$arrWeather = load_json("b");
$arrCity = load_json("city");
$fp = fopen("result.json", "w");
foreach ($arrCity as &$province) {
	foreach ($arrWeather as $key2 => $weather) {
		if(judge_equal($province['title'],$weather['title'])){
			$province['old_code'] = $weather['code'];
			if(isset($province['child'])){
			foreach ($province['child'] as &$city) {
				$boolHasCodeCity = false;
				foreach ($weather['child'] as $weather_city) {
					if(judge_equal($city['title'],$weather_city['title'])){
						$city['old_code'] = $weather_city['code'];
						$boolHasCodeCity = true;
						foreach ($city['child'] as &$district) {
							$boolHasCodeDistrict = false;
							foreach ($weather_city['child'] as $weather_district) {
								if(judge_equal($district['title'],$weather_district['title'])){
									$district['old_code'] = $weather_district['code'];
									$boolHasCodeDistrict = true;
									continue;
								}
							}
							if(!$boolHasCodeDistrict){
								$district['old_code'] = $weather_city['code'];
							}
						}
						continue;
					}
				}
				if(!$boolHasCodeCity){
					$city['old_code']=$weather['code'];
				}
			}
			continue;
		}
	}
	}
}
fwrite($fp, json_encode($arrCity));