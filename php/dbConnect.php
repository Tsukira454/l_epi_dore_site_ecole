<?php

require(__DIR__ . "/../secure/identifiant.php");

try {
    $dbEpidore = new PDO("mysql:host=".$DBHOST.";dbname=".$DBNAME, $DBUSER, $DBPASSWORD);
    $dbEpidore->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "ZUT !!! Encore une erreur ligne ".$e->getLine()." dans le fichier ".$e->getFile().":
        ".$e->getMessage();
}
