<?php
class Company{

	//attributes
	private $_id;
	private $_nom;
	private $_adresse;
	private $_nomArabe;
	private $_adresseArabe;
	private $_directeur;
	private $_created;
	private $_createdBy;
	private $_updated;
	private $_updatedBy;

	//le constructeur
    public function __construct($data){
        $this->hydrate($data);
    }
    
    //la focntion hydrate sert à attribuer les valeurs en utilisant les setters d\'une façon dynamique!
    public function hydrate($data){
        foreach ($data as $key => $value){
            $method = 'set'.ucfirst($key);
            
            if (method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }

	//setters
	public function setId($id){
    	$this->_id = $id;
    }
	public function setNom($nom){
		$this->_nom = $nom;
   	}

	public function setAdresse($adresse){
		$this->_adresse = $adresse;
   	}

	public function setNomArabe($nomArabe){
		$this->_nomArabe = $nomArabe;
   	}

	public function setAdresseArabe($adresseArabe){
		$this->_adresseArabe = $adresseArabe;
   	}

	public function setDirecteur($directeur){
		$this->_directeur = $directeur;
   	}

	public function setCreated($created){
        $this->_created = $created;
    }

	public function setCreatedBy($createdBy){
        $this->_createdBy = $createdBy;
    }

	public function setUpdated($updated){
        $this->_updated = $updated;
    }

	public function setUpdatedBy($updatedBy){
        $this->_updatedBy = $updatedBy;
    }

	//getters
	public function id(){
    	return $this->_id;
    }
	public function nom(){
		return $this->_nom;
   	}

	public function adresse(){
		return $this->_adresse;
   	}

	public function nomArabe(){
		return $this->_nomArabe;
   	}

	public function adresseArabe(){
		return $this->_adresseArabe;
   	}

	public function directeur(){
		return $this->_directeur;
   	}

	public function created(){
        return $this->_created;
    }

	public function createdBy(){
        return $this->_createdBy;
    }

	public function updated(){
        return $this->_updated;
    }

	public function updatedBy(){
        return $this->_updatedBy;
    }

}