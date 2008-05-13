<script type="text/javascript">

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
      url.requestUpdate('prises-Med'+curr_line_id, { waitingText: null });
    } 
   }
  );
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

// Initialisation des dates pour les calendars
dates = {  
  limit: {
    start: new Date().toDATE(),
    stop: null
  }
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
  <input type="hidden" name="prescription_line_medicament_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="object_class" value="{{$prescription->object_class}}" />
  <input type="hidden" name="object_id" value="{{$prescription->object_id}}" />
  
  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
  <input type="hidden" name="code_cip" value=""/>
  <!-- Date de debut -->
  <input type="hidden" name="debut" value="{{$today}}" />
  
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
	  
	  <input type="checkbox" name="_recherche_livret" value="1" {{if $prescription->type=="sejour"}}checked="checked"{{/if}} />
	  
	  Livret Thérapeutique
	  
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
   <form name="addLineCommentMed" method="post" action="" onsubmit="return onSubmitFormAjax(this, { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
      <input type="hidden" name="chapitre" value="medicament" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="this.form.onsubmit();">Ajouter</button>
    </form>
 </div> 

{{assign var=traitements value=$prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines}}
  
{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment || $traitements}}
<table class="tbl">
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{include file="../../dPprescription/templates/inc_vw_line_medicament.tpl" prescription_reelle=$prescription}} 
  {{/foreach}}
 
  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
	  <th colspan="8">Commentaires</th>
	</tr>
  {{/if}}
  
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{include file="../../dPprescription/templates/inc_vw_line_comment_elt.tpl"}}
  {{/foreach}}
  
  <!-- Affichage des traitements -->
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

//prepareForms();


/*
Main.add( function(){
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
     {{if !$curr_line->_traitement && $curr_line->_ref_prescription->object_id}}
       {{if (!$curr_line->signee  || ($mode_pharma && !$curr_line->valide_pharma)) && !$curr_line->valide_pharma}}
       //Calendar.regField('editDates-Med-{{$curr_line->_id}}', "debut", false, dates);
       //Calendar.regField('editDates-Med-{{$curr_line->_id}}', "_fin", false, dates);
       
       //syncDate(document.forms["editDates-Med-{{$curr_line->_id}}"], {{$curr_line->_id}},'','Med');
     {{/if}}
     {{/if}}
  {{/foreach}}
} );
*/

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

var oFormProduit = document.searchProd;

// Autocomplete des medicaments
urlAuto = new Url();
urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.addParam("produit_max", 20);


// callback => methode pour ajouter en post des parametres
// Faire un mini framework pour rajouter des elements du meme formulaire

urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicament,
  callback: 
    function(input, queryString){ 
      return (queryString + "&inLivret="+getCheckedValue(oFormProduit._recherche_livret)); 
    }
} );


</script>