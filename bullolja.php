<?php
exec('reset');
$marker      	= $argv['1'];
$wait 		= 10;
$has_changed 	= 0;
$now = date('d.m.Y H:i:s');

$url = "https://www.netfonds.no/quotes/ppaper.php?paper=BULL-OLJA-X5-C.NGM";

echo "Run start @ ".$now."";
echo "\nLegend\n\n";
echo "".chr(27) . "[42m" ."TODAY HIGHEST". chr(27) . "[0m"." ";
echo "".chr(27) . "[44m" ."HIGH". chr(27) . "[0m"." ";
echo "".chr(27) . "[40m" ."NO CHANGE". chr(27) . "[0m"." ";
echo "".chr(27) . "[43m" ."LOW". chr(27) . "[0m"." ";
echo "".chr(27) . "[41m" ."TODAY LOWEST". chr(27) . "[0m"." ";
echo "\n# = MARKER (if set as parameter)\n";

echo "\nBull Olja X5 C - kehitys :\n\n";

for ($x = 0; $x <= 10000; $x++) {

	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSLVERSION, 3);

	$data = curl_exec($curl);

	preg_match_all('#<tr[^>]*>(.*?)</tr>#s', $data, $matches);
	preg_match_all('/([0-9]+\.[0-9]+)/', $matches[0][14], $matches);

	$today_start    = $matches[0][7];
	$current 	= $matches[0][0];
	$today_lowest 	= $matches[0][9];
	$today_highest	= $matches[0][8];

	curl_close($curl);

	// NO CHANGE
	if($current == $today_start) {
		$out = "[40m";
	} elseif ($current > $today_start) {

                // IF HIGHEST TODAY
                if($current == $today_highest) {
                        $out = "[42m";
                } else { // HIGH
                        $out = "[44m"; // GREEN
                }

	} elseif ($current < $today_start) {

        	// IF LOWEST TODAY
        	if($current == $today_lowest) {
        	        $out = "[41m";
	        } else {
                	// IF LOW
        	        $out = "[43m"; // RED
	        }


	} else {

	}

if($current == $marker) {
        echo "".chr(27) . "$out" ."$current". chr(27) . "[0m"."#";
} else {
	echo "".chr(27) . "$out" ."$current". chr(27) . "[0m"." ";
}

if($has_changed == 0) { $has_changed = $current; }

if($current != $has_changed)  {

	// On Plus side
	if($has_changed > $today_start) {

        	$pre = round($current / $today_start * 100 - 100, 2);

	// On Negative side
	} elseif($has_changed < $today_start) {

                $pre = round($current / $today_start * 100 - 100, 2);

	} else {
	// No change

	        $pre = "0.00";

	}

	if($current > $has_changed) {

                // GOING UP
                echo "".chr(27) . "[1;32m" ."$pre%". chr(27) . "[0m"." ";

	} else {

		// GOING DOWN
                echo "".chr(27) . "[1;33m" ."$pre%". chr(27) . "[0m"." ";

	}

	if($current == $today_highest) {


//                echo "".chr(27) . "[1;32m" ."". chr(27) . "[0m"." ";

	}

	$has_changed = $current;
}

sleep ($wait);

} //for