<?php
if (get_right(whoami(), current_module())){
    if (!isset($_GET['action'])){
        render_user_module_list();
        if (isset($_POST['createnewmodule'])){
            if (isset($_POST['newmodulename'])){
                $newmodulename = vf($_POST['newmodulename'],4);
                if (empty($newmodulename)){
                    message_box('Некорректное служебное имя модуля. Оно должно содержать латинские буквы, цифры или знаки подчеркивания и дефис. Имя модуля не должно быть пустым');
                }
                else{
                    if (isset($_POST['newmodulevisiblename'])){
                        $newmodulevisiblename = vf($_POST['newmodulevisiblename']);
                        if (!empty($newmodulevisiblename)){
                            if (isset($_POST['categoryid'])){
                                $categoryid = vf($_POST['categoryid']);
                                if (category_exist($categoryid)){
                                    create_module($newmodulename, $newmodulevisiblename, $categoryid);
                                    update_page();
                                }
                                else{
                                    message_box('Нет такой категории');
                                }
                            }
                        }
                    }
                    else{
                        message_box("Заполните видимое имя!");
                    }
                }
            }
            else{
                message_box("Заполните служебное имя!");
            }
        }
    }
    else{
        if (isset($_GET['moduleedit'])){
            $module = vf($_GET['moduleedit'], 4);
            if (!empty($module)){
                $action = $_GET['action'];
                
                if($action == 'deletemodule'){
                    delete_module($module);
                    redirect('/index.php?module='.current_module());
                }
                elseif ($action == 'editfields'){
                    
                    if (isset($_GET['deletefield'])){
                        if ($_GET['deletefield'] == 1 && isset($_GET['fieldid'])){
                            $fieldid = vf($_GET['fieldid']);
                            if (!empty($fieldid)){
                                delete_field($module,$fieldid);
                            }
                            redirect('/index.php?module='.current_module().'&moduleedit='.$module.'&action=editfields');
                        }
                    }
                    
                    
                    render_fields_edit($module);
                    if (isset($_POST['editfields']) && isset($_POST['fields'])){
                        
                        $fields = $_POST['fields'];
                        foreach ($fields as $id => $field){
                            $fieldname = vf(mysql_real_escape_string($field['newfieldname']));
                            if (isset($field['updatable'])){
                                $updatable = 1;
                            }
                            else{
                                $updatable = 0;
                            }
                            if (isset($field['script'])){
                                if (script_exists($field['script'])){
                                    $script = $field['script'];
                                }
                                else{
                                    $script = "";
                                }
                            }
                            $args = mysql_real_escape_string($field['args']);
                            if (isset($field['displayable'])){
                                $displayable = 1;
                            }
                            else{
                                $displayable = 0;
                            }
                            if (!empty($fieldname)){
                            set_module_field($module, $id, $fieldname, $updatable, $script, $args, $displayable);
                            }
                            update_page();
                        }
                    
                    }
                }
                elseif ($action == 'fieldrights'){
                    render_field_rights($module);
                    if (isset($_POST['setfieldrights'])){
                        $admins = $_POST['newrights'];
                        foreach ($admins as $admin => $fieldrights){
                            $admin = get_user_name($admin);
                            foreach ($fieldrights as $field => $rights){
                                print_r($rights['read']);
                                if (empty($rights)){
                                  $value = 0;
                                  $right = 'read';
                                  set_field_right($module, $admin, $field, $value, $right);
                                  $right = 'write';
                                  set_field_right($module, $admin, $field, $value, $right);
                                }
                                else{
                                    if(!empty($rights['read'])){
                                        $value = 1;
                                        $right = 'read';
                                        set_field_right($module, $admin, $field, $value, $right);
                                    }
                                    if(!empty($rights['write'])){
                                        $value = 1;
                                        $right = 'write';
                                        set_field_right($module, $admin, $field, $value, $right);
                                    }
                                }
                            }
                        }
                        update_page();
                    }
                }
                elseif($action == 'import'){
                    if (is_uploaded_file($_FILES['uploadedscript']['tmp_name']) && isset($_FILES)){
                        move_uploaded_file($_FILES["uploadedscript"]["tmp_name"], TMP_PATH.'/'. 'import.csv');
                        if (isset($_POST['delimiter'])){
                            $delimeter = $_POST['delimiter'];
                        }
                        else{
                            $delimiter = ';';
                        }
                        $csvarr = (csv_to_array(TMP_PATH.'/'. 'import.csv', $delimeter));
                        if (count($csvarr) < 5){
                            $displayablearr = array_slice($csvarr,0,count($csvarr));
                        }
                        else{
                            $displayablearr = array_slice($csvarr,0,5);
                        }
                        render_import($module, $displayablearr, $delimiter);
                    }
                    else{
                        if (isset($_POST['importdata']) && isset($_POST['columns'])){
                            if (isset($_POST['delimiter'])){
                                $delimiter = $_POST['delimiter'];
                            }
                            else{
                                $delimiter = ';';
                            }
                            $counter = 0;
                            $headers = $_POST['columns'];
                            
                            foreach ($headers as $header){
                                if ($header == '0'){
                                    $counter++;
                                }
                                $newheaders[] = $header;
                            }
                            if ($counter == '0'){
                                message_box('Вы должны назначить хотя-бы одно имя записи для импорта!');
                            }
                            else{
                            csv_to_mysql(TMP_PATH.'/'. 'import.csv', $delimiter, $newheaders,$module);
                            }
                        }
                        render_import($module);
                    }
                }
                elseif($action == 'massactions'){
                    render_module($module,1);
                    if (isset($_POST['deleteentries']) && !empty($_POST['checked'])){
                        $massaction = $_POST['checked'];
                        foreach ($massaction as $entryid => $on){
                            delete_entry($module, $entryid);
                            update_page();
                        }
                    }
                        
                    if (isset($_POST['massaction']) && !empty($_POST['checked']) && !empty($_POST['massactionid'])){
                        $massaction = $_POST['checked'];
                        $actionid = vf($_POST['massactionid'], 3);
                        foreach ($massaction as $entryid => $on){
                            perform_action($module,$actionid, $entryid);
                            //update_page();
                        }
                        
                    }
                    
                    if (isset($_POST['massupdatefield']) && !empty($_POST['checked']) && !empty($_POST['massupdatefieldid'])){
                        $massaction = $_POST['checked'];
                        $fieldid = vf($_POST['massupdatefieldid'], 3);
                        foreach ($massaction as $entryid => $on){
                            update_field_entry($module, $entryid, $fieldid);
                            update_page();
                        }
                        
                    }
                    
                    
                    
                }
                elseif ($action == 'editactions'){
                    render_action_edit($module);
                    
                    if ($_GET['deleteaction'] == 1 && isset($_GET['actionid'])){
                        $actionid = vf($_GET['actionid']);
                        if (!empty($actionid)){
                            delete_action($module,$actionid);
                        }
                        redirect('/index.php?module='.current_module().'&moduleedit='.$module.'&action=editactions');
                    }
                    
                    if (isset($_POST['editactions']) && isset($_POST['actions'])){
                    
                    $actions = $_POST['actions'];
                    foreach ($actions as $actionid => $action){
                        if (script_exists($action['script'])){
                            $script = $action['script'];
                        }
                        else{
                            $script = "";
                        }
                        $actionname = mysql_real_escape_string($action['newactionname']);
                        $args = mysql_real_escape_string($action['args']);
                        if (!empty($actionname)){
                            set_action($module, $actionid, $script, $args, $actionname);
                        }
                        update_page();
                        }
                    }
                }
            }
        }
    }
}