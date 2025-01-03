<?php 

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/library/constants.php');

// UNCOMMENT ONE OF THESE TWO LINES FOR SETTING THE APP_MODE 
define("APP_MODE", MODE_DEVELOPMENT);
//define("APP_MODE", MODE_PRODUCTION_AWARD);
//define("APP_MODE", MODE_PRODUCTION_GRIS);


if (defined("APP_MODE")) {
    if (APP_MODE == MODE_DEVELOPMENT) {
        // Use these settings for the development localhost
        define("DATABASE_HOST", "localhost");
        define("DATABASE_PORT", 3306);
        define("DATABASE_USER",  "root");
        define("DATABASE_PASSWORD",  "xpto");  // Your current dev password
        define("DATABASE_NAME",  "multimedia");  // Your new multimedia database
        
    } else if (APP_MODE == MODE_PRODUCTION_AWARD) {
        // Use these settings for the PRODUCTION server
        define("DATABASE_HOST", "pdb1039.awardspace.net");
        define("DATABASE_PORT", 3306);
        define("DATABASE_USER",  "4212750_hiscale");
        define("DATABASE_PASSWORD",  "3+112358_material_13213455");
        define("DATABASE_NAME",  "4212750_hiscale");
    } else if (APP_MODE == MODE_PRODUCTION_GRIS) {
        // Use these settings for the PRODUCTION server
        define("DATABASE_HOST", "pdb1039.awardspace.net");
        define("DATABASE_PORT", 3306);
        define("DATABASE_USER",  "4212750_hiscale");
        define("DATABASE_PASSWORD",  "3+112358_material_13213455");
        define("DATABASE_NAME",  "4212750_hiscale");
    } else {
        echo '<h2> ... App mode not set to existing modes ... </h2>';
        die;
    }
} else {
    echo '<h2>App mode not set...</h2>';
    die;
}
