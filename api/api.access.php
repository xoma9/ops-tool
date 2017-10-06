<?php

function is_superadmin($admin){
    $admin = vf($admin);
    $query = "SELECT superadmin FROM users WHERE login='".$admin."'";
    $issuperadmin = simple_query($query);
    $issuperadmin = $issuperadmin['superadmin'];
    if ($issuperadmin){
        $result = true;
    }
    else{
        $result = false;
    }
    return($result);
}


function get_user_id($admin){
    $admin = vf($admin);
    $query = "SELECT id FROM users WHERE login='".$admin."'";
    $userid = simple_query($query);
    $result = $userid['id'];
    return($result);
}

function get_user_name($admin){
    $admin = vf($admin);
    $query = "SELECT login FROM users WHERE id='".$admin."'";
    $username = simple_query($query);
    $result = $username['login'];
    return($result);
}

function get_field_right($admin,$module,$fieldid,$right = 'read',$ignoresuperadmin = false){
    $admin = vf($admin);
    $module = vf($module, 4);
    $fieldid = vf($fieldid, 3);
    $issuperadmin = is_superadmin($admin);
    if ($right != 'read' && $right != 'write'){
        $right = 'read';
    }
    $table = table_exists('modules_'.$module.'_field_rights');
    if ($table){
        $userid = get_user_id($admin);
        $query = "SELECT user_".$right."_right FROM modules_".$module."_field_rights WHERE userid='".$userid."' AND field_id='".$fieldid."'";
        $result = simple_query($query);
        $result = $result['user_'.$right.'_right'];
        if ($result == 1){
            $result = true;
        }
        else{
            if (!$ignoresuperadmin){
                if ($issuperadmin){
                    $result = true;
                }
                else{
                    $result = false;
                }
            }
            else{
                $result = false;
            }
        }
        
    }
    else{
        if (!$ignoresuperadmin){
            if ($issuperadmin){
                $result = true;
            }
            else{
                $result = false;
            }
        }
        else{
            $result = false;
        }
    }
    return($result);
}


function get_action_right($admin,$module,$actionid,$right = 'execute',$ignoresuperadmin = false){
    $admin = vf($admin);
    $module = vf($module, 4);
    $actionid = vf($actionid, 3);
    $issuperadmin = is_superadmin($admin);
    if ($right != 'execute'){
        $right = 'execute';
    }
    $table = table_exists('modules_'.$module.'_field_rights');
    if ($table){
        $userid = get_user_id($admin);
        $query = "SELECT user_".$right."_right FROM modules_".$module."_action_rights WHERE userid='".$userid."' AND action_id='".$actionid."'";
        $result = simple_query($query);
        $result = $result['user_'.$right.'_right'];
        if ($result == 1){
            $result = true;
        }
        else{
            if (!$ignoresuperadmin){
                if ($issuperadmin){
                    $result = true;
                }
                else{
                    $result = false;
                }
            }
            else{
                $result = false;
            }
        }
        
    }
    else{
        if (!$ignoresuperadmin){
            if ($issuperadmin){
                $result = true;
            }
            else{
                $result = false;
            }
        }
        else{
            $result = false;
        }
    }
    return($result);
}

function get_right($admin,$module,$right = 'read',$ignoresuperadmin = false){
    $admin = vf($admin);
    $module = vf($module, 4);
    $issuperadmin = is_superadmin($admin);
    if ($right != 'read' && $right != 'write'){
        $right = 'read';
    }
    $table = table_exists('modules_'.$module.'_rights');
    if ($table){
        $userid = get_user_id($admin);
        $query = "SELECT user_".$right."_right FROM modules_".$module."_rights WHERE userid='".$userid."'";
        $result = simple_query($query);
        $result = $result['user_'.$right.'_right'];
        if ($result == 1){
            $result = true;
        }
        else{
            if (!$ignoresuperadmin){
                if ($issuperadmin){
                    $result = true;
                }
                else{
                    $result = false;
                }
            }
            else{
                $result = false;
            }
        }
        
    }
    else{
        if (!$ignoresuperadmin){
            if ($issuperadmin){
                $result = true;
            }
            else{
                $result = false;
            }
        }
        else{
            $result = false;
        }
    }
    return($result);
}


function set_right($module,$admin,$value = 0,$right = 'read'){
    $admin = vf($admin);
    $module = vf($module, 4);
    
    if ($value != 0 && $value != 1){
        $value = 0;
    }
    if ($right != 'read' && $right != 'write'){
        $right = 'read';
    }
    //print_r($module.' '.$admin.' '.$value.' '.$right.'</br>');
    $table = table_exists('modules_'.$module.'_rights');
    $userid = get_user_id($admin);
    if ($table){
        $query = "SELECT userid FROM modules_".$module."_rights WHERE userid='".$userid."'";
        $entry = simple_query($query);
        if (!empty($entry)){
            $query = "UPDATE modules_".$module."_rights SET user_".$right."_right='".$value."' WHERE userid='".$userid."'";
        }
        else{
            $query = "INSERT INTO modules_".$module."_rights (userid, user_".$right."_right) VALUES ('".$userid."','".$value."')";
        }
    }
    else{
        create_module_right_table($module);
        $query = "INSERT INTO modules_".$module."_rights (userid, user_".$right."_right) VALUES ('".$userid."','".$value."')";
    }
    nr_query($query);
}


function set_field_right($module,$admin,$fieldid,$value = 0,$right = 'read'){
    $admin = vf($admin);
    $module = vf($module, 4);
    $fieldid = vf($fieldid,3);
    
    if ($value != 0 && $value != 1){
        $value = 0;
    }
    if ($right != 'read' && $right != 'write'){
        $right = 'read';
    }
    $table = table_exists('modules_'.$module.'_field_rights');
    $userid = get_user_id($admin);
    if ($table){
        $query = "SELECT userid FROM modules_".$module."_field_rights WHERE userid='".$userid."' AND field_id='".$fieldid."'";
        $entry = simple_query($query);
        if (!empty($entry)){
            $query = "UPDATE modules_".$module."_field_rights SET user_".$right."_right='".$value."' WHERE userid='".$userid."' AND field_id='".$fieldid."'";
        }
        else{
            $query = "INSERT INTO modules_".$module."_field_rights (userid, field_id, user_".$right."_right) VALUES ('".$userid."','".$fieldid."','".$value."')";
        }
    }
    nr_query($query);
}


function set_action_right($module,$admin,$actionid,$value = 0,$right = 'execute'){
    $admin = vf($admin);
    $module = vf($module, 4);
    $actionid = vf($actionid,3);
    
    if ($value != 0 && $value != 1){
        $value = 0;
    }
    if ($right != 'execute'){
        $right = 'execute';
    }
    $table = table_exists('modules_'.$module.'_action_rights');
    $userid = get_user_id($admin);
    if ($table){
        $query = "SELECT userid FROM modules_".$module."_action_rights WHERE userid='".$userid."' AND action_id='".$actionid."'";
        $entry = simple_query($query);
        if (!empty($entry)){
            $query = "UPDATE modules_".$module."_action_rights SET user_".$right."_right='".$value."' WHERE userid='".$userid."' AND action_id='".$actionid."'";
        }
        else{
            $query = "INSERT INTO modules_".$module."_action_rights (userid, action_id, user_".$right."_right) VALUES ('".$userid."','".$actionid."','".$value."')";
        }
    }
    nr_query($query);
}



function set_superadmin($admin,$value){
    $admin = vf($admin);
    $userid = get_user_id($admin);
    if ($value != '0' && $value != '1'){
        $value = '0';
    }
    $query = "UPDATE users SET superadmin='".$value."' WHERE id='".$userid."'";
    nr_query($query);
}

function create_admin($admin){
    $name = vf($name, 4);
    $query = "SELECT id FROM users WHERE login='".$admin."'";
    $entry = simple_query($query);
    if (!empty($entry)){
        print('Администратор с таким именем уже существует');
    }
    else{
        $query = "INSERT INTO users (login,password,cookie,superadmin) VALUES ('".$admin."','','','0')";
        nr_query($query);
    }
}

function delete_admin($admin){
    $admin = vf($admin, 4);
    $userid = get_user_id($admin);
    $query = "SELECT id FROM users WHERE id='".$userid."'";
    $entry = simple_query($query);
    if (empty($entry)){
        print('Администратора с таким именем не существует');
    }
    else{
        $query = "DELETE FROM users WHERE id='".$userid."'";
        nr_query($query);
        $modules = get_module_list();
        foreach ($modules as $module){
            if (table_exists('modules_'.$module.'_rights')){
                $query = "DELETE FROM modules_".$module."_rights WHERE userid='".$userid."'";
            }
            
        }
    }
    
}