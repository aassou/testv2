<?php
    require('../app/classLoad.php'); 
    require('../db/PDOFactory.php');  
    include('../lib/image-processing.php');
    
    session_start();
    
    //post input processing
    $action = htmlentities($_POST['action']);
    //This var contains result message of CRUD action
    $actionMessage = "";
    $typeMessage = "";
    //class managers
    $terrainManager = new TerrainManager(PDOFactory::getMysqlConnection());
    $projetManager = new ProjetManager(PDOFactory::getMysqlConnection());
    //The History Component is used in all ActionControllers to mention a historical version of each action
    $historyManager = new HistoryManager(PDOFactory::getMysqlConnection());
    //get form inputs
    $idProjet = htmlentities($_POST['idProjet']);
    $nomProjet = $projetManager->getProjetById($idProjet)->nom();
    
    //Action Add Processing Begin
    if ( $action == "add" ) {
        if ( !empty($_POST['prix']) ) {
            $vendeur = htmlentities($_POST['vendeur']);    
            $emplacement = htmlentities($_POST['emplacement']);
            $superficie = htmlentities($_POST['superficie']);
            $prix = htmlentities($_POST['prix']);
            $fraisAchat = htmlentities($_POST['fraisAchat']);
            $createdBy = $_SESSION['userImmoERPV2']->login();
            $created = date('Y-m-d h:i:s');
            
            $terrain = new Terrain(array('vendeur' => $vendeur, 'prix' => $prix,'superficie' => $superficie, 
            'fraisAchat' =>$fraisAchat, 'emplacement' => $emplacement, 'idProjet' => $idProjet, 
            'created' => $created, 'createdBy' => $createdBy));
            $terrainManager->add($terrain);
            //Add To History Table
            $history = new History(array(
                'action' => "Ajout",
                'target' => "Table des terrains",
                'description' => "Ajout du terrain: ".$emplacement." - Projet : ".$nomProjet,
                'created' => $created,
                'createdBy' => $createdBy
            ));
            //add it to db
            $historyManager->add($history);
            $actionMessage = "Opération Valide : Terrain Ajouté avec succès.";  
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Ajout Terrain : Vous devez remplir le champ <strong>Prix</strong>.";
            $typeMessage = "error";
        }
    }
    //Action Add Processing End
    
    //Action Update Processing Begin
    else if($action == "update"){
        if(!empty($_POST['prix'])){
            $id = htmlentities($_POST['idTerrain']);
            $vendeur = htmlentities($_POST['vendeur']);    
            $emplacement = htmlentities($_POST['emplacement']);
            $superficie = htmlentities($_POST['superficie']);
            $prix = htmlentities($_POST['prix']);
            $fraisAchat = htmlentities($_POST['fraisAchat']);
            $updatedBy = $_SESSION['userImmoERPV2']->login();
            $updated = date('Y-m-d h:i:s');
            
            $terrain = new Terrain(array('id' => $id, 'vendeur' => $vendeur, 'prix' => $prix,'superficie' => $superficie, 
            'fraisAchat' =>$fraisAchat, 'emplacement' => $emplacement, 'idProjet' => $idProjet, 
            'updated' => $updated, 'updatedBy' => $updatedBy));
            $terrainManager->update($terrain);
            //Add To History Table
            $history = new History(array(
                'action' => "Modification",
                'target' => "Table des terrains",
                'description' => "Modification du terrain: ".$emplacement." - Projet : ".$nomProjet,
                'created' => $updated,
                'createdBy' => $updatedBy
            ));
            //add it to db
            $historyManager->add($history);
            $actionMessage = "Opération Valide : Terrain Modifié avec succès.";  
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Modification Terrain : Vous devez remplir le champ <strong>Prix</strong>.";
            $typeMessage = "error";
        }
    }
    //Action Update Processing End
    
    //Action Delete Processing Begin
    else if($action=="delete"){
        $idTerrain = $_POST['idTerrain'];
        $emplacementTerrain = $terrainManager->getTerrainById($idTerrain)->emplacement();
        $terrainManager->delete($idTerrain);
        //add history data to db
        $createdBy = $_SESSION['userImmoERPV2']->login();
        $created = date('Y-m-d h:i:s');
        $history = new History(array(
            'action' => "Suppression",
            'target' => "Table des terrain",
            'description' => "Suppression du terrain ".$emplacementTerrain." - Projet : ".$nomProjet,
            'created' => $created,
            'createdBy' => $createdBy
        ));
        //add it to db
        $historyManager->add($history);
        $actionMessage = "Opération Valide : Terrain Supprimé avec succès.";
        $typeMessage = "success";
    }
    //Action Delete Processing End
    
    //set session informations
    $_SESSION['terrain-action-message'] = $actionMessage;
    $_SESSION['terrain-type-message'] = $typeMessage;
    //set redirection link
    header('Location:../terrain.php?idProjet='.$idProjet);
    