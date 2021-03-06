<?php
/**
 * CASH Music Release Manifest Builder
 *
 * Creates oes the release profile / manifest for each release. Includes release info as well as 
 * a file listing with MD5 hashes of each file included. Command line utility. And whatever.
 *
 * USAGE:
 * php installers/php/dev_installer.php 
 * follow prompts. 
 * 
 *
 * @package diy.org.cashmusic
 * @author CASH Music
 * @link http://cashmusic.org/
 *
 * Copyright (c) 2011, CASH Music
 * Licensed under the Affero General Public License version 3.
 * See http://www.gnu.org/licenses/agpl-3.0.html
*/
function readStdin($prompt, $valid_inputs = false, $default = '') {
	// Courtesy of http://us3.php.net/manual/en/features.commandline.io-streams.php#101307
	while(!isset($input) || (is_array($valid_inputs) && !in_array(strtolower($input), $valid_inputs))) {
		echo $prompt;
		$input = strtolower(trim(fgets(STDIN)));
		if(empty($input) && !empty($default)) {
			$input = $default;
		}
	}
	return $input;
}

// recursive rmdir:
function profile_directory($dir,$trim_from_output,&$add_to) { 
	if (is_dir($dir)) {
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != ".." && $object != ".DS_Store") { 
				if (filetype($dir."/".$object) == "dir") {
					profile_directory($dir."/".$object,$trim_from_output,$add_to); 
				} else {
					$add_to .= "\t\t\"".ltrim(str_replace($trim_from_output,'',$dir),'/')."/".$object.'":"'.md5_file($dir."/".$object)."\",\n";
				} 
			} 
		}
	} 
}

if(!defined('STDIN')) { // force CLI, the browser is *so* 2007...
	echo "Please run installer from the command line. usage:<br / >&gt; php installers/php/dev_installer.php";
} else {
	if (count($argv) < 2) {
		echo "\nWrong. Usage: php manifest_builder.php <RELEASE FILES DIRECTORY>\n";
	} else {
		echo "\n\n                       /)-_-(/\n"
			. "                        (o o)\n"
			. "                .-----__/\o/\n"
			. "               /  __      /\n"
			. "           \__/\ /  \_\ |/\n"
			. "                \/\     ||\n"
			. "            o   //     ||\n"
			. "           xxx  |\     |\ \n"
			. "\n\n"
			. "          C A S H  M U S I C\n"
			. "           RELEASE PROFILER\n\n";

		$version = readStdin('Version number: ');
		$release_date = time();
		$schema_change = readStdin("\nSchema change for upgrades? (y/n): ", false, 'n');
		$script_needed = readStdin("\nScripting needed for upgrades? (y/n): ", false, 'n');

		if ($schema_change == 'y') {
			$schema_change = 'true';
		} else {
			$schema_change = 'false';
		}

		if ($script_needed == 'y') {
			$script_needed = 'true';
		} else {
			$script_needed = 'false';
		}

		$profile = "{\n\t\"version\":$version,\n\t\"releasedate\":$release_date,\n\t\"schemachange\":$schema_change,\n\t\"scriptneeded\":$script_needed,\n\t\"blobs\":{\n";

		profile_directory($argv[1],$argv[1],$profile);
		$profile = rtrim($profile,",\n");
		$profile .= "\n\t}\n}";

		file_put_contents($argv[1].'/release_profile.json', $profile);
	}
}