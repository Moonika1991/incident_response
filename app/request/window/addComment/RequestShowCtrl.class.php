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
        
        if(empty($this->form->comment)){
            getMessages()->addMessage(new Message('No comment',Message::ERROR));
        }
        
        if (getMessages()->isError()) return false;
        
        storeMessages();
        
        return !getMessages()->isError();
    }
    
    function setReqid(){
        $this->reqid=getFromGet('reqid');
        getSmarty()->assign('reqid', $this->reqid);
    }
    
    public function addToDb(){
        
        if($this->validate()){
             getDb()->insert("comments", ["req" => $this->reqid, "user" => getUid(), "comment" => $this->form->comment, "date" => date("Y-m-d h:i:sa")]);
        
            if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
                getMessages()->addMessage(new Message('An error occurred while saving',Message::ERROR));
            if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
            
            }
            $this->goShow();
        } else 
            getSmarty()->display(getConf()->root_path.'/app/showMessages.html');
        
        storeMessages();
    
    }
    
    public function getFromDB(){
        $this->reqid=getFromGet('reqid');
        
        $this->records = getDb()->select("req_list", ["title", "datetime", "description", "team", "progress", "uid"], ["reqid" => $this->reqid]);
        
        $this->realname = getDb()->select("users", ["realname"], ["uid" => $this->records[0]["uid"]]);
        $this->comments = getDb()->select("comments", ["[>]users" => ["user" => "uid"]], ["users.realname", "comments.comment", "comments.date"], ["comments.req" => $this->reqid]);
        //print_r($this->comments);
        //die();
        
        if (getDB()->error()[0]!=0){ //jeśli istnieje kod błędu
			getMessages()->addMessage(new Message('An error occurred while retrieving records',Message::ERROR));
			if (getConf()->debug) getMessages()->addMessage(new Message(var_export(getDB()->error(), true),Message::ERROR));
		}
        
        getSmarty()->assign('reqid',$this->reqid);
        getSmarty()->assign('date', $this->records[0]["datetime"]);
        getSmarty()->assign('title', $this->records[0]["title"]);
        getSmarty()->assign('realname', $this->realname[0]["realname"]);
        getSmarty()->assign('description', $this->records[0]["description"]);
        getSmarty()->assign('comments', $this->comments);
    }
    
    public function goShow(){
        loadMessages();
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
    public function addComment(){
        $this->setReqid();

        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/AddComment.html');
    }
    public function saveComment(){
        $this->setReqid();
        $this->addToDb();
    }
    public function editRequest(){
        $this->progress = getFromGet('p');
        
        $this->getFromDb();
        
        
        getDb()->update("req_list", ["progress" => $this->progress], ["reqid" => $this->reqid]);
        
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
}
