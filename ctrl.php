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
    case 'changePassword':
        control('/app/login/password/', 'PasswordChangeCtrl', 'changePassword', 'user');
    case 'savePassword':
        control('/app/login/password/', 'PasswordChangeCtrl', 'savePass', 'user');
	case 'newRequest':
		control('/app/request/list/','RequestListCtrl','goShowNew','user'); //rola user
    case 'searchRequest':
        control('/app/request/list/', 'RequestListCtrl', 'search');
	case 'addRequest':
		control('/app/request/window/addRequest/','RequestAddCtrl','goShowAdd','user'); //rola user
	case 'showRequest':
		control('/app/request/window/addComment/','RequestShowCtrl','goShow','user'); //rola user
	case 'addComment':
		control('/app/request/window/addComment/','RequestShowCtrl','addComment','user');
    case 'saveComment':
        control('/app/request/window/addComment/','RequestShowCtrl','saveComment','user');
    case 'editRequest':
        control('/app/request/window/addComment/','RequestShowCtrl','editRequest','helpdesk');
    case 'newUser':
        control('/app/request/window/addUser/','AddUserCtrl','showNewUser','admin');
    case 'addNewUser':
        control('/app/request/window/addUser/','AddUserCtrl','addNewUser','admin');
	case 'showListPart': //AJAX - wysłanie samej tabeli HTMLowej
		control('/app/request/list/','RequestListCtrl','goShowPart','user'); // publiczna
    case 'showUsersList':
        control('/app/users/show/', 'UsersListCtrl', 'goShow', 'admin');
    case 'showUser':
        control ('/app/users/show/', 'UsersListCtrl', 'goShowUser', 'admin');
    case 'showEditUser':
        control ('/app/users/show/', 'UsersListCtrl', 'goShowEdit', 'admin');
    case 'saveEditUser':
        control ('/app/users/show/', 'UsersListCtrl', 'goShowSave', 'admin');
    case 'reloadAddWindow':
        control('/app/request/window/', 'EditWindowCtrl', 'reload', 'user');
	default : //'incidentList' akcja domyślna
		control('/app/request/list/','RequestListCtrl','goShow','user'); // publiczna
}