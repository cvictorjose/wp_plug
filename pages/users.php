<?php

class CCWTS_User_Controller extends CCWTS_Controller {

  public function __construct(){
      parent::__construct();
      $this->setExtraAction('view_details');
  }

  public function view_details(){
      $users= ccwts_get_2('/users');
	   print_r ($users);
      
	}
}
