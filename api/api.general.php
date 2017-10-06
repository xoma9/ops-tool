<?php



function update_field_entry($module,$entryid,$fieldid){
    $fieldid = vf($fieldid,3);
    $entryid = vf($entryid,3);
    $module = vf($module,4);
    $field = get_field($module, $fieldid, $entryid);
    if (!empty($field)){
        
        $args = $field['args'];
        $script = $field['script'];
        $scriptext = explode('.', $script);
        if (isset($scriptext['1'])){
            $extension = $scriptext['1'];
        }
        else{
            $extension = '';
        }
        $interpreter = get_interpreter($extension);
        $argsforscript = parse_args($args,$module,$entryid);
        $command = $interpreter.' '.SCRIPT_PATH.'/'.$script.' '.$argsforscript;
    }
    $newvalue = shell_exec($command);
    $newvalue = str_replace("\n",'',$newvalue);
    edit_entry_field($module, $entryid, $fieldid, $newvalue);
}

function perform_action($module,$actionid,$entryid){
    $module = vf($module, 4);
    $actionid = vf($actionid, 3);
    $entryid = vf($entryid,3);
    $action = get_action($module, $actionid);
    if(get_action_right(whoami(), $module, $actionid)){
        if (!empty($action)){
            $script = $action['script'];
            $args = $action['args'];
            $scriptext = explode('.', $script);
            if (isset($scriptext['1'])){
                $extension = $scriptext['1'];
            }
            else{
                $extension = '';
            }
            $interpreter = get_interpreter($extension);
            $argsforscript = parse_args($args,$module,$entryid);
            $command = $interpreter.' '.SCRIPT_PATH.'/'.$script.' '.$argsforscript;
        }
        $result = shell_exec($command);
        render_material_window("Результат выполнения ".$command.":", $result);
    }
}


function get_interpreter($extension){
    $interpreters = parse_ini_file(CONFIG_PATH . '/interpreters.ini');
    if (isset($interpreters[$extension])){
        $interpreter = $interpreters[$extension];
    }
    else
        $interpreter = $interpreters['sh'];
    return($interpreter);
}

function parse_args($args,$module,$entryid){
    preg_match_all('/{([\s\S]+?)}/', $args, $argsarray);
    $result = $args;
    if (isset($argsarray[1])){
        foreach ($argsarray[1] as $arg){
            $argarr = explode('_', $arg);
            if (isset($argarr[0]) && isset($argarr[1])){
                $argmodule = $argarr[0];
                if ($argmodule != $module){
                    if (isset($argarr[2])){
                        $entryid = $argarr[2];
                    }
                }
                $fieldid = $argarr[1];
                $replacement = get_field_content($argmodule, $fieldid, $entryid);
                $result = str_ireplace('{'.$arg.'}', $replacement, $result);
            }
        }
        return($result);
    }
    else{
        return(false);
    }
}


function set_module_field($module,$fieldid,$fieldname,$updatable,$script,$args,$displayable){
    if ($fieldid == 0){
        $query = "INSERT INTO modules_".$module."_fields (name,updatable,script,args,displayable) VALUES ('".$fieldname."','".$updatable."','".$script."','".$args."','".$displayable."')";
    }
    else{
        $query = "UPDATE modules_".$module."_fields SET name='".$fieldname."', updatable='".$updatable."', script='".$script."', args='".$args."', displayable='".$displayable."' WHERE id='".$fieldid."'";
    }
    nr_query($query);
}


function get_field_content($module,$fieldid,$moduleentryid){
    $module = vf($module, 4);
    $fieldid = vf($fieldid, 3);
    $moduleentryid = vf($moduleentryid, 3);
    $table = table_exists('modules_'.$module.'_field_entries');
    if ($table){
        $query = "SELECT content FROM modules_".$module."_field_entries WHERE field_id='".$fieldid."' AND module_entry_id='".$moduleentryid."'";
        $result = simple_query($query);
        $result = $result['content'];
    }
    else{
        $result = "";
    }
    return($result);
}

function get_field($module,$fieldid){
    $module = vf($module, 4);
    $fieldid = vf($fieldid, 3);
    $moduleentryid = vf($moduleentryid, 3);
    $table = table_exists('modules_'.$module.'_field_entries');
    if ($table){
        $query = "SELECT * FROM modules_".$module."_fields WHERE id='".$fieldid."'";
        $result = simple_query($query);
    }
    else{
        $result = "";
    }
    return($result);
}

function get_displayable_fields($module){
    $module = vf($module, 4);
    $table = table_exists('modules_'.$module.'_fields');
    if ($table){
        $query = "SELECT * FROM modules_".$module."_fields WHERE displayable='1'";
        $result = simple_queryall($query);
    }
    else
    {
        $result = false;
    }
    return $result; 
}



function get_fields($module){
    $module = vf($module, 4);
    $table = table_exists('modules_'.$module.'_fields');
    if ($table){
        $query = "SELECT * FROM modules_".$module."_fields";
        $result = simple_queryall($query);
    }
    else
    {
        $result = false;
    }
    return $result; 
}


function get_module_entries($module){
    $module = vf($module, 4);
    $table = table_exists('modules_'.$module.'_entries');
    if ($table){
        $query = "SELECT * FROM modules_".$module."_entries";
        $result = simple_queryall($query);
    }
    else
    {
        $result = false;
    }
    return $result;
}

function create_module_entry($module,$entryname){
    $entryname = mysql_real_escape_string($entryname);
    $query = "INSERT INTO modules_".$module."_entries (name) VALUES ('".$entryname."')";
    nr_query($query);
}

function create_module($module,$visiblename,$category = 'modules'){
    $module = vf($module, 4);
    if(module_exists($module)){
        $result = false;
    }
    else{
        create_all_module_tables($module);
        mkdir(MODULE_PATH.'/'.$module);
        make_module_files($module, $visiblename, $category);
        chmod(MODULE_PATH.'/'.$module,0755);
    }
}

function delete_module($module){
    if(module_exists($module)){
        $result = false;
    }
    else{
        delete_all_module_tables($module);
        $moduledir=MODULE_PATH.'/'.$module;
        unlink($moduledir.'/moduleconfig.ini');
        unlink($moduledir.'/index.php');
        rmdir($moduledir);
    }
}

function category_exist($categoryid){
    $exist = 0;
    $allmodules = get_all_category_ids();
    foreach ($allmodules as $existingcategoryid => $module){
        //print_r($existingcategoryid);
        //print_r($categoryid);
        if ($existingcategoryid == $categoryid){
            $exist++;
        }
    }
    if ($exist != 0){
        return(true);
    }
    else{
        return(false);
    }
}


function make_module_files($module,$visiblename,$category='modules'){
    copy(BASE_PATH.'/moduletemplate.php', MODULE_PATH.'/'.$module.'/index.php');
    $ini = 'modulename = "'.$visiblename.'"'.PHP_EOL
    .      'category="'.$category.'"';
    file_put_contents(MODULE_PATH.'/'.$module.'/moduleconfig.ini',$ini);
    chmod(MODULE_PATH.'/'.$module.'/moduleconfig.ini',0555);
    chmod(MODULE_PATH.'/'.$module.'/index.php',0755);
}


function edit_entry_field($module,$entryid,$fieldid,$newcontent){
    $module = mysql_real_escape_string($module);
    $fieldid = mysql_real_escape_string($fieldid);
    $entryid = mysql_real_escape_string($entryid);
    $newcontent = mysql_real_escape_string($newcontent);
    $query = "SELECT id FROM modules_".$module."_field_entries WHERE field_id='".$fieldid."' AND module_entry_id='".$entryid."'";
    $entry = simple_query($query);
    if (!empty($entry)){
        simple_update_field("modules_".$module."_field_entries", 'content', $newcontent, "WHERE field_id='".$fieldid."' AND module_entry_id='".$entryid."'");
    }
    else{
        $query = "INSERT INTO modules_".$module."_field_entries (content,field_id,module_entry_id) VALUES ('".$newcontent."','".$fieldid."','".$entryid."')";
        nr_query($query);
    }
}

function get_entry_name($module,$entryid){
    mysql_real_escape_string($entryid);
    mysql_real_escape_string($module);
    $query = "SELECT name FROM modules_".$module."_entries WHERE id='".$entryid."'";
    $result = simple_query($query);
    $result = $result['name'];
    return($result);
}

function set_entry_name($module,$entryid,$newname){
    mysql_real_escape_string($entryid);
    mysql_real_escape_string($module);
    $query = "UPDATE modules_".$module."_entries SET name='".$newname."' WHERE id='".$entryid."'";
    nr_query($query);
}

function dir_exists($dir)
{
    $dir = vf($dir, 4);
    $path = realpath($dir);
    return ($path !== false AND is_dir($path)) ? $path : false;
}


function module_exists($module){
    $module = vf($module, 4);
    $result = dir_exists(MODULE_PATH.'/'.$module);
    return $result;
}

function get_admin_list(){
    $query = "SELECT * from users";
    $result = simple_queryall($query);
    return $result;
}

function get_all_modules(){
    $categories = parse_ini_file(CONFIG_PATH."/categories.ini",true);
    foreach ($categories as $categoryid => $category){
        if (isset($_GET['module']) && file_exists(MODULE_PATH.'/'.vf($_GET['module']).'/index.php')){
            $currentmodule = vf($_GET['module']);
        }
        else {
            $currentmodule = "index";
        }
        $modules = array_diff(scandir(MODULE_PATH), array('..', '.'));
        foreach ($modules as $module){
            $moduleconfig = parse_ini_file(MODULE_PATH."/".$module."/moduleconfig.ini");
            $modulecategory = $moduleconfig['category'];
            if ($modulecategory == $categoryid){
                $modulename = $moduleconfig['modulename'];
                $result[$categoryid][$module] = $modulename;
            }
        }
    }
    return($result);
}

function get_all_category_ids(){
    $categories = parse_ini_file(CONFIG_PATH."/categories.ini",true);
    $result = $categories;
    return($result);
}

function get_module_list(){
    $result = array_diff(scandir(MODULE_PATH), array('..', '.'));
    return($result);
}


function script_exists($script){
    $result = is_file(SCRIPT_PATH.'/'.$script);
    return($result);
}


function get_script_list(){
    $result = array_diff(scandir(SCRIPT_PATH), array('..', '.'));
    return($result);
}

function current_module(){
    $result = vf($_GET['module']);
    return($result);
}


function get_category_name($categoryid){
    $categoryid = vf($categoryid);
    $categories = parse_ini_file(CONFIG_PATH."/categories.ini",true);
    $result = "";
    foreach ($categories as $categoryinitialid => $category){
        if ($categoryinitialid == $categoryid){
            $result = $category['name'];
        }
    }
    return($result);
}


function whoami(){
    if (isset($_SESSION['login'])){
        $result = $_SESSION['login'];
    }
    else{
        $result = "";
    }
    return($result);
}

function whats_my_id(){
    $result = $_SESSION['id'];
    return($result);
}

function create_table($tablename,$columns){
    $tablename = vf($tablename);
    $columns = vf($columns);
    $query = "CREATE TABLE ".$tablename." (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, ";
    if (!table_exists($tablename)){
        $i = 0;
        $count = count($columns);
        foreach ($columns as $column => $type){
            $i++;
            if ($i == $count){
                $query .= $column." ".$type.")";
            }
            else{
                $query .= $column." ".$type.",";
            }
        }
        nr_query($query);
        $result = true;
    }
    else{
        $result = false;
    }
    return($result);
}

function create_module_right_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_rights",Array('userid' => 'INT(11)', 'user_read_right' => 'TINYINT(1)', 'user_write_right' => 'TINYINT(1)'));
}

function create_module_fields_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_fields",Array('name' => 'VARCHAR(255)', 'updatable' => 'TINYINT(1)', 'script' => 'VARCHAR(255)', 'args' => 'TEXT', 'displayable' => 'TINYINT(1)'));
}

function create_module_field_entries_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_field_entries",Array('field_id' => 'INT(11)','module_entry_id' => 'INT(11)', 'content' => 'TEXT'));
}

function create_module_field_rights_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_field_rights",Array('field_id' => 'INT(11)', 'userid' => 'INT(11)', 'user_read_right' => 'TINYINT(1)', 'user_write_right' => 'TINYINT(1)'));
}

function create_module_actions_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_actions",Array('name' =>'VARCHAR(255)', 'script' => 'VARCHAR(255)', 'args' => 'TEXT'));
}

function create_module_action_rights_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_action_rights",Array('userid' =>'INT(11)', 'action_id' => 'INT(11)', 'user_execute_right' => 'TINYINT(1)'));
}

function create_module_entries_table($module){
    $module = vf($module, 4);
    create_table("modules_".$module."_entries",Array('name' => 'VARCHAR(255)'));
}


function create_all_module_tables($module){
    create_module_actions_table($module);
    create_module_field_entries_table($module);
    create_module_field_rights_table($module);
    create_module_fields_table($module);
    create_module_right_table($module);
    create_module_entries_table($module);
    create_module_action_rights_table($module);
}

function delete_all_module_tables($module){
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_actions';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_field_entries';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_field_rights';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_action_rights';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_fields';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_rights';
    nr_query($query);
    $query = 'DROP TABLE IF EXISTS modules_'.$module.'_entries';
    nr_query($query);
    
}



function csv_to_array($filename,$delimiter) {
    if(!file_exists($filename) || !is_readable($filename)){
        return FALSE;
    }
    
    $header = NULL;
    $data = array();
    $headercouner = 1;

    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $count = count($row);
        }
    }
    $header=range(1,$count);
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

function get_all_actions($module){
    $module = vf($module, 4);
    $table = table_exists("modules_".$module."_actions");
    if ($table){
        $query = "SELECT * FROM modules_".$module."_actions";
        $result = simple_queryall($query);
    }
    else{
        $result = false;
    }
    return $result;
}

function get_action($module,$actionid){
    $module = vf($module, 4);
    $actionid = vf($actionid, 3);
    $table = table_exists("modules_".$module."_actions");
    if ($table){
        $query = "SELECT * FROM modules_".$module."_actions WHERE id='".$actionid."'";
        $result = simple_query($query);
    }
    else{
        $result = false;
    }
    return $result;
}

function set_action($module,$actionid,$script,$args,$name){
    $module = vf($module, 4);
    $actionid = vf($actionid, 3);
    $script = mysql_real_escape_string($script);
    $args = mysql_real_escape_string($args);
    $name = mysql_real_escape_string($name);
    $table = table_exists("modules_".$module."_actions");
    if ($table){
        $query = "SELECT * FROM modules_".$module."_actions WHERE id='".$actionid."'";
        $entry = simple_query($query);
        if (!empty($entry)){
            $query = "UPDATE modules_".$module."_actions SET script='".$script."',args='".$args."',name='".$name."' WHERE id='".$actionid."'";
        }
        else{
            $query = "INSERT INTO modules_".$module."_actions (name,script,args) VALUES ('".$name."','".$script."','".$args."')";
        }
        nr_query($query);
    }
    else{
        $result = false;
    }
}

function delete_action($module, $actionid){
    $module = vf($module, 4);
    $actionid = vf($actionid, 3);
    $table = table_exists("modules_".$module."_actions");
    if ($table){
        $query = "DELETE FROM modules_".$module."_actions WHERE id='".$actionid."'";
        nr_query($query);
    }
}

function delete_field($module, $fieldid){
    $module = vf($module, 4);
    $fieldid = vf($fieldid, 3);
    $table = table_exists("modules_".$module."_fields");
    if ($table){
        $query = "DELETE FROM modules_".$module."_fields WHERE id='".$fieldid."'";
        nr_query($query);
        $query = "DELETE FROM modules_".$module."_field_entries WHERE field_id='".$fieldid."'";
        nr_query($query);
    }
}


function csv_to_mysql($filename,$delimiter,$header,$module) {
    if(!file_exists($filename) || !is_readable($filename)){
        return FALSE;
    }
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    foreach ($data as $row){
        $query = "INSERT INTO modules_".$module."_entries (name) VALUES ('".$row[0]."')";
        nr_query($query);
        $entryid = simple_get_lastid("modules_".$module."_entries");
        foreach ($row as $fieldid => $content){
            if ($fieldid != '0' || $fieldid != 'none'){
                $query = "INSERT INTO modules_".$module."_field_entries (field_id,module_entry_id,content) VALUES ('".$fieldid."','".$entryid."','".$content."')";
                nr_query($query);
            }
        }
    }
    unlink($filename);
}

function update_page(){
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"])){
        if ($_SERVER["HTTPS"] == "on")
            {
                $pageURL .= "s";
            }
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    }
    else{
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    
    print('<script type="text/javascript">
           window.location = "'.$pageURL.'"
      </script>');
}

function redirect($url){
     print('<script type="text/javascript">
           window.location = "'.$url.'"
      </script>');
}

function delete_entry($module,$entryid){
    $module = vf($module, 4);
    $entryid = vf($entryid, 3);
    $query = "DELETE FROM modules_".$module."_entries WHERE id='".$entryid."'";
    nr_query($query);
    $query = "DELETE FROM modules_".$module."_field_entries WHERE module_entry_id='".$entryid."'";
    nr_query($query);
}




