<?php

require_once getConf()->root_path.'/app/request/window/addComment/CommentForm.class.php';

class RequestShowCtrl{
    
    private $rid;
    private $form;
    
    
    public function __construct(){
        $this->form = new CommentForm();
    }
    
    public function validate(){
        $this->form->comment = getFromRequest('comment');
        
        loadMessages();
        return !getMessages()->isError();
    }
    
    function setRid(){
        $this->rid=getFromGet('rid');
        getSmarty()->assign('rid', $this->rid);
    }
    
    public function addToDb($comment, $rid){
        
        getDb()->insert("comments", ["req" => $rid, "user" => getUid(), "comment" => $comment]);
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
    }
    
    public function getFromDB(){
        $this->rid=getFromGet('rid');
        
        $this->records = getDb()->select("req_list", ["title", "date", "description", "team", "solved", "uid"], ["rid" => $this->rid]);
        
        $this->comments = getDb()->select("comments", ["[>]users" => ["user" => "uid"]], ["users.realname", "comments.comment", "comments.date"], ["comments.req" => $this->rid]);
        //print_r($this->comments);
        //die();
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('Wystąpił błąd podczas pobierania rekordów',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign('rid',$this->rid);
        getSmarty()->assign('date', $this->records[0]["date"]);
        getSmarty()->assign('title', $this->records[0]["title"]);
        getSmarty()->assign('description', $this->records[0]["description"]);
        getSmarty()->assign('comments', $this->comments);
    }
    
    public function goShow(){
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
    public function addComment(){
        $this->setRid();

        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/AddComment.html');
    }
    public function saveComment(){
        $this->validate();
        $this->setRid();
        $this->addToDb($this->form->comment, $this->rid);
        $this->goShow();
        //getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
}
