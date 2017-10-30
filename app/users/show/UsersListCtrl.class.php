<?php

require_once getConf()->root_path.'/app/users/show/EditUserForm.class.php';

class UsersListCtrl{
    
    private $uid;
    private $form;
    
    function __construct(){
        $this->form = new EditUserForm();
    }
    
    function validate(){
        $this->form->username = getFromRequest('username');
        $this->form->password = getFromRequest('password');
        $this->form->realname = getFromRequest('realname');
        $this->form->rolesToDel = getFromRequest('role');
        $this->form->rolesToAdd = getFromRequest('roleToAdd');
        
        loadMessages();
		
		return ! getMessages()->isError();
    }
    
    function setUid(){
        $this->uid=getFromGet('uid');
        getSmarty()->assign('uid', $this->uid);
    }
    
    function getFromDb(){
        
        $this->records = getDb()->select("users", ["username", "realname"], ["uid" => $this->uid]);
        
        $this->roles = getDb()->select("roles", ["[>]user_role" => ["rid" => "rid"]], ["roles.rid","roles.role"], ["user_role.uid" => $this->uid] );
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign('user', $this->records[0]);
        getSmarty()->assign('roles', $this->roles);
    }
    
    public function process(){
        
        $this->records = getDb()->select("users", ["uid", "username", "realname"]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign('users', $this->records);
    }
    
    function setRoles(){
        $this->rolesToAdd = getDb()->select("roles", ["rid", "role"]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign("rolesToAdd", $this->rolesToAdd);
    }
    
    function updateDb(){

        if($this->form->username != NULL)
            getDb()->update("users", ["username" => $this->form->username], ["uid" => $this->uid]);
        if($this->form->password != NULL)
            getDb()->update("users", ["password" => $this->form->password], ["uid" => $this->uid]);
        if($this->form->realname != NULL)
            getDb()->update("users", ["realname" => $this->form->realname], ["uid" => $this->uid]);
        
        if($this->form->rolesToDel != NULL){
            foreach ($this->form->rolesToDel as $r){
                getDb()->delete("user_role", ["AND" => ["uid" => $this->uid, "rid" => $r]]);
            }
        }
        
        if($this->form->rolesToAdd != NULL){
            foreach ($this->form->rolesToAdd as $r){
            getDb()->insert("user_role", ["uid" => $this->uid, "rid" => $r]);
            }
        }
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occured while saving',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
        } else
            getMessages()->addMessage(new Message('Changes saved correctly',Message::INFO));
        
        storeMessages();
    }
    
    function goShow(){
        $this->process();
        getSmarty()->display(getConf()->root_path.'/app/users/show/UsersList.html');
    }
    
    function goShowUser(){
        $this->setUid();
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'/app/users/show/ShowUser.html');
    }
    
    function goShowEdit(){
        $this->setUid();
        $this->setRoles();
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'/app/users/show/EditUser.html');
    }
    function goShowSave(){
        $this->setUid();
        $this->validate();
        $this->updateDb();
        loadMessages();
        getSmarty()->display(getConf()->root_path.'/app/showMessages.html'); 
    }
    function resetPass(){
        $this->setUid();
        getSmarty()->display(getConf()->root_path.'/app/users/password/PasswordReset.html');
    }
}
