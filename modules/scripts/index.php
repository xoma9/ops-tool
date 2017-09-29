<?php
if (get_right(whoami(), current_module())){
    if (isset($_GET['script'])){
        $script = vf($_GET['script'], 5);
    }
    
    if (isset($_GET['action'])){
        $action = vf($_GET['action']);
    }
    
    if (!isset($action)){
        
    }
    elseif(isset($script) && isset($action)){

        if ($action == 'delete'){
            unlink(SCRIPT_PATH.'/'.$script);
            redirect('index.php?module='. current_module());
        }
    }
    elseif(isset($action)){
        if ($action == 'upload'){        
            $uploaddir = SCRIPT_PATH;
            if (is_uploaded_file($_FILES['uploadedscript']['tmp_name']) && isset($_FILES)){
                move_uploaded_file($_FILES["uploadedscript"]["tmp_name"], $uploaddir.'/'.  vf($_FILES["uploadedscript"]["name"],4));
                redirect('index.php?module='. current_module());
            }
            else{
                redirect('index.php?error=1&module='. current_module());
            }
        }
    }
    
    if (isset($_GET['error'])){
            print('Не удалось загрузить файл. Возможно, он слишком толст для параметра max_upload_size в php.ini');
        }
    render_script_list();
}