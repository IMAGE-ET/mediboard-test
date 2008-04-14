<script type="text/javascript">

// Calcul de la date de debut lors de la modification de la fin
syncDate = function(oForm, curr_line_id, fieldName) {
  // Déclaration des div des dates
  oDivDebut = $('editDates-'+curr_line_id+'_debut_da');
  oDivFin = $('editDates-'+curr_line_id+'__fin_da');

  // Recuperation de la date actuelle
  var todayDate = new Date();
  var dToday = todayDate.toDATE();
  
  // Recuperation des dates des formulaires
  var sDebut = oForm.debut.value;
  var sFin = oForm._fin.value;
  var nDuree = parseInt(oForm.duree.value, 10);
  var sType = oForm.unite_duree.value;
  
  // Transformation des dates
  if(sDebut){
    var dDebut = Date.fromDATE(sDebut);  
  }
  if(sFin){
    var dFin = Date.fromDATE(sFin);  
  }
  
  // Modification de la fin en fonction du debut
  if(fieldName != "_fin" && sDebut && sType && nDuree) {
    dFin = dDebut;
    if(sType == "jour")      { dFin.addDays(nDuree);     }
    if(sType == "semaine")   { dFin.addDays(nDuree*7);   }
    if(sType == "quinzaine") { dFin.addDays(nDuree*14);  }
    if(sType == "mois")      { dFin.addDays(nDuree*30);  }
    if(sType == "trimestre") { dFin.addDays(nDuree*90);  }
    if(sType == "semestre")  { dFin.addDays(nDuree*180); }
    if(sType == "an")        { dFin.addDays(nDuree*365); }

  	oForm._fin.value = dFin.toDATE();
  	oDivFin.innerHTML = dFin.toLocaleDate();
  }
  
  //-- Lors de la modification de la fin --
  // Si debut, on modifie la duree
  if(sDebut && sFin && fieldName == "_fin"){
    var nDuree = (dFin - dDebut)/86400000;
    oForm.duree.value = nDuree;
    oForm.unite_duree.value = "jour";
  }
  
  // Si !debut et duree, on modifie le debut
  if(!sDebut && nDuree && sType && fieldName == "_fin"){
    dDebut = dFin;
    if(sType == "jour")      { dDebut.addDays(-nDuree);     }
    if(sType == "semaine")   { dDebut.addDays(-nDuree*7);   }
    if(sType == "quinzaine") { dDebut.addDays(-nDuree*14);  }
    if(sType == "mois")      { dDebut.addDays(-nDuree*30);  }
    if(sType == "trimestre") { dDebut.addDays(-nDuree*90);  }
    if(sType == "semestre")  { dDebut.addDays(-nDuree*180); }
    if(sType == "an")        { dDebut.addDays(-nDuree*365); }

  	oForm.debut.value = dDebut.toDATE();
  	oDivDebut.innerHTML = dDebut.toLocaleDate();
  }
  
  // Si !debut et !duree, on met le debut a aujourd'hui, et on modifie la duree
  if(!sDebut && !nDuree && fieldName == "_fin"){
    dDebut = todayDate;
    oForm.debut.value = todayDate.toDATE();
    oDivDebut.innerHTML = todayDate.toLocaleDate();
    var nDuree = parseInt((dFin - dDebut)/86400000,10);
    oForm.duree.value = nDuree;
    oForm.unite_duree.value = "jour";
  }
}

syncDateSubmit = function(oForm, curr_line_id, fieldName) {
  syncDate(oForm, curr_line_id, fieldName);
  submitFormAjax(oForm, 'systemMsg');
}
  

selDivPoso = function(type, line_id){
  if(!type){
    type = "foisPar";
  }
  $('moment'+line_id).hide();
  $('foisPar'+line_id).hide();
  $('tousLes'+line_id).hide();
  $(type+line_id).show();
}

// Fonction lancée lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id){
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";
  submitFormAjax(oForm, 'systemMsg', { onComplete: 
    function(){
      // Preparation des prises pour la nouvelle posologie selectionnée
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_id", curr_line_id);
      url.addParam("no_poso", oForm.no_poso.value);
      url.addParam("code_cip", oForm._code_cip.value);
      url.requestUpdate('prises-'+curr_line_id, { waitingText: null });
    } 
   }
  );
}

reloadPrises = function(prescription_line_id){
  url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.requestUpdate('prises-'+prescription_line_id, { waitingText: null });
}

submitPrise = function(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete:
    function(){
      reloadPrises(oForm.prescription_line_id.value);
      oForm.quantite.value = 0;
      oForm.moment_unitaire_id.value = "";
  } });
}

{{assign var=nb_med value=$prescription->_ref_lines_med_comments.med|@count}}
{{assign var=nb_comment value=$prescription->_ref_lines_med_comments.comment|@count}}
{{assign var=nb_total value=$nb_med+$nb_comment}}

Prescription.refreshTabHeader("div_medicament","{{$nb_total}}");


// Permet de mettre la ligne en traitement
transfertTraitement = function(line_id){
  if(!line_id){
    return;
  }
  var oForm = document.transfertToTraitement;
  oForm.prescription_line_id.value = line_id;
  submitFormAjax(oForm, "systemMsg");
}


</script>

<form name="transfertToTraitement" action="?" method="post">
  <input type="hidden" name="dosql" value="do_prescription_traitement_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="prescription_line_id" value="" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  
  <input type="hidden" name="_traitement" value="1" />
  <input type="hidden" name="_type" value="{{$prescription->type}}" />
</form>


<!-- Cas normal -->
<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">  
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="prescription_line_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="code_cip" value=""/>
  
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  <input type="hidden" name="refresh_pharma" value="{{$refresh_pharma}}" />
  
  {{if $prescription->type=="pre_admission" && $prescription->object_id}}
  <input type="hidden" name="callback" value="transfertTraitement" />
  {{/if}}  
</form>

<!-- Ne pas donner la possibilite de signer les lignes d'un protocole -->
{{if $prescription->object_id}}
<div style="float: right">
  <form name="valideAllLines" method="post" action="">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
    <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    <input type="hidden" name="chapitre" value="medicament" />
    <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
    <button class="tick" type="button" onclick="submitFormAjax(this.form,'systemMsg')">
    {{if $mode_pharma}}
      Validation pharmacien pour toutes les lignes
    {{else}}
      Signer les lignes de médicaments
    {{/if}}
    </button>
  </form>
</div>
{{/if}}

<!-- Affichage des div des medicaments et autres produits -->
  <form action="?" method="get" name="searchProd" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';">
      <option value="">&mdash; Médicaments les plus utilisés</option>
      {{foreach from=$listFavoris.medicament item=curr_prod}}
      <option value="{{$curr_prod->code_cip}}">
        {{$curr_prod->libelle}}
      </option>
      {{/foreach}}
    </select>
    <button class="new" onclick="$('add_line_comment_med').show();">Ajouter une ligne de commentaire</button>
    
    <br />
	  <input type="text" name="produit" value=""/>
	  <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
	  <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
	  <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
	  <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
	  <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
	  {{if $prescription->type == "sejour"}}
	  <input type="hidden" name="_recherche_livret" value="1" />
	  {{else}}
	  <input type="hidden" name="_recherche_livret" value="0" />
	  {{/if}}
	  <script type="text/javascript">
		  MedSelector.init = function(onglet){
		    this.sForm = "searchProd";
		    this.sView = "produit";
		    this.sCode = "code_cip";
		    this.sRechercheLivret = document.searchProd._recherche_livret.value;
		    this.sSearch = document.searchProd.produit.value;
		    this.sOnglet = onglet;
		    this.selfClose = false;
		    this.pop();
		  }
	</script>
  </form>
  <br />
  <div id="add_line_comment_med" style="display: none">
   <button class="cancel notext" type="button" onclick="$('add_line_comment_med').hide();">Cacher</button>
   <form name="addLineCommentMed" method="post" action="">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
      <input type="hidden" name="chapitre" value="medicament" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">Ajouter</button>
    </form>
 </div> 

{{assign var=traitements value=$prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines}}
  
  
{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements}}
<table class="tbl">
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" prescription_reelle=$prescription}} 
  {{/foreach}}
  
  
  <!-- Parcours des commentaires --> 
 {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
   <tbody class="hoverable">
    <tr>
      <td>
        <form name="delLineCommentMed-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'medicament') } } );">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
      </td>
      <td>
        {{$_line_comment->commentaire}}
      </td>
      <td>
	      <form action="?" method="post" name="editLineCommentALD-{{$_line_comment->_id}}">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	        <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}"/>
	        <input type="hidden" name="del" value="0" />
	        {{mb_field object=$_line_comment field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	        {{mb_label object=$_line_comment field="ald" typeEnum="checkbox"}}
	      </form>
      </td>
      <td style="text-align: right">
        {{if $_line_comment->_ref_praticien->_id}}
        {{$_line_comment->_ref_praticien->_view}}
        {{if $prescription->object_id}}  
	        {{if $_line_comment->signee}}
	          {{if $_line_comment->_ref_praticien->_id == $app->user_id}}
	            <form name="delValidationComment-med-{{$_line_comment->_id}}" action="" method="post">
	              <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
	              <input type="hidden" name="signee" value="0" />
	              <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','medicament') } }  )">Annuler la validation</button>
	            </form>
	          {{/if}}
	        {{else}}
	          {{if $_line_comment->_ref_praticien->_id == $app->user_id}}
	            <form name="validationComment-{{$_line_comment->_id}}" action="" method="post">
	              <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
	              <input type="hidden" name="signee" value="1" />
	              <button type="button" class="tick" onclick="submitFormAjax(this.form,'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','medicament') } }  );">Signer</button>
	            </form>
	          {{/if}}
	        {{/if}}
	      {{/if}}
      {{/if}}
      </td>
    </tr>
  </tbody>
  {{/foreach}}
  
  {{foreach from=$traitements item=traitement}}
    {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" curr_line=$traitement prescription=$prescription->_ref_object->_ref_prescription_traitement prescription_reelle=$prescription}}
  {{/foreach}}
  
 </table> 
{{else}}
  <div class="big-info"> 
     Il n'y a aucun médicament dans cette prescription.
  </div>
{{/if}}



<script type="text/javascript">

prepareForms();


	var todayDate = new Date();
	var dToday = todayDate.toDATE();
	
	dates = {  
	  limit: {
	    start: dToday,
	    stop: null
	  }
	}



Main.add( function(){


  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
     {{if !$curr_line->_traitement && $curr_line->_ref_prescription->object_id}}
       {{if (!$curr_line->signee  || ($mode_pharma && !$curr_line->valide_pharma)) && !$curr_line->valide_pharma}}
       Calendar.regField('editDates-{{$curr_line->_id}}', "debut", false, dates);
       Calendar.regField('editDates-{{$curr_line->_id}}', "_fin", false, dates);
       
       syncDate(document.forms["editDates-{{$curr_line->_id}}"], {{$curr_line->_id}});
     {{/if}}
     {{/if}}
  {{/foreach}}
} );


// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

// Preparation des formulaire
prepareForm(document.addLine);
prepareForm(document.searchProd);

// Autocomplete des medicaments
urlAuto = new Url();
urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.addParam("produit_max", 10);
urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicament
} );


</script>