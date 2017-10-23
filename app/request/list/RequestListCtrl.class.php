<?php

class RequestListCtrl {


	public function process(){
		$this->records = getDB()->select("req_list", [
                "rid",
				"title",
                "date",
                "team",
                "solved",
			]);
		if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}

		getSmarty()->assign('request',$this->records);  // lista rekordów z bazy danych
        getSmarty()->assign('realname', getUname());

	}
	
	function goShow(){
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/request/list/RequestView.html',false);
	}
	
	function goShowPart(){ //dla AJAX
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/request/list/RequestListPart.html');
	}
    function goShowNew(){
        getSmarty()->display(getConf()->root_path.'/app/request/window/addRequest/RequestAddPart.html');
    }
}
