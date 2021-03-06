<?php
    require('../app/classLoad.php');
    require('../db/PDOFactory.php');  
    
    session_start();
    
    //post input processing
    $action = htmlentities($_POST['action']);
    //This var contains result message of CRUD action
    $actionMessage = "";
    $typeMessage = "";

    //Component Class Manager
    //The History Component is used in all ActionControllers to mention a historical version of each action
    $historyManager = new HistoryManager(PDOFactory::getMysqlConnection());
    $taskManager = new TaskManager(PDOFactory::getMysqlConnection());
	
	//Action Add Processing Begin
    if($action == "add"){
        if( !empty($_POST['user']) ){
			$user = htmlentities($_POST['user']);
			$content = htmlentities($_POST['content']);
			$status = htmlentities($_POST['status']);
			$createdBy = $_SESSION['userImmoERPV2']->login();
            $created = date('Y-m-d h:i:s');
            //create object
            $task = new Task(array(
				'user' => $user,
				'content' => $content,
				'status' => 0,
				'created' => $created,
            	'createdBy' => $createdBy
			));
            //add it to db
            $taskManager->add($task);
            //add History data
            $history = new History(array(
                'action' => "Ajout",
                'target' => "Table des tâches",
                'description' => "Ajouter une nouvelle tâche",
                'created' => $created,
                'createdBy' => $createdBy
            ));
            //add it to db
            $historyManager->add($history);
            $actionMessage = "Opération Valide : Tâche Ajouté(e) avec succès.";  
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Ajout Tâche : Vous devez remplir le champ 'Description'.";
            $typeMessage = "error";
        }
    }
    //Action Add Processing End
    
    //Action Update Processing Begin
    else if($action == "update"){
        $idTask = htmlentities($_POST['idTask']);
        if(!empty($_POST['user'])){
			$user = htmlentities($_POST['user']);
			$content = htmlentities($_POST['content']);
			$status = htmlentities($_POST['status']);
			$updatedBy = $_SESSION['userImmoERPV2']->login();
            $updated = date('Y-m-d h:i:s');
            $task = new Task(array(
				'id' => $idTask,
				'user' => $user,
				'content' => $content,
				'status' => $status,
				'updated' => $updated,
            	'updatedBy' => $updatedBy
			));
            $taskManager->update($task);
            //add History data
            $createdBy = $_SESSION['userImmoERPV2']->login();
            $created = date('Y-m-d h:i:s');
            $history = new History(array(
                'action' => "Modification",
                'target' => "Table des tâches",
                'description' => "Modifier une tâche",
                'created' => $created,
                'createdBy' => $createdBy
            ));
            //add it to db
            $historyManager->add($history);
            $actionMessage = "Opération Valide : Tâche Modifié(e) avec succès.";
            $typeMessage = "success";
        }
        else{
            $actionMessage = "Erreur Modification Tâche : Vous devez remplir le champ 'Desription'.";
            $typeMessage = "error";
        }
    }
    //Action Update Processing End
    
    //Action UpdateStatus Processing Begin
    else if($action == "updateStatus"){
        $idTask = htmlentities($_POST['idTask']);
        $status = htmlentities($_POST['status']);
        $updatedBy = $_SESSION['userImmoERPV2']->login();
        $updated = date('Y-m-d h:i:s');
        $task = new Task(array(
            'id' => $idTask,
            'status' => $status,
            'updated' => $updated,
            'updatedBy' => $updatedBy
        ));
        $taskManager->updateStatus($task);
        //add History data
        $createdBy = $_SESSION['userImmoERPV2']->login();
        $created = date('Y-m-d h:i:s');
        $history = new History(array(
            'action' => "Modification",
            'target' => "Table des tâches",
            'description' => "Modifier le status d'une tâche",
            'created' => $created,
            'createdBy' => $createdBy
        ));
        //add it to db
        $historyManager->add($history);
        $actionMessage = "Opération Valide : Tâche Status Modifié(e) avec succès.";
        $typeMessage = "success";
    }
    //Action UpdateStatus Processing End
    
    //Action Delete Processing Begin
    else if($action == "delete"){
        $idTask = htmlentities($_POST['idTask']);
        $taskManager->delete($idTask);
        //add History data
        $createdBy = $_SESSION['userImmoERPV2']->login();
        $created = date('Y-m-d h:i:s');
        $history = new History(array(
            'action' => "Suppression",
            'target' => "Table des tâches",
            'description' => "Supprimer une tâche",
            'created' => $created,
            'createdBy' => $createdBy
        ));
        //add it to db
        $historyManager->add($history);
        $actionMessage = "Opération Valide : Tâche Supprimé(e) avec succès.";
        $typeMessage = "success";
    }
    //Action Delete Processing End
    
    //Action Delete List Of Tasks Processing Begin
    else if($action == "deleteValideTasks"){
        $user = htmlentities($_POST['user']);
        $taskManager->deleteValideTasksByUser($user);
        //add History data
        $createdBy = $_SESSION['userImmoERPV2']->login();
        $created = date('Y-m-d h:i:s');
        $history = new History(array(
            'action' => "Suppression",
            'target' => "Table des tâches",
            'description' => "Supprimer les tâches validées",
            'created' => $created,
            'createdBy' => $createdBy
        ));
        //add it to db
        $historyManager->add($history);
        $actionMessage = "Opération Valide : Tâches Supprimées avec succès.";
        $typeMessage = "success";
    }
    //Action Delete List Of Tasks Processing End
    
    //set session informations
    $_SESSION['task-action-message'] = $actionMessage;
    $_SESSION['task-type-message'] = $typeMessage;
    
    //set redirection link
    header('Location:../views/tasks.php');

