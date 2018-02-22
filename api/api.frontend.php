<?php

function show_module(){
    global $login;
    if (isset($_GET['module']) && file_exists(MODULE_PATH.'/'.vf($_GET['module']).'/index.php') && get_right(whoami(), $_GET['module'])){
        $module = vf($_GET['module']);
    }
    else {
        $module = "index";
    }
    require_once MODULE_PATH.'/'.htmlspecialchars($module).'/index.php';
}





function render_login(){
    $result = '';
    $result = '
               <div class="row valign-wrapper-fullscreen">
		<div class="col s4 offset-s3 z-depth-3" style="margin-top:10%">
			<div class="col s12 center-align">
				<h3>Авторизация</h3>
			</div>
			<form action="/index.php" method="post" class="col s12">
			<div class="input-field col s12 align-center">
				<input id="login" type="text" name="login" class="validate">
				<label for="login">Логин</label>
			</div>
			<div class="input-field col s12 align-center">	
				<input type="password" name="password" id="password">
				<label for="password">Пароль</label>
			</div>
		<div class="row">
		<div class="col s12 center-align">
			<button class="btn waves-effect light-blue darken-2" type="submit" name="submit">
				Войти
				<i class="material-icons right">send</i>
			</button>
		</div>
	</div>
	</form>
</div>
</div>';
    print($result);
}





function render_sidebar(){

    $result = "";
    $admin = whoami();
        if(!empty($admin)){
        $categories = parse_ini_file(CONFIG_PATH."/categories.ini",true);
        foreach ($categories as $categoryid => $category){
            if (isset($_GET['module']) && file_exists(MODULE_PATH.'/'.vf($_GET['module']).'/index.php')){
                $currentmodule = vf($_GET['module']);
            }
            else {
                $currentmodule = "index";
            }
            
            $counter = 0;
            $modules = get_module_list();
            foreach ($modules as $module){
                if (is_file(MODULE_PATH."/".htmlspecialchars($module)."/moduleconfig.ini")){
                    $moduleconfig = parse_ini_file(MODULE_PATH."/".htmlspecialchars($module)."/moduleconfig.ini");
                
                    $modulecategory = $moduleconfig['category'];
                    if (get_right($admin, $module) && $modulecategory == $categoryid){
                        $counter++;
                    }
                }
            }
            
            if ($counter != 0){
                $category['name'] = htmlspecialchars($category['name']);
                $result .= "<div class='collection z-depth-3'>";
                $result .="<a href='#!' class='collection-header'><h4>".$category['name']."</h4></a>"; 
                $modules = array_diff(scandir(MODULE_PATH), array('..', '.'));
                foreach ($modules as $module){
                    if (is_file(MODULE_PATH."/".htmlspecialchars($module)."/moduleconfig.ini")){
                        $moduleconfig = parse_ini_file(MODULE_PATH."/".htmlspecialchars($module)."/moduleconfig.ini");
                        $modulecategory = $moduleconfig['category'];
                        if (get_right($admin, $module) && $modulecategory == $categoryid){
                            
                            $modulename = $moduleconfig['modulename'];
                            if ($currentmodule == $module){
                                $result .= "<a href='/index.php?module=".htmlspecialchars($module)."' class='collection-item active blue'>".htmlspecialchars($modulename)."</a>";
                            }
                            else{
                                $result .= "<a href='/index.php?module=".htmlspecialchars($module)."' class='collection-item blue-text text-darken-2 z-depth-2'>".htmlspecialchars($modulename)."</a>";
                            }
                        }
                    }
                }
                $result .= "</div>";
            }
        }
        print($result);
    }
}




function render_adminlist(){
    $admins=get_admin_list();
    print('<table class=bordered width=100%><tr><th>Администраторы</th><th></th><th></th><th></th></tr>');
    foreach ($admins as $admin){
        print('<tr><td>'.$admin['login'].'</td>');
        print('<td><a class="light-blue waves-effect btn" href=/index.php?module=admins&action=changepass&admin='.htmlspecialchars($admin['login']).'>Изменить пароль</a></td>');
        print('<td><a class="light-blue waves-effect btn" href=/index.php?module=admins&action=accessrights&admin='.htmlspecialchars($admin['login']).'>Права доступа</a></td>');
        print('<td><a class="light-blue waves-effect btn" href=/index.php?module=admins&action=deleteadmin&admin='.htmlspecialchars($admin['login']).'>Удалить</a></td></tr>');
    }
    print('<form action="index.php?module=admins&action=addadmin&admin='.whoami().'" method="post">');
    print('<tr><td><div class="input-field"><input id="newadmin" name="newadmin" type="text" class="validate"><label for="newadmin">Имя нового администратора</label></div></td><td></td><td></td><td><button class="btn waves-effect light-blue" type="submit" name="addadmin">Создать</button></td></tr>');
    print('</form>');
    print('</table>');
}





function render_logout_button(){
    $admin = whoami();
    if (!empty($admin)){
        $result="<ul class='right'>"
                . "<li></li>"
                . "<li></li>"
                . "<li><a class='light-blue waves-effect waves-light btn' href='index.php?logout=1'>".whoami().": Выход</a></li>"
                . "</ul>";
        print($result);
    }
}




function render_script_list(){
    $scripts = get_script_list();
    $result = "<table width='100%' class='centered bordered'>";
    foreach ($scripts as $script){
        $result .= "<tr>"
                . "     <td>"
                . "     ".htmlspecialchars($script)
                . "     </td>"
                . "     <td>"
                . "     <a class='waves-effect light-blue waves-light btn' href='index.php?module=scripts&action=delete&script=".htmlspecialchars($script)."'>Удалить</a>"
                . "     </td>"
                . " </tr>";
    }
    $result .= "    <tr>"
            . "         <form action='index.php?module=scripts&action=upload' method='post' enctype='multipart/form-data'>"
            . "             <td>"
            . "                 <div class='file-field input-field'>"
            . "                     <div class='btn light-blue waves-effect'>"
            . "                         <span>Выбрать файл</span>"
            . "                         <input type='file' name='uploadedscript'>"
            . "                     </div>"
            . "                     <div class='file-path-wrapper'>"
            . "                         <input class='file-path validate' type='text'>"
            . "                     </div>"
            . "                 </div>"
            . "             </td>"
            . "             <td>"
            . "                 <button class='btn waves-effect light-blue' type='submit' name='upload'>"
            . "                     Загрузить"
            . "                 </button>"
            . "             </td>"
            . "         </form>"
            . "     </tr>";
    $result .= "</table>";
    print($result);
}


function render_user_module_list(){
    $allmodules = get_all_modules();
    $result = "";
    foreach ($allmodules as $categoryid => $modules){
        if ($categoryid != 'system'){
            $categoryname = get_category_name($categoryid);
            $result .= "<div class='row'>"
                    . "     <div class='col s12'>"
                    . "         <h4>".$categoryname."</h4>"
                    . "     </div>"
                    . "</div>"
                    . "<div class='row'>"
                    . "     <div class='col s12'>"
                    . "             "
                    . "         <table class='striped'>";
            foreach ($modules as $module => $visiblename){
                $module = htmlspecialchars($module);
                $visiblename = htmlspecialchars($visiblename);
                $result .= "<tr>"
                    . "                 <td>"
                    .                       $visiblename
                    . "                 </td>"
                    . "                 <td class=center-align>"
                    . "                     <a class='waves-effect light-blue waves-light btn' href='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=editactions'>"
                    . "                         Редактировать действия"
                    . "                     </a>"
                    . "                     <p></p>"
                    . "                     <a class='waves-effect light-blue waves-light btn' href='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=editfields'>"
                    . "                         Редактировать поля"
                    . "                     </a>"
                    . "                 </td>"
                    . "                 <td class=center-align>"
                    . "                     <a class='waves-effect light-blue waves-light btn' href='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=massactions'>"
                    . "                         Массовые действия"
                    . "                     </a>"
                    . "                     <p></p>"
                    . "                     <a class='waves-effect light-blue waves-light btn' href='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=deletemodule'>"
                    . "                         Удалить модуль"
                    . "                     </a>"
                    . "                 </td>"
                    . "                 <td class=center-align>"
                    . "                     <a class='waves-effect light-blue waves-light btn' href='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=import'>"
                    . "                         Импорт данных"
                    . "                     </a>"
                    . "                 </td>"
                    . "             </tr>";
            }
            $result .= "         </table>"
                        . "    </div>"
                        . "</div>";
            
        }


    }
    $result .= "<div class='row'>"
            . "     <div class='col s12'>"
            . "     <form action='index.php?module=modules' method='post'>"
            . "     <table>";
    
    $result .= "            <tr>"
                    
                    . "                 <td>"
                    . "                     <div class='input-field'>"
                    . "                         <input id='newmodulename' name='newmodulename' type='text' class='validate'>"
                    . "                         <label for='newmodulename'>"
                    . "                             Служебное имя"
                    . "                          </label>"
                    . "                      </div>"
                    . "                 </td>"
                    . "                 <td>"
                    . "                     <div class='input-field'>"
                    . "                         <input id='newmodulevisiblename' name='newmodulevisiblename' type='text' class='validate'>"
                    . "                         <label for='newmodulevisiblename'>"
                    . "                             Видимое имя"
                    . "                          </label>"
                    . "                      </div>"
                    . "                 </td>"
                    . "                 <td>"
                    . "                     <div class='input-field'>"
                    . "                         <select name=categoryid>";
            $categories = get_all_category_ids();
            foreach ($categories as $categoryid => $categoryname){
                $categoryname = $categoryname['name'];
                $i=0;
                
                if ($categoryid != 'system'){
                    $i++;
                    if ($i == 1){
                        $result .= "                <option value='".htmlspecialchars($categoryid)."' selected>".htmlspecialchars($categoryname)."</option>";
                    }
                    else{
                        $result .= "                <option value='".$categoryid."'>".htmlspecialchars($categoryname)."</option>";
                    }
                }
            } 
            $result .= "                         </select>"
                    . "                     <label>Категория</label>"
                    . "                    </div>"
                    . "                 </td>"
                    . "                 <td>"
                    . "                     <button class='btn waves-effect light-blue' type='submit' name='createnewmodule'>"
                    . "                         Создать"
                    . "                     </button>"
                    . "                 </td>"
                    
                    . "              </tr>"
                    . "        </table>"
                    . "              </form>"
                    . "    </div>"
                    . "</div>";
    print($result);
}

function render_import($module,$csvarr=''){
    $delimiters=array(';',',','|');
    $result = "<form action='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=import' method='post' enctype='multipart/form-data'>"
            . "<table>"
            . "     <tr>"
            . "         <form action='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=import' method='post' enctype='multipart/form-data'>"
            . "             <td>"
            . "                 <div class='file-field input-field'>"
            . "                     <div class='btn light-blue waves-effect'>"
            . "                         <span>Выбрать файл</span>"
            . "                         <input type='file' name='uploadedscript'>"
            . "                     </div>"
            . "                     <div class='file-path-wrapper'>"
            . "                         <input class='file-path validate' type='text'>"
            . "                     </div>"
            . "                 </div>"
            . "             </td>"
            . "             <td width=5%>"
            . "                 <div class='input-field'>"
            . "                 <select id=delimiter name=delimiter>";
    foreach ($delimiters as $delimiter){
        $result .= "                <option value='".htmlspecialchars($delimiter)."' class=align-center>".htmlspecialchars($delimiter)."</option>";
    }
    $result .="                     "
            . "                 </select>"
            . "                 <label>Разделитель</label>"
            . "             </div>"
            . "             </td>"
            . "             <td>"
            . "                 <button class='btn waves-effect light-blue' type='submit' name='upload'>"
            . "                     Загрузить"
            . "                 </button>"
            . "             </td>"
            . "     </tr>"
            . "</table>"
            . "</form>";
    if (!empty($csvarr)){

        $fields = get_fields($module);
        $result .= "<form action='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=import' method='post'>"
                . "     <table>"
                . "         <tr>";
        foreach($csvarr[0] as $id => $column){
            $result .= "        <td>"
                    . "             <select name=columns[".htmlspecialchars($id)."]>"
                    . "                 <option value='0'>Имя в списке записей</option>"
                    . "                 <option value='none'>-----</option>";
            foreach ($fields as $field){
                $result .= "            <option value=".htmlspecialchars($field['id']).">".htmlspecialchars($field['name'])."</option>";
            }
            $result .= "            </select>"
                    . "         </td>";
        }
        $result .= "";
        $result .= "</tr>";
        $i = 0;
        foreach ($csvarr as $csventry){
            $i++;
            $result .= "<tr>";
            foreach ($csventry as $csvcolumn){
                $result .= "<td>"
                        . htmlspecialchars($csvcolumn)
                        . "</td>";
            }
            $result .= "</tr>";
        }
        $dlmtr = $_POST['delimiter'];
        $result .= "        <tr>"
                . "             <td colspan='".$i."' class=center-align>"
                . '                 <input type="hidden" name="delimiter" value="'.$dlmtr.'">'
                . "                 <button class='btn waves-effect light-blue' type='submit' name='importdata'>"
                . "                     Импортировать"
                . "                 </button>"
                . "             <td>"
                . "         </tr>";
    }
    
    
    $result .="</table>"
            . "</form>";
    print($result);
}

function render_field_rights($admin,$module){
    $fields = get_fields($module);
    if ($fields){
        $result = '<div class="row"><div class="row"><b>Поля</b></div>';
        foreach ($fields as $field){
            $result .= "<div class='row'>"
                    . "     <div class='col s6'>"
                    . "        <input type='hidden' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]' value=''>"
                    . "        <input type='hidden' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]' value=''>";
            if (get_field_right($admin, $module, $field['id'],'read',true)){
                $result .= "         <input id='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]' type='checkbox' checked='checked' class='filled-in'>"
                        . "         <label for=newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]>Чтение</label>";
            }
            else{
                $result .= ""
                        . "         <input id='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]' type='checkbox' class='filled-in'>"
                        .  "         <label for=newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][read]>Чтение</label>";
            }
            $result .= "<br>";
            if (get_field_right($admin, $module, $field['id'],'write',true)){
                $result .= "         <input id='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]' type='checkbox' checked='checked' class='filled-in'>"
                        . "         <label for=newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]>Запись</label>";
            }
            else{
                $result .= "         <input id='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]' name='newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]' type='checkbox' class='filled-in'>"
                        . "         <label for=newfieldrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($field['id'])."][write]>Запись</label>";
            }
        
        
            $result .= "</div>     <div class='col s6'><p class='valign-wrapper'>"
                    .           htmlspecialchars($field['name'])
                    ."     </p></div>"
                    . "</div>";
        }
        $result .= "</div>";
        print($result);
    }
}

function render_action_rights($admin,$module){
    $actions = get_all_actions($module);
    if ($actions){
        $result = '<div class="row"><div class="row"><b>Действия</b></div>';
        foreach ($actions as $action){
            $result .= "<div class='row'>"
                    . "     <div class='col s12'>"
                    . "        <input type='hidden' name='newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]' value=''>";
            if (get_action_right($admin, $module, $action['id'],'execute',true)){
                $result .= "         <input id='newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]' name='newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]' type='checkbox' checked='checked' class='filled-in'>"
                        . "         <label for=newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]>".htmlspecialchars($action['name'])."</label>";
            }
            else{
                $result .= "         <input id='newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]' name='newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]' type='checkbox' class='filled-in'>"
                        . "         <label for=newactionrights[".$module."][".htmlspecialchars(get_user_id($admin))."][".htmlspecialchars($action['id'])."][execute]>".htmlspecialchars($action['name'])."</label>";
            }
        
        
            $result .= "</div></div>";
        }
        $result .= "</div>";
        print($result);
    }
}


function message_box($text){
    $result = '<script>alert("'.htmlspecialchars($text).'");</script>';
    print($result);
}

function render_module($module,$massaction = '0'){
    $fields = get_displayable_fields($module);
    $result = "<div class='row'>";
        if ($massaction == 1){
        $result .= "<form action='index.php?module=modules&moduleedit=".htmlspecialchars($module)."&action=massactions' method=post>";
    }
    else{
        $result .= "<form action='index.php?module=".htmlspecialchars($module)."' method=post>";
    }
    $result .= "<table id='sortable'>"
            . "<thead>"
            . "     <tr>"
            . "         <th width=2%>ID</th>"
            . "         <th class=head>Имя</th>";
    if ($fields != false){
		$fieldrigts = Array();
        foreach ($fields as $field){
			$fieldrights[$field['id']]=get_field_right(whoami(), $module, $field['id']);
            if($fieldrights[$field['id']]){
                $result .= '<th>'.htmlspecialchars($field['name']).'</th>';
            }
        }    
    }
    if ($massaction == '1'){
        $result .= "<th class='left-align sorting_disabled'>"
                . "     <input type='checkbox' id='checkall' name='checkall' class='filled-in' onchange='checkAll(this)'>"
                . "     <label for='checkall'></label>"
                . "</th>";
        }
    $result .= "</tr>"
            . "</thead>"
            . "<tbody>";

    $entries = get_module_entries($module);
    if(!empty($entries)){
		$allcontent=get_all_field_content($module);
		//print_r($allcontent);
        foreach ($entries as $entryarr){
            $entryid = $entryarr['id'];
            $entryname = $entryarr['name'];
            $result .= "<tr>"
                    . "<td>"
                    . "<a href='index.php?module=".htmlspecialchars($module)."&entry=".htmlspecialchars($entryid)."'>".htmlspecialchars($entryid)."</a>"
                    . " </td>"
                    . " <td>"
                    . "<a href='index.php?module=".htmlspecialchars($module)."&entry=".htmlspecialchars($entryid)."'>".$entryname."</a>"
                    . " </td>";
            if ($fields != false){
                foreach ($fields as $field){
                        if($fieldrights[$field['id']]){
                        $fieldcontent = $allcontent[$entryid][$field['id']];
                        $result .= '<td>'.$fieldcontent.'</td>';
                    }
                }
            }
            if ($massaction == '1'){
                $result .= "<td class=left-align>"
                        . "     <input type='checkbox' id='checked[".htmlspecialchars($entryid)."]' name='checked[".htmlspecialchars($entryid)."]' class='filled-in'>"
                        . "<label for='checked[".htmlspecialchars($entryid)."]'></label>"
                        . "</td>";
            }
            $result .= "</tr>";

        }
        $result .= "</tbody>"
                . "</table>";
    }
    if (get_right(whoami(), $module, 'write') && $massaction == '0'){
        $result .= "</form>"
                . "<form action='index.php?module=".htmlspecialchars($module)."' method=post>"
                . "<table width=100%>"
                . "     <tr>"
                . "         <td colspan=2>"
                . "             <div class='input-field'>"
                . "                 <input id='newentryname' name='newentryname' type='text' class='validate'>"
                . "                 <label for='newentryname'>"
                . "                     Имя новой записи"
                . "                 </label>"
                . "             </div>"
                . "         </td>"
                . "         <td>"
                . "             <button class='btn waves-effect light-blue' type='submit' name='createentry'>"
                . "                 Создать"
                . "             </button>"
                . "         </td>"
                . "     </tr>"
                . "</tbody>"
                . "</table>"
                . "</form>";
    }
    
    if (get_right(whoami(), $module, 'write') && $massaction == '1'){
        $result .= " <table>"
                . "     <tr>"
                . "         <td class='center-align'>"
                . "             <button class='btn waves-effect light-blue' type='submit' name='deleteentries' style='width:100%'>"
                . "                 Удалить отмеченные"
                . "             </button>"
                . "         </td>";
        $actions = get_all_actions($module);
        if (!empty($actions)){
            $i = 0;
            $j = 0;
            foreach ($actions as $action){
                $i++;
                $result .= "<td class='center-align'>"
                        . "<button class='btn waves-effect light-blue' type='submit' name='massaction[".$action['id']."]' style='width:100%'>"
                        . htmlspecialchars($action['name'])
                        . "</button>"
                        . "</td>";
                if ($i%3 == 0 && $j == 0){
                    $result .= "</tr><tr>";
                    $j++;
                }
                elseif($i%4 == 0 && $j >= 1){
                    $result .= "</tr><tr>";
                }
            }
        }
        
        $result .= "</tr>"
                . "<tr>";
        
        $fields = get_fields($module);
        if (!empty($fields)){
            $i = 0;
            foreach ($fields as $field){
                if ($field['updatable'] == 1){
                    $i++;
                    $result .= "<td class='center-align'>"
                            . "<button class='btn waves-effect light-blue' type='submit' name='massupdatefield[".$field['id']."]' style='width:100%'>"
                            . "Обновить ".htmlspecialchars($field['name'])
                            . "</button>"
                            . "</td>";
                    if ($i%4 == 0){
                        $result .= "</tr><tr>";
                    }
                }
            }
        }
        
        $result .= "     </tr>"
                . "</tbody>"
                . " </table>"
                . "</form>";
    }
    
    $result .= "</table>"
            . "</div>";
    

    
    print($result);
}

function render_entry_top($module, $entryid, $edit = 0){
    if ($edit == 0){
    $result = "<table class='bordered' width=50%>"
            . "<th colspan='4'>"
            .   get_entry_name($module,$entryid)
            . "</th>"
            . "<th>";
            if (get_right(whoami(), $module, 'write')){
                $result .= '    <a class="light-blue waves-effect btn" href="/index.php?module='.htmlspecialchars($module).'&nameedit=1&entry='.htmlspecialchars($entryid).'">'
                        . '         <i class="small material-icons">edit</i>'
                        . '     </a>';
            }
            $result .= '</th>';
    }
    else{
        $result = "<table class='bordered' width=50%>"
                . "<form action='index.php?module=".htmlspecialchars($module)."&entry=".htmlspecialchars($entryid)."&fieldid=".$fieldid."' method='post'>"
                . "<th colspan='4'>"
                . "     <div class='input-field'>"
                . "         <input id='newentryname' name='newentryname' type='text' class='validate' value='". htmlspecialchars(get_entry_name($module,$entryid))."'>"
                . "         <label for='newentryname' class='active'>"
                . "             Имя"
                . "         </label>"
                . "     </div>" 
                . "</th>"
                . "<th>"
                . "   <button class='btn waves-effect light-blue' type='submit' name='editentryname'>"
                . "      Сохранить"
                . "   </button>"
                . '</th>'
                . '</form>';
    }
    print($result);
}

function render_entry($module, $entryid){
    $fields = get_fields($module);
    $result = "";
    if ($fields != false){
            foreach ($fields as $field){
                if (get_field_right(whoami(), $module, $field['id'])){
                    $fieldcontentarr = get_field_content($module, $field['id'], $entryid);
                    $result .= '<tr>'
                            . '     <td>'
                            . $field['id']
                            . '     </td>'
                            . '     <td>'
                            . htmlspecialchars($field['name'])
                            . '     </td>'
                            . '     <td>'
                            . $fieldcontentarr
                            . '     </td>'
                            . '     <td width=10%>';
                    if ($field['updatable'] == 1 && get_field_right(whoami(), $module, $field['id'], 'write')){
                        $result .= '    <a class="light-blue waves-effect btn" href="/index.php?module='.htmlspecialchars($module).'&update=1&entry='.htmlspecialchars($entryid).'&fieldid='.htmlspecialchars($field['id']).'">'
                                . '         <i class="small material-icons">autorenew</i>'
                                . '     </a>';
                    }
                    else{
                        $result .= "";
                    }
                        $result .= '</td>'
                                . ' <td width=10%>';
                        if (get_field_right(whoami(), $module, $field['id'], 'write')){
                            $result .= '    <a class="light-blue waves-effect btn" href="/index.php?module='.htmlspecialchars($module).'&edit=1&entry='.htmlspecialchars($entryid).'&fieldid='.htmlspecialchars($field['id']).'">'
                                    . '         <i class="small material-icons">edit</i>'
                                    . '     </a>';
                        }
                    $result .= "</td>";
                    
                    $result .= "</tr>";
                }
            }
        }
    $result .= "</table>";
    print($result);
}



function render_entry_edit($module,$entryid,$fieldid){
    $fields = get_fields($module);
    $result = "";
    if ($fields != false){
            foreach ($fields as $field){
                if (get_field_right(whoami(), $module, $field['id'])){
                    $fieldcontentarr = get_field_content($module, $field['id'], $entryid);
                    if ($fieldid != $field['id']){
                        $result .= '<tr>'
                                . '     <td colspan="2">'
                                . '         '.htmlspecialchars($field['name'])
                                . '     </td>'
                                . '     <td>'
                                . '         '.htmlspecialchars($fieldcontentarr)
                                . '     </td>'
                                . '     <td width=10%>';
                        if ($field['updatable'] == 1 && get_field_right(whoami(), $module, $field['id'], 'write')){
                            $result .= '    <a class="light-blue waves-effect btn" href="/index.php?module='.htmlspecialchars($module).'&update=1&entry='.htmlspecialchars($entryid).'&fieldid='.htmlspecialchars($field['id']).'">'
                                    . '         <i class="small material-icons">autorenew</i>'
                                    . '     </a>';
                        }
                        else{
                            $result .= '';
                        }
                        $result .= '</td>'
                                . ' <td width=10%>';
                        if (get_field_right(whoami(), $module, $field['id'], 'write')){
                            $result .='    <a class="light-blue waves-effect btn" href="/index.php?module='.htmlspecialchars($module).'&edit=1&entry='.htmlspecialchars($entryid).'&fieldid='.htmlspecialchars($field['id']).'">'
                                    . '         <i class="small material-icons">edit</i>'
                                    . '     </a>';
                        }
                        $result .= " </td>"
                                . "</tr>";
                    }
                    elseif (get_field_right(whoami(), $module, $field['id'], 'write')){
                        $result .= "<form action='index.php?module=".htmlspecialchars($module)."&entry=".htmlspecialchars($entryid)."&fieldid=".htmlspecialchars($fieldid)."' method='post'>"
                                . "     <tr>"
                                . "         <td colspan='2'>"
                                . htmlspecialchars($field['name'])
                                . "         </td>"
                                . "         <td colspan='2'>"
                                . "             <div class='input-field'>"
                                . "                 <input id='newcontent' name='newcontent' type='text' class='validate' value='". htmlspecialchars(get_field_content($module, $fieldid, $entryid))."'>"
                                . "                 <label for='newcontent' class='active'>"
                                . "                     Текст"
                                . "                 </label>"
                                . "             </div>"
                                . "         </td>"
                                . "         <td width=10%>"
                                . "             <button class='btn waves-effect light-blue' type='submit' name='editentryfield'>"
                                . "                 Сохранить"
                                . "             </button>"
                                . "         </td>"
                                . "     </tr>"
                                . "     ";
                    }
                }
            }
        }
    $result .= "</table>";
    print($result);
}

function render_material_window($title,$content){
    $result = '<div class="row z-depth-3">'
            . '     <div class="row light-blue darken-1">'
            . '          <div class="col s12">'
            . '             <h5>'.$title.'</h5>'
            . '          </div>'
            . '     </div>'
            . '     <div class="row">'
            . '         <div class="col s12">'
            . '              <p>'.$content.'</p>'
            . '         </div>'
            . '     </div>'
            . '</div>';
    print($result);
}

function render_fields_edit($module){
    $module = vf($module, 4);
    $fields = get_fields($module);
    
    $result = '<form action="/index.php?module='. htmlspecialchars(current_module()).'&moduleedit='.htmlspecialchars($module).'&action=editfields" method="post">'
            . '<table>';
    
        if (!empty($fields)){
        foreach($fields as $field){
        $result .= ''
            . '     <tr>'
            . '         <input type="hidden" name="fields['.htmlspecialchars($field['id']).']">'
            . '         <td>'
            . '             <div class="input-field">'
            . '                 <input id="fields['.htmlspecialchars($field['id']).'][newfieldname]" name="fields['.htmlspecialchars($field['id']).'][newfieldname]" type="text" class="validate" value="'.htmlspecialchars($field['name']).'">'
            . '                     <label for="fields['.htmlspecialchars($field['id']).'][newfieldname]" class="active">'
            . '                         Название'
            . '                     </label>'
            . '             </div>'
            . '         </td>'
            . '         <td>';
        if ($field['updatable'] == 1){
            $result .= '             <input id="fields['.htmlspecialchars($field['id']).'][updatable]" name="fields['.htmlspecialchars($field['id']).'][updatable]" type="checkbox" checked="checked" class="filled-in">';
        }
        else{
            $result .= '             <input id="fields['.htmlspecialchars($field['id']).'][updatable]" name="fields['.htmlspecialchars($field['id']).'][updatable]" type="checkbox" class="filled-in">';
        }
            $result .= '                 <label for="fields['.htmlspecialchars($field['id']).'][updatable]">'
                    . '                     Обновляемое'
                    . '                 </label>'
                    . '                 <br>';
        if ($field['displayable'] == 1){        
            $result .= '             <input id="fields['.htmlspecialchars($field['id']).'][displayable]" name="fields['.htmlspecialchars($field['id']).'][displayable]" type="checkbox" class="filled-in" checked="checked">';
        }
        else{
            $result .= '             <input id="fields['.htmlspecialchars($field['id']).'][displayable]" name="fields['.htmlspecialchars($field['id']).'][displayable]" type="checkbox" class="filled-in">';
        }
        $result .= '                 <label for="fields['.htmlspecialchars($field['id']).'][displayable]">'
                . '                     Отображаемое в списке'
                . '                 </label>'
                . '         </td>'
                . '         <td>'
                . '             <div class="input-field">'
                . '                 <select name="fields['.htmlspecialchars($field['id']).'][script]">';
    $scripts = get_script_list();
    foreach ($scripts as $script){
                if ($script == $field['script']){
                    $result .= '<option value='.htmlspecialchars($script).' selected>'.htmlspecialchars($script).'</option>';
                }
                else{
                    $result .= '<option value='.htmlspecialchars($script).'>'.htmlspecialchars($script).'</option>';
                }
                
            }
    $result .= '                </select>'
            . '                 <label>Скрипт</label>'
            . '             </div>'
            . '         <td>'
            . '             <div class="input-field">'
            . '                 <input id="fields['.htmlspecialchars($field['id']).'][args]" name="fields['.htmlspecialchars($field['id']).'][args]" type="text" class="validate" value="'.$field['args'].'">'
            . '                     <label for="fields['.htmlspecialchars($field['id']).'][args]" id="fields['.htmlspecialchars($field['id']).'][args][label]">'
            . '                         Аргументы'
            . '                     </label>'
            . '             </div>'
            . '         </td>'
            . "         <td>";
        $fields = get_fields($module);
        if (!empty($fields)){
            $result .= "<a class='dropdown-button btn light-blue' href='#' data-activates='dropdown-args-".htmlspecialchars($field['id'])."'>Подставить аргумент</a>"
                    . "<ul id='dropdown-args-".htmlspecialchars($field['id'])."' class='dropdown-content'>";
            foreach ($fields as $fieldtopaste){
                $result .= '            <li><a href="#" onClick=insertArgsFields('.htmlspecialchars($fieldtopaste["id"]).',"'.$module.'",'.$field['id'].');>'.htmlspecialchars($fieldtopaste['name']).'</a></li>';
            }
        }
        $result .= '                </ul>'
                . '     </td>'
                . '     <td>'
                . '         <a class="light-blue waves-effect btn" href="/index.php?module='. htmlspecialchars(current_module()).'&moduleedit='.htmlspecialchars($module).'&deletefield=1&fieldid='.htmlspecialchars($field['id']).'&action=editfields">'
                . '             Удалить'
                . '         </a>'
                . '     </td>'
                . '      </tr>';
    }
    }
    
    
    
    $result .= ''
            . '     <tr>'
            . '         <input type="hidden" name="fields[0]">'
            . '         <td>'
            . '             <div class="input-field">'
            . '                 <input id="fields[0][newfieldname]" name="fields[0][newfieldname]" type="text" class="validate">'
            . '                     <label for="newfieldname">'
            . '                         Название'
            . '                     </label>'
            . '             </div>'
            . '         </td>'
            . '         <td>'
            . '             <input id="fields[0][updatable]" name="fields[0][updatable]" type="checkbox" class="filled-in">'
            . '                 <label for="fields[0][updatable]">'
            . '                     Обновляемое'
            . '                 </label>'
            . '                 <br>'
            . '             <input id="fields[0][displayable]" name="fields[0][displayable]" type="checkbox" class="filled-in">'
            . '                 <label for="fields[0][displayable]">'
            . '                     Отображаемое в списке'
            . '                 </label>'
            . '         </td>'
            . '         <td>'
            . '             <div class="input-field">'
            . '                 <select name="fields[0][script]">';
    $scripts = get_script_list();
    foreach ($scripts as $script){
                $result .= '        <option value='.htmlspecialchars($script).'>'.htmlspecialchars($script).'</option>';
            }
    $result .= '                </select>'
            . '                 <label>Скрипт</label>'
            . '              </div>'
            . '         <td>'
            . '             <div class="input-field">'
            . '                 <input id="fields[0][args]" name="fields[0][args]" type="text" class="validate">'
            . '                     <label for="fields[0][args]" id="fields[0][args][label]">'
            . '                         Аргументы'
            . '                     </label>'
            . '             </div>'
            . '         </td>'
            . "     <td>";
    $fields = get_fields($module);
    if (!empty($fields)){
        $result .= "<a class='dropdown-button btn light-blue' href='#' data-activates='dropdown-args-0'>Подставить аргумент</a>"
                . "<ul id='dropdown-args-0' class='dropdown-content'>";
        foreach ($fields as $fieldtopaste){
            $result .= '            <li><a href="#" onClick=insertArgsFields('.htmlspecialchars($fieldtopaste["id"]).',"'.$module.'",0);>'.htmlspecialchars($fieldtopaste['name']).'</a></li>';
        }
    }
    $result .= '                </ul>'
            . '     </td>'
            . '         <td>'
            . '         </td>'
            . '      </tr>';
    
    
    
    
    
    

    
    
    $result .= ''
            . '<tr>'
            . '     <td colspan=6 class=center-align>'
            . '        <button class="btn waves-effect light-blue" type="submit" name="editfields">'
            . '             Сохранить'
            . '        </button>'
            . '     </td>'
            . '</tr>';
            
    $result .= '</table>'
            . '</form>';
    print $result;
}


function render_action_edit($module){
    $result = "";
    $module = vf($module,4);
    if (get_right(whoami(), $module, 'write')){
        $actions = get_all_actions($module);
        $result .= '<form action="/index.php?module='. htmlspecialchars(current_module()).'&moduleedit='.htmlspecialchars($module).'&action=editactions" method="post">'
                . "<table>";
        if (!empty($actions)){
            foreach ($actions as $action){
                $result .=" <tr>"
                        . '     <td>'
                        . '         <div class="input-field">'
                        . '             <input id="actions['.htmlspecialchars($action['id']).'][newactionname]" name="actions['.htmlspecialchars($action['id']).'][newactionname]" type="text" class="validate" value="'.htmlspecialchars($action['name']).'">'
                        . '                 <label for="actions['.htmlspecialchars($action['id']).'][newactionname]" class="active">'
                        . '                     Название'
                        . '                 </label>'
                        . '         </div>'
                        . '     </td>'
                        . "     <td>"
                        . '             <div class="input-field">'
                        . '                 <select name="actions['.htmlspecialchars($action['id']).'][script]">';
                $scripts = get_script_list();
                foreach ($scripts as $script){
                    if ($action['script'] == $script){
                        $result .= '        <option value='.htmlspecialchars($script).' selected>'.htmlspecialchars($script).'</option>';
                    }
                    else{
                        $result .= '        <option value='.htmlspecialchars($script).'>'.htmlspecialchars($script).'</option>';
                    }
                }
                $result .= '                </select>'
                        . '                 <label>Скрипт</label>'
                        . '              </div>'
                        . '     </td>'
                        . '     <td>'
                        . '         <div class="input-field">'
                        . '             <input id="actions['.htmlspecialchars($action['id']).'][args]" name="actions['.htmlspecialchars($action['id']).'][args]" type="text" class="validate" value="'.htmlspecialchars($action['args']).'">'
                        . '                 <label for="actions['.htmlspecialchars($action['id']).'][args]" id="actions['.htmlspecialchars($action['id']).'][args][label]">'
                        . '                     Аргументы'
                        . '                 </label>'
                        . '         </div>'
                        . '     </td>'
                        . "     <td>";
                $fields = get_fields($module);
                if (!empty($fields)){
                    $result .= "<a class='dropdown-button btn light-blue' href='#' data-activates='dropdown-args-".htmlspecialchars($action['id'])."'>Подставить аргумент</a>"
                            . "<ul id='dropdown-args-".htmlspecialchars($action['id'])."' class='dropdown-content'>";
                    foreach ($fields as $field){
                        $result .= '            <li><a href="#" onClick=insertArgs('.htmlspecialchars($field["id"]).',"'.$module.'",'.htmlspecialchars($action['id']).');>'.htmlspecialchars($field['name']).'</a></li>';
                    }
                }
                $result .= '                </ul>'
                        . '     </td>'
                        . '     <td>'
                        . '         <a class="light-blue waves-effect btn" href="/index.php?module='. htmlspecialchars(current_module()).'&moduleedit='.htmlspecialchars($module).'&deleteaction=1&actionid='.htmlspecialchars($action['id']).'&action=editactions">'
                        . '             Удалить'
                        . '         </a>'
                        . '     </td>'
                        . ' </tr>';
            }
        }
        
        
        $result .=" <tr>"
                . '     <td>'
                . '         <div class="input-field">'
                . '             <input id="actions[0][newactionname]" name="actions[0][newactionname]" type="text" class="validate">'
                . '                 <label for="actions[0][newactionname]" class="active">'
                . '                     Название'
                . '                 </label>'
                . '         </div>'
                . '     </td>'
                . "     <td>"
                . '             <div class="input-field">'
                . '                 <select name="actions[0][script]">';
        $scripts = get_script_list();
        $i = 0;
        foreach ($scripts as $script){
            $i++;
            if ($i == 1){
                $result .= '        <option value='.htmlspecialchars($script).' selected>'.htmlspecialchars($script).'</option>';
            }
            else{
                $result .= '        <option value='.htmlspecialchars($script).'>'.htmlspecialchars($script).'</option>';
            }
        }
        $result .= '                </select>'
                . '                 <label>Скрипт</label>'
                . '              </div>'
                . '     </td>'
                . '     <td>'
                . '         <div class="input-field">'
                . '             <input id="actions[0][args]" name="actions[0][args]" type="text" class="validate">'
                . '                 <label for="actions[0][args]" id="actions[0][args][label]">'
                . '                     Аргументы'
                . '                 </label>'
                . '         </div>'
                . '     </td>'
                . "     <td>";
        $fields = get_fields($module);
        if (!empty($fields)){
            $result .= "<a class='dropdown-button btn light-blue' href='#' data-activates='dropdown-args-0'>Подставить аргумент</a>"
                    . "<ul id='dropdown-args-0' class='dropdown-content'>";
            foreach ($fields as $field){
                $result .= '            <li><a href="#" onClick=insertArgs('.htmlspecialchars($field["id"]).',"'.$module.'",0);>'.htmlspecialchars($field['name']).'</a></li>';
                //$result .= '            <li class=light-blue-text><a href="#" onClick=insertArgs('.htmlspecialchars($field["id"]).',"'.$module.'");>'.htmlspecialchars($field['name']).'</a></li>';
            }
        }
        $result .= '                </ul>'
                . '     </td>'
                . '     <td>'
                . '     </td>'
                . ' </tr>';
        $result .= ''
                . '<tr>'
                . '     <td colspan=5 class=center-align>'
                . '        <button class="btn waves-effect light-blue" type="submit" name="editactions">'
                . '             Сохранить'
                . '        </button>'
                . '     </td>'
                . '</tr>';
        
        $result .= "</table>"
                . "</form>";
        print($result);
    }
}


function render_module_actions($module,$entryid){
    $module = vf($module,4);
    $entryid = vf($entryid, 3);
    $result = "";
    
    if (!empty($module)){
        $actions = get_all_actions($module);
        $result .= "<form action='index.php?module=".$module."&entry=".$entryid."' method=post>"
                . "<table>"
                . "     <tr>";
        $i = 0;
        if (!empty($actions)){
            foreach ($actions as $action){
                if(get_action_right(whoami(), $module, $action['id'])){
                    $i++;
                    $result.="<td>"
                            . '       <button class="btn waves-effect light-blue" type="submit" name="action['.$action['id'].']" style="width:100%">'
                            .               htmlspecialchars($action['name'])
                            . '        </button>'
                            . '</td>';
                    if($i%4 == 0){
                        $result.= "<tr></tr>";
                    }
                }
            }
        }
        $result .= "</tr>"
                . "</table>"
                . "</form>";
    }
    print($result);
}