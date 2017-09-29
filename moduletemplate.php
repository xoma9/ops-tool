<?php
if (get_right(whoami(), current_module())){
     $module = current_module();
     if (!isset($_GET["entry"])){
         render_module($module);
             if (isset($_POST["createentry"]) && !empty($_POST["newentryname"]) && get_right(whoami(), $module,'write')){
                  create_module_entry($module, $_POST["newentryname"]);
             update_page();
        }
     }
     else{
         $entryid = vf($_GET["entry"],3);

         if (!isset($_GET["nameedit"])){
             render_entry_top($module, $entryid);
         }
         else{
             render_entry_top($module, $entryid, 1);
         }
         
         if ($_GET["edit"] == 1 && isset($_GET["fieldid"])){
         $fieldid = vf($_GET["fieldid"], 3);
             if (get_field_right(whoami(), $module, $fieldid, "write")){
                 render_entry_edit($module,$entryid,$fieldid);
             }
         }
         else{
             render_entry($module, $entryid);
             render_module_actions($module, $entryid);
         }
         
         if (!empty($_POST["newcontent"]) && isset($_POST["editentryfield"])){
             $newcontent = mysql_real_escape_string($_POST["newcontent"]);
             $fieldid = vf($_GET['fieldid']);
             if (get_field_right(whoami(), $module, $fieldid, "write")){
                edit_entry_field($module,$entryid,$fieldid,$newcontent);
             }
             redirect("/index.php?module=".$module."&entry=".$entryid);
         }
         
         if ($_GET["update"] == 1 && isset($_GET["fieldid"])){
             $fieldid = vf($_GET["fieldid"]);
             if (get_field_right(whoami(), $module, $fieldid, "write")){
                update_field_entry($module, $entryid, $fieldid);
             }
             redirect("/index.php?module=".$module."&entry=".$entryid);
         }
         
         
         
         if (isset($_POST["action"]) && is_array($_POST["action"])){
             foreach ($_POST["action"] as $actionid => $value){
                 $actionid = vf($actionid, 3);
                 perform_action($module, $actionid, $entryid);
             }
         }
         if (isset($_POST["newentryname"])){
             $newentryname = vf($_POST["newentryname"]);
             if (!empty($newentryname) && get_right(whoami(), $module,'write')){
                  set_entry_name($module, $entryid, $newentryname);
                  redirect("/index.php?module=".$module."&entry=".$entryid);
              }
              else{
                  redirect("/index.php?module=".$module."&entry=".$entryid);
              }
          }
     }
}
