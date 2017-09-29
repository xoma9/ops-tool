<html>
    <head>
        <title>ops-tool</title>
        <meta charset="utf-8" />
        <!--Import Google Icon Font-->
        <link href="template/css/materialicons.css" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="template/css/materialize.css">
        <link type="text/css" rel="stylesheet" href="template/css/materialize.datatables.css">


        <style>
        </style>
    </head>
    <body>
        <script type="text/javascript" src="template/js/jquery-3.2.1.min.js"></script>

        <script type="text/javascript" src="template/js/materialize.js"></script>
        <script type="text/javascript" src="template/js/datatables.js"></script>   
        
        <script type="text/javascript">
            $(document).ready(function() {
                $('#sortable').DataTable({
                    "language": {
      "sStripClasses": "",
      "search": "",
      "searchPlaceholder": "Поиск...",
      "lengthMenu":"Отображать: _MENU_",

      "info": "<p>_START_ - _END_ из _TOTAL_</p>",
      "infoFiltered": "Отфильтровано из _MAX_ записей",
      paginate:{
            "next" : "<i class='material-icons medium' style='cursor:pointer'>chevron_right</i>",
            "previous":"<i class='material-icons medium' style='cursor:pointer'>chevron_left</i>"
            }

    },
        "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ],
                "lengthChange": true,
                "pageLength": 15,
                "lengthMenu":[[15,30,100,-1],[15,30,100,"Все"]],
                "pagingType": "simple",
                "dom": "<'row'<'col s7'f><'col s2'l><'col s1'i><'col s2' <'valign-wrapper'p>>><'row'<'col s12't>><'row'<'col s12'p>>"
                });
                $('select').material_select();
            });
            
            $('.dropdown-button').dropdown({
              inDuration: 300,
              outDuration: 225,
              constrainWidth: false, // Does not change width of dropdown to that of the activator
              hover: true, // Activate on hover
              gutter: 0, // Spacing from edge
              belowOrigin: false, // Displays dropdown below the button
              alignment: 'left', // Displays dropdown with edge aligned to the left of button
              stopPropagation: false // Stops event propagation
            }
            );
        </script>
        <script type="text/javascript" language="javascript">// <![CDATA[
            function checkAll(ele) {
                var checkboxes = document.getElementsByTagName('input');
                if (ele.checked) {
                   for (var i = 0; i < checkboxes.length; i++) {
                        if (checkboxes[i].type == 'checkbox') {
                            checkboxes[i].checked = true;
                        }
                    }
                } else {
                    for (var i = 0; i < checkboxes.length; i++) {
                        console.log(i)
                        if (checkboxes[i].type == 'checkbox') {
                            checkboxes[i].checked = false;
                        }
                   }
                }
            }
        </script>
        <script type="text/javascript">
            function insertArgs(fieldid,moduleid,actionid){
                inputfield = document.getElementById('actions[' + actionid + '][args]');
                label = document.getElementById('actions[' + actionid + '][args][label]');
                label.className = 'active';
                inputfield.value = inputfield.value + '{' + moduleid + '_' + fieldid + '}';
                replaceClass();
                return;
            }
            
            function insertArgsFields(fieldtopaste,moduleid,fieldid){
                inputfield = document.getElementById('fields[' + fieldid + '][args]');
                label = document.getElementById('fields[' + fieldid + '][args][label]');
                label.className = 'active';
                inputfield.value = inputfield.value + '{' + moduleid + '_' + fieldtopaste + '}';
                replaceClass();
                return;
            }
        </script>
        
        
        
        <header class="navbar-fixed">
                <nav class="top-nav">
                    <div class="nav-wrapper light-blue darken-1">
                        <a class="brand-logo" href="#">ops-tool</a>
                        <?render_logout_button()?>
                    </div>
                </nav>
        </header>
        <div class="row">
            <div class="col s2">
            <?php render_sidebar()?>
            </div>
            <div class="col s10">
                <div class="row">
                        <?php show_module()?>
                </div>
            </div>
        </div>
    </body>
</html>

