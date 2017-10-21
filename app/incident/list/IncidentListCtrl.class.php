<?php
//require_once getConf()->root_path.'/app/person/list/PersonSearchForm.class.php';

class IncidentListCtrl {

	private $form; //dane formularza wyszukiwania
	private $records; //rekordy pobrane z bazy danych

	public function __construct(){
		//stworzenie potrzebnych obiektów
		//$this->form = new PersonSearchForm();
	}
		
	public function validate() {
		// 1. sprawdzenie, czy parametry zostały przekazane
		// - nie trzeba sprawdzać
		//$this->form->surname = getFromRequest('sf_surname');
	
		// 2. sprawdzenie poprawności przekazanych parametrów
		// - nie trzeba sprawdzać

		// 3. załaduj messages z sesji, jeśli jest (pozwala przekazywać komunikaty przez redirect)
		loadMessages();
		
		return ! getMessages()->isError();
	}
	
	public function process(){
		$this->records = getDB()->select("inc_list", [
				"title",
                "date",
                "team",
                "solved",
			]);
		if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
		
		// 4. dane dla widoku
		// getSmarty()->assign('searchForm',$this->form); // dane formularza (wyszukiwania w tym wypadku)
		getSmarty()->assign('incident',$this->records);  // lista rekordów z bazy danych
        getSmarty()->assign('realname', getUname());

	}
	
	function goShow(){
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/incident/list/IncidentList.html',false);
	}
	
	function goShowPart(){ //dla AJAX
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/incident/list/IncidentListPart.html');
	}
    function goShowNew(){
        getSmarty()->display(getConf()->root_path.'/app/incident/add/IncidentAddPart.html');
    }
}
