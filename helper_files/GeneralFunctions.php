<?php

class GeneralFunctions{

    function returnValue($return, $status){
        if ($status)
            return json_encode(array(
                'data' => json_encode($return),
                'success' => true
                )
            );
        else 
        return json_encode(array(
                'data' => json_encode($return),
                'success' => false
                )
            );
    }

}

?>    