<?php
class InidentAddCtrl{
    private $form;
    
    public funtion __construct(){
        $this->form = new AddIncidentForm();
    }
    public function validate(){
        $this->form->title = getFromRequest('title');
        $this->form->description = getFromRequest('description');
        
        loadMessages();
		
		return ! getMessages()->isError();
    } 
    function addToDb(){
        
        $this->validate();
        
        getDb()->insert("inc_list", ["title" => "$this->form->title", "date" => date(Y-m-d), "description" => "$this->form->description", "team" => "helpdesk", "solved" => 0]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
    }
    public function goShowAdd(){
        getSmarty()->display(getConf()->root_path.'/app/incident/list/IncidentAddSucced.html'); 
    }
}
