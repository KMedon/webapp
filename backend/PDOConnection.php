<?php
// api/PDOConnection.php

$pdoConnection = null;

//define("APP_MODE", "development"); // Change to "production" as needed
define("APP_MODE", "MODE_PRODUCTION_AWARD");

function getPDOConnection() {
    global $pdoConnection;

    if ($pdoConnection === null) {
        if (APP_MODE === "development") {
            $host = "localhost";
            $port = "3306";
            $dbname = "multimedia";
            $username = "root";
            $password = "xpto";
        } elseif (APP_MODE === "MODE_PRODUCTION_AWARD") {
            // Use production settings
            $host = "fdb1029.awardspace.net";
            $port = "3306";
            $dbname = "4571085_multimedia";
            $username = "4571085_multimedia";
            $password = "Kubam20013#";
        }

        try {
            $pdoConnection = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdoConnection;
}
