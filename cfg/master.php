<?php
/*
| -------------------------------------------------------------------
| MASTER CONFIGURATION
| -------------------------------------------------------------------
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

// -- App Info
define('APP_TITLE'  , 'e-SPTPD');
define('APP_NAME'   , 'os_pad'); //no space please
define('APP_CORP'   , 'openSIPKD');
define('APP_VERSION', '0.1');
define('APP_YEAR'   , '2016');
define('LICENSE_TO' , 'KOTA TANGERANG');
define('LICENSE_TO_SUB' , 'DINAS PENGELOLAAN KEUANGAN DAERAH');
define('EXT' , '.php');
// -- Module
//define('DEF_MODULE'     , 1); // 1. perencanaan 2.etc  ref => apps table
//define('SELECT_MODULE'  , TRUE);

// -- Environment
define('MY_ENV', 'development'); //development testing production
//define('MY_ENV', 'production'); //development testing production

// -- System & Application
//define('MY_SYS', 'sys213');
define('MY_APP', 'application');
define('MY_DEFAULT_CONTROLLER', 'root');
define('MY_MODULES_LOCATIONS' , '../modules/');

// -- Database
$db_debug =  (MY_ENV == 'development') ? TRUE : FALSE;
define('DB_DBUG', $db_debug);
define('DB_TYPE', 'postgre');  //mysql postgre
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
//define('DB_USER', 'simpad');
define('DB_USER', 'aagusti');
//define('DB_PASS', 'S1mp4d');
define('DB_PASS', 'a');
//define('DB_NAME', 'pad');
define('DB_NAME', 'simpad');
//define('DB_NAME', 'esptpd');

//--ESPTPD
define('ESPTPD_DB_TYPE', 'postgre');  //mysql postgre
define('ESPTPD_DB_HOST', 'localhost');
define('ESPTPD_DB_PORT', '5432');
define('ESPTPD_DB_USER', 'simpad');
define('ESPTPD_DB_PASS', 'S1mp4d');
//define('ESPTPD_DB_NAME', 'pad');
define('ESPTPD_DB_NAME', 'simpad_devel');
//define('ESPTPD_DB_NAME', 'esptpd');
//RPC
define('RPC_SERVER', 'http://localhost/~aagusti/padl-tng/api');
define('RPC_USER', 'admin');
define('RPC_PASS', 'admin');
define('RPC_AUTH', ''); //basic
define('RPC_API_KEY', '');
define('RPC_API_NAME', '');
define('RPC_SSL_VERIFY_PEER', '');
define('RPC_SSL_CAINFO', '');
//'api_key'         => 'Setec_Astronomy'
//'api_name'        => 'X-API-KEY'
//'http_user'       => 'username',
//'http_pass'       => 'password',
//'http_auth'       => 'basic',
//'ssl_verify_peer' => TRUE,
//'ssl_cainfo'      => '/certs/cert.pem'

// -- Url
$PROTOCOL = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
$SERVER   = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
$SERVER   = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $SERVER; 
$BASE_URL = $PROTOCOL . $SERVER . str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

define('MY_BASE_URL', $BASE_URL);
define('MY_INDEX_PAGE', '');

// -- Hook
define('MY_ENABLE_HOOKS', FALSE);

// -- Compress Output
define('MY_COMPRESS_OUTPUT', FALSE);

// -- Cache n minutes
define('MY_CACHE', 0);
define('MY_CACHE_PATH', 'cache/');

// -- Error Logging Threshold 0-4
$err_log = (MY_ENV == 'development') ? 4 : 0;
define('MY_LOG_THRESHOLD', $err_log);

// -- Encrypt & Security
define('MY_ENCRYPTION_KEY', 'mr34n1k');
define('MY_GLOBAL_XSS_FILTERING', TRUE);
define('MY_CSRF_PROTECTION', FALSE);
define('MY_CSRF_TOKEN_NAME', APP_NAME.'_csrf_test');
define('MY_CSRF_COOKIE_NAME', APP_NAME.'_cookie_name');
define('MY_CSRF_EXPIRE', 150);
define('MY_SESS_COOKIE_NAME', APP_NAME.'_session');
define('MY_SESS_TABLE_NAME', APP_NAME.'_session');

// -- Etc
define('ADMIN_NAME', 'Administrator');
define('ADMIN_EMAIL', 'asd@ajetjet.com');
define('ADMIN_DATE_FORMAT', '%D, %d %M %Y %H:%i');
define('ADMIN_DATE_TIME_FORMAT', 'd/m/y H:i');
define('EMAIL_POSTF', '@ajetjet.com');
define('LOGIN_ATTEMPT', 3);
define('LOGIN_ATTEMPT_EXPIRE', 20); //60*60*24);


/*
| -------------------------------------------------------------------
| SETING PER APLIKASI
| -------------------------------------------------------------------
*/

// -- PBB-BPHTB
define('KD_PROPINSI','32');
define('KD_DATI2','03');
define('BPHTB_NEED_APPROVAL', FALSE);
 
// -- POS PBB
define('DEF_POS_TYPE', 1); // 1. kanwil_kantor tp 2.etc 
if (DEF_POS_TYPE==1)
   define('POS_FIELD', 'kd_kanwil,kd_kantor,kd_tp');
else 
   define('POS_FIELD', 'kd_bank_tunggal,kd_bank_persepsi,kd_kanwil_bank,kd_kppbb_bank,kd_tp');
   
?>
