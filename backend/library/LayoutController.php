<?php
namespace backend\library;

use \backend\library\Controller;

class LayoutController extends Controller
{
    function __construct()
    {

    }

    function showLayout($layoutPath) {
        include($layoutPath);
    }
}
