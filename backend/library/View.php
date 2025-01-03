<?php
namespace backend\library;



define("MODE_SHOW", 0);
define("MODE_INSERT", 1);
define("MODE_EDIT", 2);
define("MODE_DELETE", 3);


interface ViewInterface {
    function setAlertMessage($alertMessage): ViewInterface;
    function setAlertType($alertType) : ViewInterface;
    function setAlertTimout($alertTimout): ViewInterface;
    function viewData($key, $value) : ViewInterface;

    function getView(): ViewInterface;
    function setViewFolder($viewFolder) : ViewInterface;
    function setViewURL($viewFolder): ViewInterface;

    function show($document, $MODE=null);    
}

class View implements ViewInterface{    
    private $viewFolder       =null;
    private $viewURL          =null;
    
    private $alertMessage;
    private $alertType;
    private $alertTimout = 5000;

    private $viewData=array();
    

    public function showAlert() {        
    // <<< is used to construct huge strings, this case with html code    
    echo <<<HEREDOC
            <div class="alert" role="alert" id="idAlertMessage">                          
                <!-- message will appear here -->
            </div>
            <script>                
                function showAlert(message, alertType, timeout) {
                    var elementAlert =  document.getElementById("idAlertMessage"); 
                    elementAlert.innerHTML = message
                    elementAlert.classList.remove(elementAlert.classList);
                    elementAlert.classList.add("alert", alertType)
                    setTimeout(function(){ 
                        var elementAlert =  document.getElementById("idAlertMessage");
                        elementAlert.style.display = 'none';
                    },  timeout);
                }    
                
                var msg='$this->alertMessage';
                
                if(msg!=='') {
                    showAlert('$this->alertMessage', '$this->alertType', $this->alertTimout);
                }
                
            </script>                    
    HEREDOC;

    } 

    function __construct($viewName=null)
    {   
        $pathToView = DOCUMENT_ROOT . "/mvc/" . Route::$moduleName . "/views/" .  $viewName . ".php";   
        echo $pathToView;
    }

    static public function make($viewName, $parameters = null) {
        //$moduleName =         
        include $viewName;
        
    }


    function getView(): ViewInterface {
        return $this;
    }


    function setViewFolder($viewFolder) :ViewInterface {
        $this->viewFolder = $viewFolder;
        return $this;
    }

    function setViewURL($viewURL): ViewInterface {
        $this->viewURL = $viewURL;
        return $this;
    }



    function setAlertMessage($alertMessage): ViewInterface {
        $this->alertMessage = $alertMessage;
        return $this;
    }

    function setAlertType($alertType): ViewInterface {
        $this->alertType = $alertType;
        return $this;
    }

    function setAlertTimout($alertTimout): ViewInterface {
        $this->alertTimout = $alertTimout;
        return $this;
    }

    function viewData($key, $value) : ViewInterface {
        $this->viewData[$key] =  $value;
        return $this;
    }

    function show($document,  $MODE=null) {
        foreach($this->viewData as $key => $value) {
            ${$key} = $value;  // this is amazing!!
            //echo $key . '::'.$value . '<br/>'; 
        }
       // die;

        ${"viewFolder"}   = $this->viewFolder;
        ${"viewURL"}      = $this->viewURL;

        
        require $viewFolder . '/' . $document;        
    }


    static function showServiceNotFoundMessage()
    {   
        include __DIR__ . '/route_not_found_html.php';
    }

}
