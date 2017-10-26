<?php

class EditWindowCtrl{
    
    function reload(){
        getSmarty()->display(getConf()->root_path.'/app/request/window/EditWindowPart.html');
    }
}