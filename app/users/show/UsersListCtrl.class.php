<?php

class UsersListCtrl{
    
    private $uid;
    
    function setUid(){
        $this->uid=getFromGet('uid');
        getSmarty()->assign('uid', $this->uid);
    }
    
    function getFromDb(){
        
        $this->records = getDb()->select("users", ["username", "realname"], ["uid" => $this->uid]);
        
        $this->roles = getDb()->select("roles", ["[>]user_role" => ["rid" => "rid"]], ["roles.role"], ["user_role.uid" => $this->uid] );
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
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
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'app/users/show/EditUser.html');
    }
}