<?php
    require('../app/classLoad.php'); 
    require('../db/PDOFactory.php');;  
    //classes loading end
    session_start();
    if(isset($_SESSION['userImmoERPV2'])){
        //classes managers
        $companyManager          = new CompanyManager(PDOFactory::getMysqlConnection());
        $releveBancaireManager   = new ReleveBancaireManager(PDOFactory::getMysqlConnection());
        $chargesCommunsManager   = new ChargeCommunManager(PDOFactory::getMysqlConnection());
        $typeChargeCommunManager = new TypeChargeCommunManager(PDOFactory::getMysqlConnection());
        $typeChargeProjetManager = new TypeChargeManager(PDOFactory::getMysqlConnection());
        $projetManager           = new ProjetManager(PDOFactory::getMysqlConnection());
        $compteBancaireManager   = new CompteBancaireManager(PDOFactory::getMysqlConnection());
        //obj and vars
        $companyID          = $_GET['companyID'];
        $company            = $companyManager->getCompanyById($companyID);
        $typeChargesCommuns = $typeChargeCommunManager->getTypeCharges();
        $typeChargesProjets = $typeChargeProjetManager->getTypeCharges();
        $projets            = $projetManager->getProjets();
        $releveBancaires    = $releveBancaireManager->getReleveBancaires();
        $comptesBancaires   = $compteBancaireManager->getCompteBancaires();
        $debit              = $releveBancaireManager->getTotalDebit();
        $credit             = $releveBancaireManager->getTotalCredit();
        $solde              = $credit - $debit;
        
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="UTF-8" />
    <title>ImmoERP - Management Application</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/metro.css" rel="stylesheet" />
    <link href="assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" />
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/style_responsive.css" rel="stylesheet" />
    <link href="assets/css/style_default.css" rel="stylesheet" id="style_color" />
    <link href="assets/fancybox/source/jquery.fancybox.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.default.css" />
    <link rel="stylesheet" type="text/css" href="assets/chosen-bootstrap/chosen/chosen.css" />
    <link rel="stylesheet" href="assets/data-tables/DT_bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="assets/uniform/css/uniform.default.css" />
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <?php 
        include("include/top-menu.php"); 
        $alerts = $alertManager->getAlerts();
        ?>   
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->    
    <div class="page-container row-fluid sidebar-closed">
        <!-- BEGIN SIDEBAR -->
        <?php include("include/sidebar.php"); ?>
        <!-- END SIDEBAR -->
        <!-- BEGIN PAGE -->
        <div class="page-content">
            <!-- BEGIN PAGE CONTAINER-->
            <div class="container-fluid">
                <!-- BEGIN PAGE HEADER-->
                <div class="row-fluid">
                    <div class="span12">
                        <!-- BEGIN PAGE TITLE & BREADCRUMB-->           
                        <h3 class="page-title">
                            Gestion des Relevés Bancaires 
                        </h3>
                        <ul class="breadcrumb">
                            <li>
                                <i class="icon-home"></i>
                                <a href="company-choice.php">Accueil</a>
                                <i class="icon-angle-right"></i>
                            </li>
                            <li>
                                <i class="icon-sitemap"></i>
                                <a href="company-dashboard.php?companyID=<?= $companyID ?>">Société <?= $company->nom() ?></a>
                                <i class="icon-angle-right"></i>
                            </li>
                            <li>
                                <i class="icon-envelope"></i>
                                <a><strong>Gestion des Relevés Bancaires</strong></a>
                            </li>
                        </ul>
                        <!-- END PAGE TITLE & BREADCRUMB-->
                    </div>
                </div>
                <!-- END PAGE HEADER-->
                <!-- BEGIN PAGE CONTENT-->
                <!-- BEGIN PORTLET-->
                <div class="row-fluid">
                    <div class="span12">
                        <?php
                         if( isset($_SESSION['releveBancaire-action-message'])
                         and isset($_SESSION['releveBancaire-type-message']) ){ 
                            $message = $_SESSION['releveBancaire-action-message'];
                            $typeMessage = $_SESSION['releveBancaire-type-message'];    
                         ?>
                            <div class="alert alert-<?= $typeMessage ?>">
                                <button class="close" data-dismiss="alert"></button>
                                <?= $message ?>     
                            </div>
                         <?php } 
                            unset($_SESSION['releveBancaire-action-message']);
                            unset($_SESSION['releveBancaire-type-message']);
                         ?>
                        <div class="portlet">
                            <div class="portlet-title line">
                                <h4><i class="icon-envelope"></i>Ajouter un relevé bancaire</h4>
                                <!--div class="tools">
                                    <a href="javascript:;" class="collapse"></a>
                                    <a href="javascript:;" class="remove"></a>
                                </div-->
                            </div>
                            <div class="portlet-body" id="chats">
                                <form action="../controller/ReleveBancaireActionController.php" method="POST" enctype="multipart/form-data">
                                    <div class="control-group">
                                        <label class="control-label">Compte bancaire</label>
                                        <div class="controls">
                                            <select name="idCompteBancaire" class="m-wrap" >
                                                <?php foreach($comptesBancaires as $compte){ ?>
                                                <option value="<?= $compte->id() ?>"><?= $compte->numero() ?></option>
                                                <?php } ?>    
                                            </select>    
                                         </div>
                                    </div>
                                    <div class="control-group">   
                                        <input class="m-wrap" type="file" name="excelupload" />
                                    </div>
                                    <div class="btn-cont"> 
                                        <input type="hidden" name="action" value="add" />
                                        <button type="submit" class="btn blue icn-only"><i class="icon-save icon-white"></i>&nbsp;Enregistrer</button>
                                    </div>
                                </form>
                                <a href="#deleteReleveActuel" data-toggle="modal" class="btn red pull-right get-down"><i class="icon-trash"></i>&nbsp;Supprimer le relevé actuel</a>
                                <!-- deleteReleveActuel box begin-->
                                <div id="deleteReleveActuel" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                        <h3>Supprimer Relevé Actuel</h3>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal loginFrm" action="../controller/ReleveBancaireActionController.php" method="post">
                                            <p>Êtes-vous sûr de vouloir supprimer ce relevé actuel ?</p>
                                            <div class="control-group">
                                                <label class="right-label"></label>
                                                <input type="hidden" name="action" value="deleteReleveActuel" />
                                                <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- deleteReleveActuel box end -->     
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <!-- BEGIN RELEVE PORTLET-->               
                        <div class="portlet box light-grey">
                            <div class="portlet-title">
                                <h4>Les Relevés Bancaires</h4>
                                <div class="tools">
                                    <a href="javascript:;" class="reload"></a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="clearfix">
                                    <!--div class="btn-group pull-right">
                                        <button class="btn dropdown-toggle" data-toggle="dropdown">Outils <i class="icon-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Print</a></li>
                                            <li><a href="#">Save as PDF</a></li>
                                            <li><a href="#">Export to Excel</a></li>
                                        </ul>
                                    </div-->
                                </div>
                                <table class="table table-striped table-bordered table-hover" id="sample_1">
                                    <thead>
                                        <tr>
                                            <th style="width:10%;">Actions</th>
                                            <th style="width:10%;">DateOpe</th>
                                            <th style="width:10%;">DateVal</th>
                                            <th style="width:20%;">Libelle</th>
                                            <th style="width:10%;">Reference</th>
                                            <th style="width:15%;">Débit</th>
                                            <th style="width:15%;">Crédit</th>
                                            <th style="width:10%;">Projet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach($releveBancaires as $releve){
                                            $numeroCompte = $compteBancaireManager->getCompteBancaireById($releve->idCompteBancaire())->numero();
                                        ?>
                                        <tr class="odd gradeX">
                                            <td>
                                                <?php
                                                if ( $_SESSION['userImmoERPV2']->profil() == "admin" ) {
                                                ?>
                                                    <a href="#update<?= $releve->id() ?>" data-toggle="modal" data-id="<?= $releve->id() ?>" class="btn mini green"><i class="icon-refresh"></i></a>
                                                    <a href="#delete<?= $releve->id() ?>" data-toggle="modal" data-id="<?= $releve->id() ?>" class="btn mini red"><i class="icon-remove"></i></a>
                                                <?php  
                                                    //In this section we will process credit and debit element.
                                                    //The debit element will be added for fournisseur component
                                                    //The credit element will be added for client component
                                                    if ( $releve->debit() > 0 ) {
                                                ?>
                                                        <a title="Opérations Fournisseurs" href="#processFournisseur<?= $releve->id() ?>" data-toggle="modal" data-id="<?= $releve->id() ?>" class="btn mini blue"><i class="icon-cogs"></i></a>
                                                <?php
                                                    }
                                                    else if ( $releve->credit() > 0 ) {
                                                ?>
                                                        <a title="Opérations Client" href="#processClient<?= $releve->id() ?>" data-toggle="modal" data-id="<?= $releve->id() ?>" class="btn mini purple"><i class="icon-cogs"></i></a>
                                                <?php        
                                                    }
                                                }
                                                ?>
                                            </td>    
                                            <!--td><?php //date('d/m/Y', strtotime($releve->dateOpe())) ?></td-->
                                            <!--td><?php //date('d/m/Y', strtotime($releve->dateVal())) ?></td-->
                                            <td><?= $releve->dateOpe() ?></td>
                                            <td><?= $releve->dateVal() ?></td>
                                            <td><?= $releve->libelle() ?></td>
                                            <td><?= $releve->reference() ?></td>
                                            <td><?= number_format($releve->debit(), 2, ',', ' ' ) ?></td>
                                            <td><?= number_format($releve->credit(), 2, ',', ' ') ?></td>
                                            <td><?= $releve->projet() ?></td>
                                        </tr>
                                        <!-- updateReleve box begin-->
                                        <div id="update<?= $releve->id() ?>" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h3>Modifier les informations du relevé </h3>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" action="../controller/ReleveBancaireActionController.php" method="post">
                                                    <div class="control-group">
                                                        <label class="control-label">Compte bancaire</label>
                                                        <div class="controls">
                                                            <select name="idCompteBancaire" class="m-wrap" >
                                                                <option value="<?= $releve->idCompteBancaire() ?>"><?= $numeroCompte ?></option>
                                                                <option disabled="disabled">----------------------</option>
                                                                <?php foreach($comptesBancaires as $compte){ ?>
                                                                <option value="<?= $compte->id() ?>"><?= $compte->numero() ?></option>
                                                                <?php } ?>    
                                                            </select>    
                                                         </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">DateOpe</label>
                                                        <div class="controls date date-picker" data-date="" data-date-format="yyyy-mm-dd">
                                                            <input name="dateOpe" id="dateOpe" class="m-wrap m-ctrl-small date-picker" type="text" value="<?= $releve->dateOpe() ?>" />
                                                            <span class="add-on"><i class="icon-calendar"></i></span>
                                                         </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">DateVal</label>
                                                        <div class="controls date date-picker" data-date="" data-date-format="yyyy-mm-dd">
                                                            <input name="dateVal" id="dateVal" class="m-wrap m-ctrl-small date-picker" type="text" value="<?= $releve->dateVal() ?>" />
                                                            <span class="add-on"><i class="icon-calendar"></i></span>
                                                         </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Libelle</label>
                                                        <div class="controls">
                                                            <textarea class="textarea-width" rows="3" name="libelle"><?= $releve->libelle() ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Reference</label>
                                                        <div class="controls">
                                                            <input type="text" name="reference" value="<?= $releve->reference() ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Débit</label>
                                                        <div class="controls">
                                                            <input type="text" name="debit" value="<?= $releve->debit() ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Crédit</label>
                                                        <div class="controls">
                                                            <input type="text" name="credit" value="<?= $releve->credit() ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Projet</label>
                                                        <div class="controls">
                                                            <input type="text" name="projet" value="<?= $releve->projet() ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <input type="hidden" name="idReleveBancaire" value="<?= $releve->id() ?>" />
                                                        <input type="hidden" name="action" value="update" />
                                                        <div class="controls">  
                                                            <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                            <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- updateReleve box end -->
                                        <!-- processFournisseur box begin-->
                                        <div id="processFournisseur<?= $releve->id() ?>" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h3>Affecter opération débit au système</h3>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" action="../controller/ReleveBancaireActionController.php" method="post">
                                                    <div class="control-group">
                                                        <label class="control-label">Destination</label>
                                                        <div class="controls">
                                                            <select name="destinations" class="destinations">
                                                                <option value="ChargesCommuns">Charges communs</option>
                                                                <option value="ChargesProjets">Charges Projets</option>
                                                                <option value="Ignorer">Ignorer</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="chargesCommunsElements">
                                                        <div class="control-group">
                                                            <label class="control-label">Type Charge Commun</label>
                                                            <div class="controls">
                                                                <select name="typeChargesCommuns">
                                                                    <?php foreach( $typeChargesCommuns as $type ) { ?>    
                                                                    <option value="<?= $type->id() ?>"><?= $type->nom() ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="control-group">
                                                            <label class="control-label">Société</label>
                                                            <div class="controls">
                                                                <input type="text" name="societe" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="chargesProjetsElements" style="">
                                                        <div class="control-group">
                                                            <label class="control-label">Type Charge Projet</label>
                                                            <div class="controls">
                                                                <select name="typeChargesProjet">
                                                                    <?php foreach( $typeChargesProjets as $type ) { ?>    
                                                                    <option value="<?= $type->id() ?>"><?= $type->nom() ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="control-group">
                                                            <label class="control-label">Projet</label>
                                                            <div class="controls">
                                                                <select name="projet">
                                                                    <?php foreach( $projets as $projet ) { ?>    
                                                                    <option value="<?= $projet->id() ?>"><?= $projet->nom() ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="control-group">
                                                            <label class="control-label">Société</label>
                                                            <div class="controls">
                                                                <input type="text" name="societe2" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <input type="hidden" name="idReleveBancaire" value="<?= $releve->id() ?>" />
                                                        <input type="hidden" name="montant" value="<?= $releve->debit() ?>" />
                                                        <input type="hidden" name="dateOperation" value="<?= $releve->dateOpe() ?>" />
                                                        <input type="hidden" name="designation" value="<?= $releve->libelle() ?>" />
                                                        <input type="hidden" name="action" value="process-fournisseur" />
                                                        <div class="controls">  
                                                            <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                            <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- processFournisseur box end -->
                                        <!-- processClient box begin-->
                                        <div id="processClient<?= $releve->id() ?>" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h3>Affecter opération crédit au système</h3>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" action="../controller/ReleveBancaireActionController.php" method="post">
                                                    <div class="control-group">
                                                        <label class="control-label">Action</label>
                                                        <div class="controls">
                                                            <select name="projet-contrat" class="projet-contrat span12">
                                                                <option value="Ignorer">Séléctionnez un projet ou Ignorer ?</option>
                                                                <?php foreach( $projets as $projet ) { ?>    
                                                                <option value="<?= $projet->id() ?>"><?= $projet->nom() ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Contrat client</label>
                                                        <div class="controls">
                                                            <select name="contrat-client" class="contrat-client span12">
                                                                <option value="">Séléctionnez un contrat ou Ignorer ?</option>
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control-group">
                                                        <label class="control-label">Mode Paiement</label>
                                                        <div class="controls">
                                                            <select name="mode-paiement" class="span12">   
                                                                <option value="Especes">Espèces</option>
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="Versement">Versement</option>
                                                                <option value="Virement">Virement</option>
                                                                <option value="Lettre de change">Lettre de change</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <strong>Synthèse client</strong>
                                                    <br />
                                                    <div class="tab-pane ">
                                                        <div class="controls controls-row">
                                                            <input disabled="disabled" class="span2 m-wrap input-bold-text" type="text" value="DateOpé" />
                                                            <input disabled="disabled" class="span2 m-wrap input-bold-text" type="text" value="DateRég" />
                                                            <input disabled="disabled" class="span4 m-wrap input-bold-text" type="text" value="Montant" />
                                                            <input disabled="disabled" class="span2 m-wrap input-bold-text" type="text" value="Compte" />
                                                            <input disabled="disabled" class="span2 m-wrap input-bold-text" type="text" value="Chèque" />
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane synthese-client">
                                                    </div>
                                                    <div class="control-group">
                                                        <input type="hidden" name="idReleveBancaire" value="<?= $releve->id() ?>" />
                                                        <input type="hidden" name="montant" value="<?= $releve->credit() ?>" />
                                                        <input type="hidden" name="compte-bancaire" value="<?= $numeroCompte ?>" />
                                                        <input type="hidden" name="dateOperation" value="<?= $releve->dateOpe() ?>" />
                                                        <input type="hidden" name="dateReglement" value="<?= $releve->dateVal() ?>" />
                                                        <input type="hidden" name="observation" value="<?= $releve->libelle() ?>" />
                                                        <input type="hidden" name="reference" value="<?= $releve->reference() ?>" />
                                                        <input type="hidden" name="action" value="process-client" />
                                                        <div class="controls">  
                                                            <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                            <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- processClient box end -->
                                        <!-- delete box begin-->
                                        <div id="delete<?= $releve->id();?>" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="false" >
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h3>Supprimer Relevé</h3>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal loginFrm" action="../controller/ReleveBancaireActionController.php" method="post">
                                                    <div class="control-group">
                                                        <label class="right-label"></label>
                                                        <input type="hidden" name="idReleveBancaire" value="<?= $releve->id() ?>" />
                                                        <input type="hidden" name="action" value="delete" />
                                                        <button class="btn" data-dismiss="modal"aria-hidden="true">Non</button>
                                                        <button type="submit" class="btn red" aria-hidden="true">Oui</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- delete box end -->     
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <tbody>
                                        <tr>
                                            <th style="width:60%;">Total Débit</th>
                                            <th style="width:20%"><a><?= number_format($debit, '2', ',', ' ') ?></a>&nbsp;DH</th>
                                            <th style="width:20%;"></th>
                                        </tr>
                                        <tr>
                                            <th style="width:60%;">Total Crédit</th>
                                            <th style="width:20%;"></th>
                                            <th style="width:20%"><a><?= number_format($credit, '2', ',', ' ') ?></a>&nbsp;DH</th>
                                        </tr>
                                        <tr>
                                            <th style="width:60%;">Solde</th>
                                            <th style="width:20%"><a><?= number_format($solde, '2', ',', ' ') ?></a>&nbsp;DH</th>
                                            <th style="width:20%;"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>             
                        <!-- END RELEVE PORTLET-->
                    </div>
                </div>
                <!-- END PORTLET-->
                <!-- END PAGE CONTENT-->
            </div>
            <!-- END PAGE CONTAINER-->  
        </div>
        <!-- END PAGE -->       
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="footer">
        2015 &copy; ImmoERP. Management Application.
        <div class="span pull-right">
            <span class="go-top"><i class="icon-angle-up"></i></span>
        </div>
    </div>
    <!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS -->
    <!-- Load javascripts at bottom, this will reduce page load time -->
    <script src="assets/js/jquery-1.8.3.min.js"></script>   
    <script src="assets/breakpoints/breakpoints.js"></script>   
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>        
    <script src="assets/js/jquery.blockui.js"></script>
    <script src="assets/js/jquery.cookie.js"></script>
    <!-- ie8 fixes -->
    <!--[if lt IE 9]>
    <script src="assets/js/excanvas.js"></script>
    <script src="assets/js/respond.js"></script>
    <![endif]-->    
    <script type="text/javascript" src="assets/uniform/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="assets/data-tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="assets/data-tables/DT_bootstrap.js"></script>
    <script src="assets/js/app.js"></script>        
    <script>
        jQuery(document).ready(function() {         
            // initiate layout and plugins
            App.setPage("table_managed");
            App.init();
        });
        //processFournisseur begin
        $(".chargesProjetsElements").hide();
        $('.destinations').on('change',function(){
            if ( $(this).val() === "ChargesCommuns" ) {
                $(".chargesCommunsElements").show();
                $(".chargesProjetsElements").hide();
            }
            else if ( $(this).val() === "ChargesProjets" ) {
                $(".chargesProjetsElements").show();
                $(".chargesCommunsElements").hide();
            }
            else {
                $(".chargesCommunsElements").hide();
                $(".chargesProjetsElements").hide();    
            }
            
        }); 
        //processFournisseur end
        //processClient begin
        $('.projet-contrat').change(function(){
            var idProjet = $(this).val();
            var data = 'idProjet='+idProjet;
            $.ajax({
                type: "POST",
                url: "projets-contrats.php",
                data: data,
                cache: false,
                success: function(html){
                    $('.contrat-client').html(html);
                }
            });
        });
        //synthese client
        $('.contrat-client').change(function(){
            var idContrat = $(this).val();
            var data = 'idContrat='+idContrat;
            $.ajax({
                type: "POST",
                url: "synthese-client.php",
                data: data,
                cache: false,
                success: function(html){
                    $('.synthese-client').html(html);
                }
            });
        });
        //processClient end
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
<?php
}
else{
    header('Location:index.php');    
}
?>