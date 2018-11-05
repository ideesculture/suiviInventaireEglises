<?php $eglise_id=$this->getVar("eglise_id"); ?>

<h1>Demande de validation de l'inventaire</h1>
<p>Êtes-vous sûr de demander de la validation de l'inventaire de l'église <?php print $eglise_id; ?> ?</p>
<p>Une fois celle-ci transmise, </p>
<ul style="margin-bottom: 60px;">
    <li>un email sera transmis à la coordination,</li>
    <li>une date de demande de validation sera enregistrée dans la fiche de l'église</li>
    <li>après réception de votre demande, la coordination du CIPAR reviendra vers vous pour vous donner plus d'informations</li>
</ul>

<div class="control-box rounded">
    <div class="control-box-left-content">
        <a href="/gestion/index.php/suiviInventaireEglises/Statistics/DemandeValidation/ID/<?php print $eglise_id; ?>" class="form-button 1541425903">
            <span class="form-button"><img src="/gestion/themes/default/graphics/buttons/glyphicons_198_ok.png" border="0" class="form-button-left" style="padding-right: 10px">Valider</span></a>
        <div style="position: absolute; top: 0px; left:-5000px;"><input type="submit"></div>  <a href="<?php print __CA_URL_ROOT__; ?>" class="form-button"><span class="form-button "><img src="/gestion/themes/default/graphics/buttons/glyphicons_445_floppy_remove.png" alt="Annuler" title="Annuler" border="0" class="cancelIcon" style="padding-right: 10px;">Annuler</span></a></div>
    <div class="control-box-right-content"></div>
    <div class="control-box-middle-content"></div>
</div>