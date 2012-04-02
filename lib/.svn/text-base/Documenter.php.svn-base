<?php
/**
  This class extends ReflectionClass and is used to document classes.
  Taken from code accompanying "Object-Oreinted PHP: Concepts, 
  Techniques, and Code" by Peter Lavin.  No Starch Press, 2006.

  modified by Peter Keane for daseproject.org

 */
class Documenter extends ReflectionClass
{
	private $_publicmethods        = array();
	private $_protectedmethods     = array();
	private $_privatemethods       = array();
	private $_publicdatamembers    = array();
	private $_protecteddatamembers = array();
	private $_privatedatamembers   = array();

	/**
	  Call methods to set up arrays after constructing parent.
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->createDataMemberArrays();
		$this->createMethodArrays();
	}

	function __get($var) 
	{
		//allows smarty to invoke function as if getter
		$classname = get_class($this);
		$method = 'get'.ucfirst($var);
		if (method_exists($classname,$method)) {
			return $this->{$method}();
		//} else {
		//	return parent::__get($var);
		}
	}


	/** 
	  Returns whether class is internal or user-defined. 
	 */  
	public function getClassType()
	{
		if($this->isInternal()){
			$type = "Internal";
		}else{
			$type = "User-defined";
		}
		return $type;
	}

	/** 
	  Returns description of class and inheritance 
	 */

	public function getFullDescription()
	{
		$description = "";
		if ($this->isFinal()){
			$description = "final ";
		}
		if($this->isAbstract()){
			$description = "abstract ";
		}
		if($this->isInterface()){
			$description .= "interface ";
		}
		else{
			$description .= "class ";
		}
		$description .= $this->name . " ";
		if($this->getParentClass()){
			$name =  $this->getParentClass()->getName();
			$description .= "extends $name ";
		}
		$interfaces = $this->getInterfaces();
		$number = count($interfaces);
		if( $number > 0){
			$counter = 0;
			$description .= "implements ";
			foreach ($interfaces as $i){        
				$description .= $i->getName();
				$counter ++;
				if($counter != $number){
					$description .= ", ";
				}
			}    
		}

		return $description;
	}

	public function getPublicMethods()
	{
		return $this->_publicmethods;
	}

	public function getProtectedMethods()
	{
		return $this->_protectedmethods;
	}

	public function getPrivateMethods()
	{
		return $this->_privatemethods;
	}

	/**
	  Use the static method of the Reflection class
	  to get all modifiers - use with both properties 
	  and methods.
	 */

	public function getModifiers($r)
	{
		if ($r instanceof ReflectionMethod ||
			$r instanceof ReflectionProperty){
				$arr = Reflection::getModifierNames($r->getModifiers());
				$description = implode(" ", $arr );
			}else{
				$msg = "Must be ReflectionMethod or ReflectionProperty";
				throw new ReflectionException( $msg );
			}
		return $description;
	}

	public function getPublicDataMembers()
	{
		return $this->_publicdatamembers;
	}

	public function getPrivateDataMembers()
	{
		return $this->_privatedatamembers;
	}

	public function getProtectedDataMembers()
	{
		return $this->_protecteddatamembers;
	}

	//private methods

	/** create arrays of methods and set modifiers */
	private function createMethodArrays()
	{
		$methods = $this->getMethods();
		//returns ReflectionMethod array
		foreach ($methods as $m){      
			$name = $m->getName();
			if($m->isPublic()){      
				$this->_publicmethods[$name] = $m;
			}
			if($m->isProtected()){
				$this->_protectedmethods[$name] = $m;
			}
			if($m->isPrivate()){
				$this->_privatemethods[$name] = $m;
			}
		}
	}

	/** Set up data member arrays by type */
	private function createDataMemberArrays()
	{    
		//ReflectionProperty[] getProperties()
		$properties = $this->getProperties();
		foreach ($properties as $p){      
			$name = $p->getName();
			if($p->isPublic()){      
				$this->_publicdatamembers[$name] = $p;
			}
			if($p->isPrivate()){      
				$this->_privatedatamembers[$name] = $p;
			}
			if($p->isProtected()){      
				$this->_protecteddatamembers[$name] = $p;
			}
		}
	}
} 

