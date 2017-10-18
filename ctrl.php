<?php
require_once 'init.php';
// Po załadowaniu skryptu 'init.php' w całej aplikacji dostępne są obiekty:
// konfiguracji, smarty, messages oraz bazy danych Medoo (Smarty i Medoo ładowane i tworzone w momencie pierwszego użycia)
// za pomocą funkcji: getConf(), getSmarty(), getMessages() oraz getDB()
// dodatkowo szereg przydatnych funkcji:
// - getFromRequest(), getFromSession(), getFromCookies(), getFromPost(), getFromGet()
// pozwalają one od razu wygenerowac błąd (Message) gdy parametr jest wymagany
// - forwardTo(), redirectTo() czyli przekazanie żądania lub przekierowanie przeglądarki do podanej akcji
// - addRole(), inRole() czyli możliwość zapisania nazwy ról uzytkwnika w sesji i sprawdzenie czy użytkownik jest w danej roli
// - funkcja control() upraszczająca wywołanie metody wskazanego kontrolera ze zintegrowaną ochroną (na podstawie roli)
// - funkcje pozwalające na przechowywanie Messages, obiektów i danych w sesji: storeMessages/loadMessages, storeObject/loadObject, storeData/loadData
// - funkcja validateDate() sprawdzająca poprawność daty (walidator)
// - jest również dostępna zmienna $action inicjowana z parametru żądania

getConf()->login_action = 'loginShow'; // akcja przekierowania dla chronionej zawartości gdy użytkownik nie zalogowany

switch ($action){
	case 'loginShow':
		control('/app/login/','LoginCtrl','generateView'); // publiczna
	case 'login':
		control('/app/login/','LoginCtrl','doLogin'); // publiczna
	case 'logout':
		control('/app/login/','LoginCtrl','doLogout'); // publiczna
	case 'personNew':
		control('/app/person/edit/','PersonEditCtrl','generateView','user'); //rola user
	case 'personEdit':
		control('/app/person/edit/','PersonEditCtrl','goEdit','user'); //rola user
	case 'personSave':
		control('/app/person/edit/','PersonEditCtrl','goSave','user'); //rola user
	case 'personDelete':
		control('/app/person/edit/','PersonEditCtrl','goDelete','admin'); //rola admin
	case 'personListPart': //AJAX - wysłanie samej tabeli HTMLowej
		control('/app/person/list/','PersonListCtrl','goShowPart','admin'); // publiczna
	default : //'incidentList' akcja domyślna
		control('/app/incident/list/','IncidentListCtrl','goShow','user'); // publiczna
}