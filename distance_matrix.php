<?php

    require 'Google/Maps/DistanceMatrix.php';

    $distance = new Google\Maps\DistanceMatrix();

    $distance->setOrigins(array(
        "Lyon",
        "L'Arbresle"
    ));

    $distance->setDestinations(array(
        "Paris",
        "Toulouse",
    ));

    if($distance->request()) {
        var_dump($distance->rows);
    } else {
        die($distance->error);
    }
