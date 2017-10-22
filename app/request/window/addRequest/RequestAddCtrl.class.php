<?php

require_once getConf()->root_path.'/app/request/window/addRequest/RequestAddForm.class.php';

class RequestAddCtrl{
    private $form;
    
    public function __construct(){
        $this->form = new RequestAddForm();
    }
    public function validate(){
        $this->form->title = getFromRequest('title');
        $this->form->description = getFromRequest('description');
        
        loadMessages();
		
		return ! getMessages()->isError();
    } 
    function addToDb($title, $description){

        getDb()->insert("req_list", ["title" => $title, "date" => date("Y-m-d"), "description" => $description, "team" => "helpdesk", "solved" => 0, "uid" => getUid()]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas zapisywania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
    }
    public function goShowAdd(){
        $this->validate();
        $this->addToDb($this->form->title, $this->form->description);
        getSmarty()->display(getConf()->root_path.'/app/request/window/addRequest/RequestAddSucced.html'); 
    }
    public function goShow(){
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
}
