<?php

error_reporting(E_ALL);

function myCustomHandler($errno, $errstr, $errfile, $errline) {
    if(!(error_reporting() & $errno)) {
        return false;
        
    }
    
    switch($errno){
        
        case E_USER_ERROR:
            echo "<b>Custom Error #$errno:</b> $errstr";
            exit(1);
            break;
            
        case E_USER_WARNING:
            echo "<b>Custom Warning #$errno:</b> $errstr";
            break;
        
        case E_USER_NOTICE:
            echo "<b>Custom Notice #$errno:</b> $errstr <br/>";
            break;
            
        default:
            echo "<b>Unkown Error type #$errno:</b> $errstr";
            break;
            
    }
    
    return true;
}

set_error_handler("MyCustomHandler");

?>