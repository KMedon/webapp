<?php
// api/PDOConnection.php

$pdoConnection = null;

define("APP_MODE", "development"); // Change to "production" as needed

function getPDOConnection() {
    global $pdoConnection;

    if ($pdoConnection === null) {
        if (APP_MODE === "development") {
            $host = "localhost";
            $port = "3306";
            $dbname = "multimedia";
            $username = "root";
            $password = "xpto";
        } elseif (APP_MODE === "production") {
            // Use production settings
            $host = "production_host";
            $port = "3306";
            $dbname = "prod_app";
            $username = "prod_user";
            $password = "prod_password";
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
