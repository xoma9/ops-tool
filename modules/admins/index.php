<?php
require_once API_PATH.'/api.general.php';

if(!isset($_GET['action']) && get_right(whoami(), $_GET['module'])){
    render_adminlist();
}
elseif(isset($_GET['action']) && isset($_GET['admin']) && get_right(whoami(), $_GET['module'])){
    $action = vf($_GET['action']);
    $admin = vf($_GET['admin']);
    
    
    if ($action == 'changepass'){
        if (!isset($_POST['newpass']) && !isset($_POST['repeatnewpass'])){
            
            print('<div class="row"><form class="col s12" method="post" action="index.php?module=admins&action=changepass&admin='.$admin.'"><div class="row">');
            
            print('<div class="input-field col s6"><input id="newpass" type="password" name="newpass" class="validate"><label for="newpass" class="">Новый пароль</label></div>');
            print('<div class="input-field col s6"><input id="repeatnewpass" type="password" name="repeatnewpass" class="validate"><label for="repeatnewpass">Повторите новый пароль</label></div>');
            print('<div class="col s2 align-center"><button class="btn waves-effect light-blue darken-2" type="submit" name="chpass">
				Сменить пароль
			</button></div>');
            
            print('</div></form></div>');
        }
        else{
            if (isset($_POST['chpass'])){
                if ($_POST['newpass'] != $_POST['repeatnewpass']){
                    message_box('Пароли не совпадают');
                    update_page();
                }
                elseif($_POST['newpass'] == "" || $_POST['repeatnewpass'] == ""){
                    message_box('Вы должны заполнить оба поля!');
                    update_page();
                }
                else{
                $newpass = sha1($_POST['newpass']);
                $query = "UPDATE users SET password='".$newpass."' WHERE login='".$admin."'";
                nr_query($query);
                message_box('Пароль был успешно изменен!');
                update_page();
                }
            }
        }
    }
    
    
    if ($action == 'accessrights'){
        $allmodules = get_all_modules();
        //print_r($allmodules);
        print('<div class="row"><form action="index.php?module=admins&action=accessrights&admin='.$admin.'" method="post">');
        print('<div class="row"><div class="col s12"><h4>Суперадминистратор</h4></div></div>');
        if (is_superadmin($admin)){
            print('<div class="row"><div class="col s12"><p><input type="checkbox" class="filled-in" id="superadmin" name="superadmin" checked="checked"><label for="superadmin">Суперадминистратор</label></p></div></div>');
            foreach ($allmodules as $category => $modules){
                foreach ($modules as $module => $modulename){
                    $currentreadright = get_right($admin, $module,'read',true);
                    $currentwriteright = get_right($admin, $module,'write',true);
                    if ($currentreadright){
                        print('<input type="hidden" name="module_'.$module.'_read" value="on"></input>');
                    }
                    if ($currentwriteright){
                        print('<input type="hidden" name="module_'.$module.'_write" value="on"></input>');
                    }            
                }
            }
        }
        else{
            print('<div class="row"><div class="col s12"><p><input type="checkbox" class="filled-in" id="superadmin" name="superadmin"><label for="superadmin">Суперадминистратор</label></p></div></div>');
        
            foreach ($allmodules as $category => $modules){
                print('<div class="row"><div class="col s12"><h4>'.get_category_name($category).'</h4></div><div class="row">');
                $i = 0;
                foreach ($modules as $module => $modulename){
                    if ($i%4 == 0){
                        $i = 0;
                        print('</div><div class="row">');
                    }
                    $i++;
                    $currentreadright = get_right($admin, $module,'read');
                    $currentwriteright = get_right($admin, $module,'write');
                    
                    print('<div class="col s3">');
                    print('<div class="row"><h5>'.$modulename.'</h5></div>');
                    if ($currentreadright){
                        print('<div class="row"><p><input type="checkbox" class="filled-in" id="module_'.$module.'_read" name="module_'.$module.'_read" checked="checked"><label for="module_'.$module.'_read">Чтение</label></p></div>');
                    }
                    else{
                        print('<div class="row"><p><input type="checkbox" class="filled-in" id="module_'.$module.'_read" name="module_'.$module.'_read"><label for="module_'.$module.'_read">Чтение</label></p></div>');
                    }
                    if ($currentwriteright){
                        print('<div class="row"><p><input type="checkbox" class="filled-in" id="module_'.$module.'_write" name="module_'.$module.'_write" checked="checked"><label for="module_'.$module.'_write">Запись</label></p></div>');
                    }
                    else{
                        print('<div class="row"><p><input type="checkbox" class="filled-in" id="module_'.$module.'_write" name="module_'.$module.'_write"><label for="module_'.$module.'_write">Запись</label></p></div>');
                    }
                    print('</div>');
                    
                }
                print('</div></div>');
            }
        }
        print('<button class="btn waves-effect light-blue darken-2" type="submit" name="setrights">
				Сохранить
			</button>');
        
        print('</form></div>');
        
        if (isset($_POST['setrights'])){
            $modules = get_module_list();
            foreach ($modules as $module){
                if (isset($_POST['module_'.$module.'_read'])){
                    $right = 'read';
                    $value = '1';
                    set_right($module, $admin, $value, $right);
                }
                else{
                    $right = 'read';
                    $value = '0';
                    set_right($module, $admin, $value, $right);
                }
                
                if (isset($_POST['module_'.$module.'_write'])){
                    $right = 'write';
                    $value = '1';
                    set_right($module, $admin, $value, $right);
                }
                else{
                    $right = 'write';
                    $value = '0';
                    set_right($module, $admin, $value, $right);
                }
            }
            if (isset($_POST['superadmin'])){
                $value = '1';
            }
            else{
                $value = '0';
            }
            print $current;
            if (whoami() == $admin && $value == '0'){
                print('<script>alert("Вы действительно собираетесь выстрелить себе в ногу?");</script>');
            }
            else{
                set_superadmin($admin, $value);
            }
            update_page();
        }
    }
    
    
    if ($action == 'addadmin'){
        if (isset($_POST['newadmin']) && !empty($_POST['newadmin'])){
            $newadmin = vf($_POST['newadmin'], 4);
            create_admin($newadmin);
            redirect('index.php?module=admins');
        }
        else{
            //print_r($_POST);
            message_box('У каждого должно быть имя. Пожалуйста, не пренебрегайте поименованием своих сущностей');
            update_page();
        }
    }
    
    if ($action == 'deleteadmin' ){
        if (whoami() == $admin){
            message_box('<script>alert("Вы действительно собираетесь выстрелить себе в ногу?");</script>');
        }
        else{
            delete_admin($admin);
        }
        redirect('index.php?module=admins');
    }
    
}