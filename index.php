<?php
require_once 'common_vars.php';

if(file_exists(CONFIG_PATH.'/mysql.ini')){
    require_once API_PATH.'/api.mysql.php';
    require_once API_PATH.'/api.general.php';
    require_once API_PATH.'/api.access.php';
    require_once './auth.php';
    require_once API_PATH.'/api.frontend.php';
    require_once TEMPLATE_PATH.'/'.'theme.php';
}
else{
    require_once API_PATH.'/api.general.php';
    redirect('install.php');
}
?>
