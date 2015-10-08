<?php

    require 'Google/Maps/Geocoding.php';

    $geocoding = new Google\Maps\Geocoding();

    $geocoding->setAddress('14 Avenue des Champs-Elysées Paris France');

    if($geocoding->request()) {
        var_dump($geocoding->response);
    } else {
        die($geocoding->error);
    }
