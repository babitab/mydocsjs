<?php 
/*
* This file contains book keeping functions
*/

require_once 'core/init.php';

function sanitize($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

 ?>