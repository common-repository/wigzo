<?php
function array_return_values ($array) 
{ 
    $retValue = "[";  
    $count = 0;
    foreach ($array as $key => $value) 
    { 
        if (NULL != $value && $value != '') 
        { 
            if ($count != 0) {
                $retValue .= ",";
            }
            $retValue .= "'";
            $retValue .= $value;
            $retValue .= "'";
        } 
        $count++;
    } 
    $retValue.="]";
    return $retValue; 
}