<?php
require_once "LoginForm.class.php";

class LoginCtrl{
	private $form;
	
	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new LoginForm();
	}
    
    private function getRoles($uid){
        $roleNames = array();
        $roleIds = array();
        $roles = getDb()->select('user_role',['rid'], ['uid' => $uid]);
        foreach ($roles as $roleId){
            $roleIds[] = $roleId['rid'];
        }
        $rolesChecked = array();
        $rolesToCheck =$roleIds;
        while (count($rolesToCheck) != 0){
            foreach($rolesToCheck as $roleId){
                foreach (getDb()->select('role_role', ['imported_id'], ['role_id' => $roleId]) as $role){
                    $roleIds[] = $role['imported_id'];
                }
                $rolesChecked[] = $roleId;
            }
            $rolesToCheck = array_diff($roleIds, $rolesChecked);
        }
        foreach ($roleIds as $role){
            $roleNames[] = getDb()->select('roles', ['role'], ['rid' => $role])[0]['role'];
        }
        return $roleNames;
    }
		
	public function validate() {
		$this->form->login = getFromRequest('login',true,'Błędne wywołanie systemu');
		$this->form->pass = getFromRequest('pass',true,'Błędne wywołanie systemu');

		//nie ma sensu walidować dalej, gdy brak parametrów
		if (getMessages()->isError()) return false;
		
		// sprawdzenie, czy potrzebne wartości zostały przekazane
		if (empty($this->form->login)) {
			getMessages()->addMessage(new Message('Nie podano loginu',Message::ERROR));
		}
		if (empty($this->form->pass)) {
			getMessages()->addMessage(new Message('Nie podano hasła',Message::ERROR));
		}

		//nie ma sensu walidować dalej, gdy brak wartości
		if (getMessages()->isError()) return false;
		
		// sprawdzenie, czy dane logowania poprawne
		// (takie informacje najczęściej przechowuje się w bazie danych)
        $user = getDb()->select('users',array('uid', 'realname'),array('AND' => array('username' => $this->form->login, 'password' => $this->form->pass)));
        if(count($user) > 0){
            setUname($user[0]['realname']);
            setUid($user[0]['uid']);
            foreach ($this->getRoles($user[0]['uid']) as $role){
                addRole($role);
            }
            
			//addRole('user');
            //dodanie ról
        } else {
			getMessages()->addMessage(new Message('Niepoprawny login lub hasło',Message::ERROR));
		}
		
		return ! getMessages()->isError();
	}
	
	public function doLogin(){
		if ($this->validate()){
			//zalogowany => przekieruj na główną akcję (z przekazaniem messages przez sesję)
			getMessages()->addMessage(new Message('Poprawnie zalogowano do systemu',Message::INFO));
			storeMessages();
			redirectTo("incidentList");
		} else {
			//niezalogowany => pozostań na stronie logowania
			$this->generateView(); 
		}		
	}
	
	public function doLogout(){
		// 1. zakończenie sesji
		session_destroy();
		// 2. idź na stronę główną (z przekazaniem messages przez sesję)
		session_start(); //rozpocznij nową sesję w celu przekazania messages w sesji
		getMessages()->addMessage(new Message('Poprawnie wylogowano z systemu',Message::INFO));
		storeMessages();
		redirectTo('incidentList');
	}
	
	public function generateView(){
		getSmarty()->assign('form',$this->form); // dane formularza do widoku
		getSmarty()->display(getConf()->root_path.'/app/login/LoginView.html');		
	}
}