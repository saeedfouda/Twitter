<?php

if(! function_exists('hideEmail')){
    function hideEmail($email)
    {
        $mail_parts = explode("@", $email);
        $length = strlen($mail_parts[0]);
        $show = floor($length/2);
        $hide = $length - $show;
        $replace = str_repeat("*", $hide);
        return substr_replace ( $mail_parts[0] , $replace , $show, $hide ) . "@" . substr_replace($mail_parts[1], "**", 0, 2);
    }
}
