<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS 
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

if(preg_match('/^(localhost|127\.\d\.\d\.\d|192\.168(\.\d{1,3}){2})/',$_SERVER['SERVER_NAME'])){
	$db['default']['hostname'] = 'localhost';
	$db['default']['username'] = 'root';
	$db['default']['password'] = 'root';
	$db['default']['database'] = 'possp';
	$db['default']['dbdriver'] = 'mysql';
	$db['default']['dbprefix'] = 'ospos_';
	$db['default']['pconnect'] = FALSE;
	$db['default']['db_debug'] = TRUE;
	$db['default']['cache_on'] = FALSE;
	$db['default']['cachedir'] = '';
	$db['default']['char_set'] = 'utf8';
	$db['default']['dbcollat'] = 'utf8_general_ci';
	$db['default']['swap_pre'] = '';
	$db['default']['autoinit'] = FALSE;
	$db['default']['stricton'] = FALSE;

	//Obligatorio para envio para chat entre locations, locations y envio de items entre tiendas
	$db['centralized']['hostname'] = 'localhost';
	$db['centralized']['username'] = 'root';
	$db['centralized']['password'] = 'root';
	$db['centralized']['database'] = 'possp_centralized';
	$db['centralized']['dbdriver'] = 'mysql';
	$db['centralized']['dbprefix'] = 'ospos_';
	$db['centralized']['pconnect'] = FALSE;
	$db['centralized']['db_debug'] = TRUE;
	$db['centralized']['cache_on'] = FALSE;
	$db['centralized']['cachedir'] = '';
	$db['centralized']['char_set'] = 'utf8';
	$db['centralized']['dbcollat'] = 'utf8_general_ci';
	$db['centralized']['swap_pre'] = '';
	$db['centralized']['autoinit'] = FALSE;
	$db['centralized']['stricton'] = FALSE;
}else{
	$db['default']['hostname'] = 'localhost';
	$db['default']['username'] = 'arepa_ausrdb';
	$db['default']['password'] = '732gx2401mup';
	$db['default']['database'] = 'arepa_apos';
	$db['default']['dbdriver'] = 'mysql';
	$db['default']['dbprefix'] = 'ospos_';
	$db['default']['pconnect'] = FALSE;
	$db['default']['db_debug'] = TRUE;
	$db['default']['cache_on'] = FALSE;
	$db['default']['cachedir'] = '';
	$db['default']['char_set'] = 'utf8';
	$db['default']['dbcollat'] = 'utf8_general_ci';
	$db['default']['swap_pre'] = '';
	$db['default']['autoinit'] = TRUE;
	$db['default']['stricton'] = FALSE;
 
	//Obligatorio para envio para chat entre locations, locations y envio de items entre tiendas 
	$db['centralized']['hostname'] = 'localhost';
	$db['centralized']['username'] = 'arepa_ausrdb';
	$db['centralized']['password'] = '732gx2401mup';
	$db['centralized']['database'] = 'arepa_aposcentral';
	$db['centralized']['dbdriver'] = 'mysql';
	$db['centralized']['dbprefix'] = 'ospos_';
	$db['centralized']['pconnect'] = FALSE;
	$db['centralized']['db_debug'] = TRUE;
	$db['centralized']['cache_on'] = FALSE;
	$db['centralized']['cachedir'] = '';
	$db['centralized']['char_set'] = 'utf8';
	$db['centralized']['dbcollat'] = 'utf8_general_ci';
	$db['centralized']['swap_pre'] = '';
	$db['centralized']['autoinit'] = FALSE;
	$db['centralized']['stricton'] = FALSE;
}

//Carga grupos de db para diferentes locaciones
@$mysql = new mysqli($db['centralized']['hostname'], $db['centralized']['username'], $db['centralized']['password'], $db['centralized']['database']);

if ($mysql->connect_error === NULL) {
	$query = "SELECT * FROM ospos_locations WHERE active = '1'";
//echo '<pre>'; print_r($db); echo '</pre>';
	if ($result = $mysql->query($query)) {
		while ($row = $result->fetch_assoc()) {
			foreach ($row as $key => $value) {
				if ($key == 'name')$group_name = $value;
				$db[$group_name][$key] = $value;
			}
			$db[$group_name]['pconnect'] = $db['default']['pconnect'];
			$db[$group_name]['db_debug'] = $db['default']['db_debug'];
			$db[$group_name]['cache_on'] = $db['default']['cache_on'];
			$db[$group_name]['cachedir'] = $db['default']['cachedir'];
			$db[$group_name]['char_set'] = $db['default']['char_set'];
			$db[$group_name]['dbcollat'] = $db['default']['dbcollat'];
			$db[$group_name]['swap_pre'] = $db['default']['swap_pre'];
			$db[$group_name]['autoinit'] = $db['default']['autoinit'];
			$db[$group_name]['stricton'] = $db['default']['stricton'];
		}
	}
	$mysql->close();
}



