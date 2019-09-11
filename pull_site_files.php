<?php

include "./src/interfaces/FtpInterface.php";
include "./src/Ftp.php";
include "./src/SFtp.php";

// Parse arguments
$flags = [
	"--dealer_id" => "string",
	"--site_host" => "string",
	"--username"  => "string",
	"--password"  => "string"
];

// Function to check string starting
// with given substring
function startsWith ($string, $startString)
{
	$len = strlen($startString);
	return (substr($string, 0, $len) === $startString);
}

$arguments = [];

for ($i = 0; $i < count($argv); $i++) {
	if (array_key_exists($argv[$i], $flags)) {
		$flag = ltrim($argv[$i], "-");
		$flag_type = $flags[$argv[$i]];
	} else {
		continue;
	}

	switch ($flag_type) {
		case "string":
			if (
				!empty($argv[$i+1])
				&& startsWith($argv[$i+1], "--") === false
			) {
				$arguments[$flag] =	$argv[$i+1];
			} else {
				exit("\nFlag: '$flag' cannot be empty.\n\n");
			}

			break;
		case "array":
			$j = $i+1;
			$next_arg = $argv[$j];

			while (
				startsWith($next_arg, "--") === false
				&& $j < (count($argv)-1)
			) {
				$arguments[$flag][] = $next_arg;

				$next_arg = $argv[$j+=1];
			}

			if (empty($arguments[$flag])) {
				exit("\nFlag: '$flag' cannot be empty.\n\n");
			}

			break;
	}
}

// END Parse arguments

if (empty($arguments["dealer_id"])) {
//	die("'--dealer_id' is a required flag.\n");
}

$arguments["site_host"] = "rhooks.dealereprocess.com";

$site_host = "";
if (isset($arguments["site_host"])) {
	$site_host = "sites/" . $arguments["site_host"] . "/";
}

$arguments["dealer_id"] = 1;


$prod_sites_ftp_host = "ord-webmaster-prod.dealereprocess.net";
$prod_sites_ftp_username = "tmckenzie"; //$arguments['username'];
$prod_sites_ftp_password = "DEPdev!2018"; //$arguments['password']; // TODO: read password as user input for security sake.
$prod_sites_ftp_dir = "/_ids/{$arguments["dealer_id"]}/{$site_host}";
//
//$prod_ftp = new Ftp($prod_sites_ftp_host);
//
//if (!$prod_ftp->login($prod_sites_ftp_username, $prod_sites_ftp_password)) die("Could not login to Prod FTP with {$prod_sites_ftp_username}");
//$prod_ftp->pasv(true);
//
//$prod_ftp->chdir($prod_sites_ftp_dir);
//$prod_site_files = $prod_ftp->list(".");

function download_prod_files($directory, $download_dirname, $ftp)
{
	$files_array = $ftp->list($directory);

	foreach ($files_array as $file) {
		if ($file == "." || $file == "..") continue;

		if ($ftp->isDir($directory.$file) === true) {
			if (!file_exists($download_dirname."/".$file)) mkdir($download_dirname."/".$file, 0777, true);

			download_prod_files($directory.$file,$download_dirname."/".$file, $ftp);
		} else {
			$ftp->download(rtrim($directory, "/")."/".$file, $download_dirname . "/" . $file);
		}
	}
}

$download_directory =  __DIR__ . "/" . $arguments["site_host"];
//download_prod_files($prod_sites_ftp_dir, $download_directory, $prod_ftp);

$dev_ssh_ftp = "184.106.49.15";
$dev_ssh_ftp_user = "sites"; // read from args
$dev_ssh_ftp_pass = "&YCBneKpzVF5JAd8"; // dynamic read from stdin

//$site_ftp = new SFtp($dev_ssh_ftp);
//
//if (!$site_ftp->login($dev_ssh_ftp_user, $dev_ssh_ftp_pass)) echo "could not login";

$download_dir_handle = opendir($download_directory);
echo "{$download_directory}\n";

while (($file = readdir($download_dir_handle)) !== false) {
	if ($file === "." || $file === "..") continue;

	if (is_dir($download_directory."/".$file)) {
		echo "D: {$download_directory}/{$file}\n";
	} else {
		echo "F: {$download_directory}/{$file}\n";
	}
}

closedir($download_dir_handle);

echo "done\n";
