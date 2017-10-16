<?php
require_once getConf()->root_path.'/app/person/list/PersonSearchForm.class.php';

class PersonListCtrl {

	private $form; //dane formularza wyszukiwania
	private $records; //rekordy pobrane z bazy danych

	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new PersonSearchForm();
	}
		
	public function validate() {
		// 1. sprawdzenie, czy parametry zostały przekazane
		// - nie trzeba sprawdzać
		$this->form->surname = getFromRequest('sf_surname');
	
		// 2. sprawdzenie poprawności przekazanych parametrów
		// - nie trzeba sprawdzać

		// 3. załaduj messages z sesji, jeśli jest (pozwala przekazywać komunikaty przez redirect)
		loadMessages();
		
		return ! getMessages()->isError();
	}
	
	public function process(){
		// 1. Walidacja danych formularza (z pobraniem)
		// - W tej aplikacji walidacja nie jest potrzebna, ponieważ nie wystąpią błedy podczas podawania nazwiska.
		//   Jednak pozostawiono ją, ponieważ gdyby uzytkownik wprowadzał np. datę, lub wartość numeryczną, to trzeba
		//   odpowiednio zareagować wyświetlając odpowiednią informację (poprzez obiekt wiadomości Messages)
		$this->validate();
		
		// 2. Przygotowanie mapy z parametrami wyszukiwania (nazwa_kolumny => wartość)
		$search_params = []; //przygotowanie pustej struktury (aby była dostępna nawet gdy nie będzie zawierała wierszy)
		if ( isset($this->form->surname) && strlen($this->form->surname) > 0) {
			$search_params['surname[~]'] = $this->form->surname.'%'; // dodanie symbolu % zastępuje dowolny ciąg znaków na końcu
		}
		
		// 3. Pobranie listy rekordów z bazy danych
		// W tym wypadku zawsze wyświetlamy listę osób bez względu na to, czy dane wprowadzone w formularzu wyszukiwania są poprawne.
		// Dlatego pobranie nie jest uwarunkowane poprawnością walidacji (jak miało to miejsce w kalkulatorze)
		
		//przygotowanie frazy where na wypadek większej liczby parametrów
		$num_params = sizeof($search_params);
		if ($num_params > 1) {
			$where = [ "AND" => &$search_params ];
		} else {
			$where = &$search_params;
		}
		//dodanie frazy sortującej po nazwisku
		$where ["ORDER"] = "surname";
		//wykonanie zapytania
		$this->records = getDB()->select("person", [
				"idperson",
				"name",
				"surname",
				"birthdate",
			], $where );
		if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
		
		// 4. dane dla widoku
		getSmarty()->assign('searchForm',$this->form); // dane formularza (wyszukiwania w tym wypadku)
		getSmarty()->assign('people',$this->records);  // lista rekordów z bazy danych
	}
	
	function goShow(){
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/person/list/PersonList.html');
	}
	
	function goShowPart(){ //dla AJAX
		$this->process();
		getSmarty()->display(getConf()->root_path.'/app/person/list/PersonListPart.html');
	}	
}
