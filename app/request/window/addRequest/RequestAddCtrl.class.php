<?php

require_once getConf()->root_path.'/app/request/window/addRequest/RequestAddForm.class.php';

class RequestAddCtrl{
    private $form;
    
    public function __construct(){
        $this->form = new RequestAddForm();
    }
    public function validate(){
        $this->form->title = getFromRequest('title',true,'Invalid system call');
        $this->form->description = getFromRequest('description',true,'Invalid system call');
        $this->form->type = getFromRequest('type',true,'Invalid system call');
        
        if (inRole('helpdesk')){
            $this->form->team = getFromRequest('team',true,'Invalid system call');
        }
        
        if (empty($this->form->title)) {
			getMessages()->addMessage(new Message('No title',Message::ERROR));
        }
        if (empty($this->form->description)) {
			getMessages()->addMessage(new Message('No description',Message::ERROR));
        }
        
        if (getMessages()->isError()) return false;
		
		return ! getMessages()->isError();
    }
    function addToDb(){
        
        if ($this->validate()){
            
            if(inRole('helpdesk')){
                getDb()->insert("req_list", ["title" => $this->form->title, "datetime" => time(), "description" => $this->form->description, "team" => $this->form->team, "progress" => "new", "uid" => getUid(), "rtid" => $this->form->type]);
            } else {
                getDb()->insert("req_list", ["title" => $this->form->title, "datetime" => time(), "description" => $this->form->description, "team" => "1", "progress" => "new", "uid" => getUid(),"rtid" => $this->form->type]);
            }

        
            if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			 getMessages()->addMessage(new Message('An error occurred while saving the records',Message::ERROR));
			     if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
            } else {
                getMessages()->addMessage(new Message('Request added correctly',Message::INFO));
			     storeMessages();
            }
			
		} else {
			getMessages()->addMessage(new Message('Error',Message::ERROR));
			storeMessages();
		}
        
    }
    function goShowAdd(){
        $this->addToDb();
        loadMessages();
        getSmarty()->display(getConf()->root_path.'/app/showMessages.html'); 
    }
}
