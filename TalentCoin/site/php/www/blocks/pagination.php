<?php

class Pagination {


	public $num = 10;

	function Page(){

		if(!isset($_GET['page'])){
  			$page = 1;
		}
		else{

  			if(intval($_GET['page'])){
    			$page = intval($_GET['page']);
  			}
  			else{
    			$page = 1;
  			}
		}

		return $page;
	}

	function forLimit(){
		$this_page_first_result = ($this->Page() - 1) * $this->num;	

		return $this_page_first_result;
	}

	

	function sqlRequest(){

	}

}

?>