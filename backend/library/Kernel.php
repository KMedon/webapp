<?php

namespace backend\library\Kernel;

use backend\library\Route;

class Kernel {
    static $kernelInstance = null;

    static $routerInstance = null;

    static public function factory(): Kernel    {
        if(Kernel::$kernelInstance == NULL) {
            Kernel::$kernelInstance = new Kernel();
        }
        return Kernel::$kernelInstance;
    } 

    public function handle() {

    }
}