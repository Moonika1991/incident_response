<?php
require_once "PasswordChangeForm.class.php";

class PasswordChangeCtrl{
    
	private $form;
	
	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new PasswordChangeForm();
	}
    
    public function validate() {
		$this->form->oldPass = getFromRequest('oldPass',true,'Invalid system call');
		$this->form->newPass1 = getFromRequest('newPass1',true,'Invalid system call');
        $this->form->newPass2 = getFromRequest('newPass2',true,'Invalid system call');

		//nie ma sensu walidować dalej, gdy brak parametrów
		if (getMessages()->isError()) return false;
		
		// sprawdzenie, czy potrzebne wartości zostały przekazane
		if (empty($this->form->oldPass)) {
			getMessages()->addMessage(new Message('Please enter old password',Message::ERROR));
		}
		if (empty($this->form->newPass1)) {
			getMessages()->addMessage(new Message('Please enter new password',Message::ERROR));
		}
        if (empty($this->form->newPass2)) {
			getMessages()->addMessage(new Message('Please enter new password twice',Message::ERROR));
		}

		//nie ma sensu walidować dalej, gdy brak wartości
		if (getMessages()->isError()) return false;
		
		return ! getMessages()->isError();
	}
    
    function changePassword(){
        
        $this->realname = getUname();
        
        getSmarty()->assign('realname', $this->realname);
        
        getSmarty()->display(getConf()->root_path.'/app/login/password/PasswordChangeView.html');
    }
    
    function savePass(){
        $this->validate();
        
        if($this->form->oldPass == getDb()->select("users", ["password"], ["uid" => getUid()])[0]['password']){
            if($this->form->newPass1 == $this->form->newPass2){
                
                getDb()->update("users", ["password" => $this->form->newPass1], ["uid" => getUid()]);
                getMessages()->addMessage(new Message('Password changed correctly',Message::INFO));
            } else {
                getMessages()->addMessage(new Message('New passwords are different',Message::ERROR));
            }
        } else {
            getMessages()->addMessage(new Message('Incorrect old password',Message::ERROR));
        }
        
        storeMessages();
        
        redirectTo("incidentList");
    }
}