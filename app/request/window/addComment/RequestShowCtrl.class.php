<?php

require_once getConf()->root_path.'/app/request/window/addComment/CommentForm.class.php';

class RequestShowCtrl{
    
    private $form;
    
    
    public function __construct(){
        $this->form = new CommentForm();
    }
    
    public function validate(){
        $this->form->comment = getFromRequest('comment');
        
        loadMessages();
        return !getMessages()->isError();
    }
    
    public function getFromDB(){
        $this->rid=getFromGet('rid');
        
        $this->records = getDb()->select("req_list", ["title", "date", "description", "team", "solved", "uid"], ["rid" => $this->rid]);
        
        getSmarty()->assign('rid',$this->rid);
        getSmarty()->assign('date', $this->records[0]["date"]);
        getSmarty()->assign('title', $this->records[0]["title"]);
        getSmarty()->assign('description', $this->records[0]["description"]);
    }
    
    public function goShow(){
        $this->getFromDb();
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/RequestShow.html');
    }
    public function addComment(){
        getSmarty()->display(getConf()->root_path.'/app/request/window/addComment/AddComment.html');
    }
}
