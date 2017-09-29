<?php
if (!empty($_SESSION['login'])){
    //render_sidebar();
}
else{
    render_login();
}

