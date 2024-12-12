<?php
class config {

    function base_url() {
        require_once('conn.php');
        return URL;
    }

    function connectDB() {
        require_once('conn.php');
        $dbc = new mysqli(H, U, P, DB);
        return $dbc;
    }
    function clean($data,$type){
        $data = trim($data);
        if($type=="post") {
            $data = filter_input(INPUT_POST, $data, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        else if($type=="get") {
            $data = filter_input(INPUT_GET, $data, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
        }

        if(is_null($data)) {
            $data = "";
        }

         return $data;
    }
}

?>