<?php

    require 'Google/Maps/Directions.php';

    $directions = new Google\Maps\Directions();

    $directions->setOrigin("Lyon");
    $directions->setDestination("Paris");

    if($directions->request()) {
        var_dump($directions->response);
    } else {
        die($directions->error);
    }
