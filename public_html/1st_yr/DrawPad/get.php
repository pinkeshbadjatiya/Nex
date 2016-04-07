<?php
        session_start();
        if (isset($_SESSION["li"]) {
          $data = json_decode( file_get_contents("data.json", true) );
          $data[0]['sw'];
          $data[0]['sc'];
          $data[0]['d'];
        
          //     echo json_encode( $data[10:20]; 
        }
        else {
                $data = json_decode( file_get_contents("data.json") );
                $_SESSION["li"] = count($data);
                echo json_encode( $data );
        }
