<?php
define('SOFTNAME', 'MPRS 7.0');
define('COPYRIGHT', 'Copyright 2007-2017');
define('STD_RETURN', true);
define('LT_FULL_HEADER', true);
define('DEFAULT_SCHEMA', 'mprs');

define('RUTA', '/mprs/');
define('RUTA_CSS', '/mprs/');
define('RUTA_JS', '/mprs/');
define('RUTA_IMG', '/mprs/images/');
define('RUTA_HOST', 'https://www.orioncorp.com.ve');

define('RUTA_LT', '../../'); // ruta absoluta a los archivos ltable_*

function lt_global()
{
	$_SESSION['useini'] = false;
	$_SESSION['inifile'] = "/etc/mprs.conf";
	$_SESSION['dburl'] = 'localhost';
	$_SESSION['dbuser'] = 'root';
	$_SESSION['dbpasswd'] = 'paracelso';
	
	$_SESSION['gps_dbexterna'] = true;
	$_SESSION['gps_dbhost'] = '172.16.2.14';
	$_SESSION['gps_dbuser'] = 'gts';
	$_SESSION['gps_dbpwd'] = 'lrp3492114';

	$_SESSION['sms_host'] = 'localhost';
	$_SESSION['sms_port'] = 28703;
	$_SESSION['sms_user'] = 'program1';
	$_SESSION['sms_pwd'] = '43912';
}
?>
