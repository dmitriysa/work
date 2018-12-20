<?php
define('WP_API_URL', 'http://api.wordpress.org/core/version-check/1.7/');
$latestWordPressVersion = getLatestVersion();
if (empty($latestWordPressVersion)) {
	die("Failed to fetch latest version. Try again later.\n");
}
define('WPLANG_API_URL', 'https://api.wordpress.org/translations/core/1.0/?version='.$latestWordPressVersion);

function getLatestVersion() {
	$result = '';
	$curl = curl_init(WP_API_URL);
	curl_setopt($curl, CURLOPT_FAILONERROR, true); 
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	$response = curl_exec($curl);
	if (!empty($response)) {
		$response = json_decode($response, true);
	}
	if (!empty($response['offers'][0]['current'])) {
		$result = $response['offers'][0]['current'];
	}
	return $result;
}
function getLatestVersionURL() {
	$result = '';
	$curl = curl_init(WP_API_URL);
	curl_setopt($curl, CURLOPT_FAILONERROR, true); 
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	$response = curl_exec($curl);
	if (!empty($response)) {
		$response = json_decode($response, true);
	}
	if (!empty($response['offers'][0]['download'])) {
		$result = $response['offers'][0]['download'];
	}
	return $result;
}
function getLatestVersionLANG($LANG) {
	$result = '';
	$curl = curl_init(WPLANG_API_URL);
	curl_setopt($curl, CURLOPT_FAILONERROR, true); 
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
	$response = curl_exec($curl);
	if (!empty($response)) {
		$arr = json_decode($response);
	}
	if (!empty($arr)) {
		foreach($arr->translations as $item)
			{
			if($item->language == "$LANG")
				{
					$result = $item->package;
				}
			}
	}
	return $result;
}
function getfile($fileUrl, $savepath) {
	$saveTo = $savepath;
	$fp = fopen($saveTo, 'w+');
	if($fp === false){
		throw new Exception('Could not open: ' . $saveTo);
	}
	$ch = curl_init($fileUrl);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_exec($ch);
	if(curl_errno($ch)){
		throw new Exception(curl_error($ch));
	}
	$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($statusCode == 200){
		echo 'Downloaded '.$savepath.'!<br>';
	} else{
		echo "Status Code: " . $statusCode . "<br>";
	}
}
function tempdir($LANGUAGE) {
    //$tempfile=tempnam(sys_get_temp_dir(), $LANGUAGE."-");
	$tempfile=tempnam(getcwd(), $LANGUAGE."-");
    if (file_exists($tempfile)) { unlink($tempfile); }
    mkdir($tempfile);
    if (is_dir($tempfile)) { return $tempfile; }
}
function unpak($ziparc, $toloc) {
	$zip = new ZipArchive;
	$res = $zip->open($ziparc);
	if ($res === TRUE) {
		$zip->extractTo($toloc);
		$zip->close();
		echo $ziparc.' extracted<br>';
	} else {
		echo 'unzip failed!; ';
	}
}
function del($file) {
	unlink($file);
	echo $file.' deleted<br>';	
}
function unpakwp ($ziparc, $toloc) {
	  $zip = new ZipArchive;
	  $unzip = $zip->open($ziparc);
	  if($unzip === true) {
		for($i=0; $i<$zip->numFiles; $i++) {
		  $name = $zip->getNameIndex($i);
		  $parts = explode('/', $name);
		  if(count($parts) > 1) {
			array_shift($parts);
		  }
		  $file = $toloc . '/' . implode('/', $parts);
		  $dir = dirname($file);
		  if(!is_dir($dir))
			mkdir($dir, 0777, true);
		  if(substr($file, -1) == "/") {
			if(!is_dir($file))
			  mkdir($file, 0777, true);
		  } else {
			$fpr = $zip->getStream($name);
			$fpw = fopen($file, 'w');
			while($data = fread($fpr, 1024)) {
			  fwrite($fpw, $data);
			}
			fclose($fpr);
			fclose($fpw);
		  }
		}
		echo $ziparc.' extracted<br>';
	  } else {
		echo 'unzip failed.';
	}
}
function append_ua_hostenko($UA_TEMP) {
	$append_to = $UA_TEMP.'/wp-config-sample.php';
	$handle = fopen($append_to, 'a') or die('Cannot open file:  '.$append_to);
	flock($handle, LOCK_EX);
	$data = '

//content of our custom modification of wp-config-sample

';
fwrite($handle, $data);
flock( $handle, LOCK_UN );
fclose($handle);
echo "Append our custom modification code to UA distribution complete<br>";
}
function append_ru_hostenko($RU_TEMP) {
	$append_to = $RU_TEMP.'/wp-config-sample.php';
	$handle = fopen($append_to, 'a') or die('Cannot open file:  '.$append_to);
	flock($handle, LOCK_EX);
	$data = '
	
//content of our custom modification of wp-config-sample

';
fwrite($handle, $data);
flock( $handle, LOCK_UN );
fclose($handle);
echo "Append our custom modification code to RU distribution complete<br>";
}
print "Latest WordPress version: $latestWordPressVersion\n<br/>";
$latestWordPressVersionURL = getLatestVersionURL();
if (empty($latestWordPressVersionURL)) {
	die("Failed to fetch latest version URL. Try again later.\n");
}
print "Latest WordPress version URL: $latestWordPressVersionURL\n<br/>";

$latestWordPressVersionLangRU = getLatestVersionLANG('ru_RU');
if (empty($latestWordPressVersionLangRU)) {
	die("Failed to fetch latest RU version. Try again later.\n");
}
print "Latest WordPress RU localization: $latestWordPressVersionLangRU\n<br/>";
$latestWordPressVersionLangUA = getLatestVersionLANG('uk');
if (empty($latestWordPressVersionLangUA)) {
	die("Failed to fetch latest UA version. Try again later.\n");
}
print "Latest WordPress UA localization: $latestWordPressVersionLangUA\n<br/>";

if($_GET["dwnld"] == '1') {
	$EN_TEMP = tempdir('EN');
	$UA_TEMP = tempdir('UA');
	$RU_TEMP = tempdir('RU');
	getfile($latestWordPressVersionURL, 'enwordpresslatest.zip');
	getfile($latestWordPressVersionLangUA, 'ualocalization.zip');
	getfile($latestWordPressVersionLangRU, 'rulocalization.zip');
	unpakwp('enwordpresslatest.zip',$EN_TEMP);
	unpakwp('enwordpresslatest.zip',$RU_TEMP);
	unpakwp('enwordpresslatest.zip',$UA_TEMP);
	del('enwordpresslatest.zip');
	unpak('ualocalization.zip',$UA_TEMP.'/wp-content/languages/');
	unpak('rulocalization.zip',$RU_TEMP.'/wp-content/languages/');
	del('ualocalization.zip');
	del('rulocalization.zip');
	append_ua_hostenko($UA_TEMP);
	append_ru_hostenko($RU_TEMP);
}
echo "DONE";
