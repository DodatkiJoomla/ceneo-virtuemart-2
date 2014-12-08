<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class ceneoCategory
{
    public $id;
    public $name;
    public $parent_id;
    public $link;
	public $lastCategory;

    function __construct($id, $name, $link, $parent_id = NULL)
    {
        $this->id = (int)$id;
        $this->name = (string)$name;
        $this->parent_id = (int)$parent_id;
        $this->link = (string)$link;
		$this->lastCategory = 0;
    }
	
	public function isLast()
	{
		$this->lastCategory = 1;
    }	
}
