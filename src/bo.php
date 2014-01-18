<?php

/**
 * @SWG\Model(id="APIserver",required="['nom']")
 */
class APIserver
{
    /**
	* @SWG\Property(name="nom",type="string")
	*/
    public $nom;
    /**
	* @SWG\Property(name="url",type="string")
	*/
    public $url;
	
	function __construct($nom,$url=''){
		$this->nom = $nom;
		$this->url = $url;
	}
}

/**
 * @SWG\Model(id="DBEntity",required="['nom']")
 */
class DBEntity
{
    /**
	* @SWG\Property(name="id",type="integer")
	*/
    public $id;
    /**
	* @SWG\Property(name="nom",type="string")
	*/
    public $nom;
    /**
	* @SWG\Property(name="parentID",type="integer")
	*/
    public $parentID;
	
	function __construct($id,$nom,$parentID=0){
		$this->id = $id;
		$this->nom = $nom;
		$this->parentID = $parentID;
	}
}

?>