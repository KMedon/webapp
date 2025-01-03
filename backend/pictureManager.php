<?php

function savePicture($base64Picture) {
    $pictureFolder = __DIR__ . "/pictures";
    if (!is_dir($pictureFolder)) {
        mkdir($pictureFolder, 0777, true);
    }

    $pictureData = explode(',', $base64Picture);
    $decodedPicture = base64_decode($pictureData[1]);

    $fileName = uniqid('img_', true) . ".jpg";
    $filePath = $pictureFolder . "/" . $fileName;

    file_put_contents($filePath, $decodedPicture);

    return $fileName;
}

function deletePicture($pictureID) {
    $pictureFolder = __DIR__ . "/pictures";
    if (!is_dir($pictureFolder)) {
        mkdir($pictureFolder, 0777, true);
    }

    $filePath = $pictureFolder . "/" . $pictureID;

    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

function getPicture($pictureID) {
    $pictureFolder = __DIR__ . "/pictures";
    if (!is_dir($pictureFolder)) {
        mkdir($pictureFolder, 0777, true);
    }
    $filePath = $pictureFolder . "/" . $pictureID;

    if (file_exists($filePath)) {
        $fileContents = file_get_contents($filePath);
        $base64Picture = base64_encode($fileContents);

        return 'data:image/jpeg;base64,' . $base64Picture;
    } else {
        return null;
    }
}