<?php

require_once getConf()->root_path.'/app/request/list/SearchForm.class.php';

class RequestListCtrl {
    
    private $form;
    
    public function __construct(){
        $this->form = new SearchForm();
    }
    
    public function validate(){
        
        $this->form->search = getFromRequest('search');
        
        loadMessages();
        return !getMessages()->isError();
    }
    
    function setTeamList(){
        if(inRole("helpdesk")){
            $this->teams = getDb()->select("roles", ["rid", "role"]);
            unset($this->teams[0]);
            unset($this->teams[1]);
            
            if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
                getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
                if (getConf()->debug) 
                    getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
            }
        
            getSmarty()->assign('teams', $this->teams);
        }
    }
    
    function setRequestType(){
        $this->types = getDb()->select("req_type", ["rtid", "name"]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign("types", $this->types);
    }

	public function process(){
		$this->records = getDB()->select("req_list", ["[>]roles" => ["team" => "rid"]],[
                "req_list.reqid",
				"req_list.title",
                "req_list.datetime",
                "roles.role",
                "req_list.progress",
			], ["ORDER" => ["req_list.datetime" => "DESC"]]);
		if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}

		getSmarty()->assign('request',$this->records);
        getSmarty()->assign('realname', getUname());

	}
	
	function goShow(){
        loadMessages();
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/request/list/RequestView.html',false);
	}
	
	function goShowPart(){ //dla AJAX
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/request/list/RequestListPart.html');
	}
    function goShowNew(){
        $this->setTeamList();
        $this->setRequestType();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addRequest/RequestAddPart.html');
    }
    function search(){
        
        $this->validate();

        
        $this->records = getDB()->select("req_list", ["[>]roles" => ["team" => "rid"]],[
                "req_list.reqid",
				"req_list.title",
                "req_list.datetime",
                "roles.role",
                "req_list.progress",
			], ["req_list.reqid" => $this->form->search]);
		if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}

		getSmarty()->assign('request',$this->records);
        
        getSmarty()->display(getConf()->root_path.'/app/request/list/RequestListPart.html');
    }
}
