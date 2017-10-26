<?php

require_once getConf()->root_path.'/app/request/window/addUser/NewUserForm.class.php';

class AddUserCtrl{
    
    private $form;
    
    function __construct(){
        $this->form = new NewUserForm();
    }
    
    function validate(){
        $this->form->username = getFromRequest('username');
        $this->form->password = getFromRequest('password');
        $this->form->realname = getFromRequest('realname');
        $this->form->role = getFromRequest('role');
        
        loadMessages();
		
		return ! getMessages()->isError();
    }
    
    function setRoles(){
        $this->roles = getDb()->select("roles", ["rid", "role"]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign("roles", $this->roles);
    }
    
    function addToDb(){
        
        $this->validate();
        
        getDb()->insert("users", ["username" => $this->form->username, "password" => $this->form->password, "realname" => $this->form->realname]);
        
        $this->uid = getDb()->select("users", ["uid"], ["username" => $this->form->username])[0]["uid"];
        
        foreach ($this->form->role as $r){
            getDb()->insert("user_role", ["uid" => $this->uid, "rid" => $r]);
        }
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas zapisywania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
    }
    
    public function showNewUser(){
        $this->setRoles();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addUser/newUser.html');
    }
    
    public function addNewUser(){
        $this->addToDb();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addUser/success.html');
    }
}