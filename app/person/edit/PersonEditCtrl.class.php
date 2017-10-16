<?php
require_once "PersonEditForm.class.php";

class PersonEditCtrl {

	private $form; //dane formularza

	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new PersonEditForm();
	}

	//validacja danych przed zapisem (nowe dane lub edycja)
	public function validateSave() {
		//0. Pobranie parametrów z walidacją
		$this->form->id = getFromRequest('id',true,'Błędne wywołanie aplikacji');
		$this->form->name = getFromRequest('name',true,'Błędne wywołanie aplikacji');
		$this->form->surname = getFromRequest('surname',true,'Błędne wywołanie aplikacji');
		$this->form->birthdate = getFromRequest('birthdate',true,'Błędne wywołanie aplikacji');

		if ( getMessages()->isError() ) return false;

		// 1. sprawdzenie czy wartości wymagane nie są puste
		if (empty(trim($this->form->name))) {
			getMessages()->addMessage(new Message('Wprowadź imię',Message::ERROR));
		}
		if (empty(trim($this->form->surname))) {
			getMessages()->addMessage(new Message('Wprowadź nazwisko',Message::ERROR));
		}
		if (empty(trim($this->form->birthdate))) {
			getMessages()->addMessage(new Message('Wprowadź datę urodzenia',Message::ERROR));
		}

		if ( getMessages()->isError() ) return false;
		
		// 2. sprawdzenie poprawności przekazanych parametrów
		if ( ! validateDate($this->form->birthdate,'Y-m-d') ){
			getMessages()->addMessage(new Message('Zły format daty. Przykład: 2015-12-20',Message::ERROR));
		}
		
		return ! getMessages()->isError();
	}

	//validacja danych przed wyswietleniem do edycji
	public function validateEdit() {
		//pobierz parametry na potrzeby wyswietlenia danych do edycji
		//z widoku listy osób (parametr jest wymagany)
		$this->form->id = getFromRequest('id',true,'Błędne wywołanie aplikacji');
		return ! getMessages()->isError();
	}
	
	//wysiweltenie rekordu do edycji wskazanego parametrem 'id'
	public function goEdit(){
		// 1. walidacja id osoby do edycji
		if ( $this->validateEdit() ){
		  // 2. odczyt z bazy danych osoby o podanym ID (tylko jednego rekordu)
			$record = getDB()->get("person", "*",[
				"idperson" => $this->form->id
			]);
		  // 2.1 jeśli osoba istnieje to wpisz dane do obiektu formularza
			if (getDB()->error()[0]==0){
				$this->form->id = $record['idperson'];
				$this->form->name = $record['name'];
				$this->form->surname = $record['surname'];
				$this->form->birthdate = $record['birthdate'];
			} else { //jeśli istnieje kod błędu
				getMessages()->addMessage(new Message('Wystąpił nieoczekiwany błąd podczas odczytu rekordu',Message::ERROR));
				if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
			}
		} 
		
		// 3. Wygenerowanie widoku
		$this->generateView();		
	}

	public function goDelete(){		
		// 1. walidacja id osoby do usuniecia
		if ( $this->validateEdit() ){
		  // 2. usunięcie rekordu
			getDB()->delete("person",[
				"idperson" => $this->form->id
			]);
			if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
				getMessages()->addMessage(new Message('Wystąpił nieoczekiwany błąd podczas usuwania rekordu',Message::ERROR));
				if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
			} else {
				getMessages()->addMessage(new Message('Pomyślnie usunięto rekord',Message::INFO));
			}
		}
		
		// 3. Przekierowanie na stronę listy osób
		forwardTo('personList');		
	}

	public function goSave(){
			
		// 1. Walidacja danych formularza (z pobraniem)
		if ($this->validateSave()) {
			// 2. Zapis danych w bazie
			
			//2.1 Nowy rekord
			if ($this->form->id == '') {
				//sprawdź liczebność rekordów - nie pozwalaj przekroczyć 20
				$count = getDB()->count("person");
				if ($count <= 20) {
					getDB()->insert("person", [
						"name" => $this->form->name,
						"surname" => $this->form->surname,
						"birthdate" => $this->form->birthdate
					]);
				} else { //za dużo rekordów
					// 3a. Gdy za dużo rekordów to pozostań na stronie
					getMessages()->addMessage(new Message('Ograniczenie: Zbyt dużo rekordów. Aby dodać nowy usuń wybrany wpis.',Message::WARNING));
					$this->generateView(); //pozostań na stronie edycji
					exit(); //zakończ przetwarzanie, aby nie dodać wiadomości o pomyślnym zapisie danych
				}
			} else { 
			//2.2 Edycja rekordu o danym ID
				getDB()->update("person", [
					"name" => $this->form->name,
					"surname" => $this->form->surname,
					"birthdate" => $this->form->birthdate
				], [ 
					"idperson" => $this->form->id
				]);
			}
			if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
				getMessages()->addMessage(new Message('Wystąpił nieoczekiwany błąd podczas zapisu rekordu',Message::ERROR));
				if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
			} else {
				getMessages()->addMessage(new Message('Pomyślnie zapisano rekord',Message::INFO));
			}
			// 3b. Po zapisie przejdź na stronę listy osób (w ramach tego samego żądania http)
			forwardTo('personList');
		} else {
			// 3c. Gdy błąd to pozostań na stronie
			$this->generateView();
		}		
	}
	
	public function generateView(){
		getSmarty()->assign('form',$this->form);    // dane formularza do widoku
		getSmarty()->display(getConf()->root_path.'/app/person/edit/PersonEdit.html');
	}
}
 