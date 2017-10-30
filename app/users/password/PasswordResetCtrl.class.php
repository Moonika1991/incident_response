<?php

require_once getConf()->root_path.'/app/users/password/PasswordResetForm.class.php';

class PasswordResetCtrl{
    
    private $form;
    
    function __construct(){
        $this->form = new PasswordResetForm();
    }
    
    function validate(){
        
        $this->form->newPass = getFromRequest('newPass');
        
        loadMessages();
		
		return ! getMessages()->isError();
    }
    
    function updatePass(){
        $this->uid = getFromGet('uid');
        
        getDb()->update("users", ["password" => $this->form->newPass], ["uid" => $this->uid]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		} else {
           getMessages()->addMessage(new Message('Password reset succed',Message::INFO)); 
        }
        
        storeMessages();
    }
    
    function saveReset(){
        $this->validate();
        $this->updatePass();
        loadMessages();
        getSmarty()->display(getConf()->root_path.'/app/showMessages.html'); 
    }
}