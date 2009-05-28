{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage [subpackage]
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
 
{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
function checkHeureSortie(){
  var oForm = document.editSejour;
  var heure_entree = parseInt(oForm._hour_entree_prevue.value, 10);
  
  if (oForm._hour_sortie_prevue.value < heure_entree + 1) {
    heure_entree = heure_entree + 1;
    oForm._hour_sortie_prevue.value = heure_entree;
  }
}

function loadTransfert(form, mode_sortie){
  // si Transfert, affichage du select
  if(mode_sortie=="transfert"){
    //Chargement de la liste des etablissement externes
    var url = new Url();
    url.setModuleAction("dPadmissions", "httpreq_vw_etab_externes");
    url.requestUpdate('listEtabExterne', { waitingText : null });
  } else {
    // sinon, on vide le contenu de la div
    $("listEtabExterne").innerHTML = "";
  }
}

function checkModeSortie(){
  var oForm = document.editSejour;
  
  if(oForm.sortie_reelle && oForm.sortie_reelle.value && !oForm.mode_sortie.value) {
    alert("Date de sortie réelle et mode de sortie incompatibles");
    return false;
  }
	
	return true;
}

function checkSejour() {
  var oForm = document.editSejour;
  return checkDureeHospi() && checkModeSortie() && checkForm(oForm);
}

function checkPresta(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  if($V(oForm.prestation_id) != ""){
    if (oForm) {
      $V(oForm.chambre_seule, "1");
    }
    if (oFormEasy) {
      $V(oFormEasy.chambre_seule, "1");
    }
  } 
}


function checkChambreSejour(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  var valeur_chambre = $V(oForm.chambre_seule);
  
  if (oFormEasy)
    $V(oFormEasy.chambre_seule, valeur_chambre, false);
  
  if(valeur_chambre == "0")
    $V(oForm.prestation_id, "", false);
}


function checkChambreSejourEasy(){
  var oForm = document.editSejour;
  var oFormEasy = document.editOpEasy;
  
  if (oFormEasy){
    var valeur_chambre = $V(oFormEasy.chambre_seule);
    $V(oForm.chambre_seule, valeur_chambre);
    
    if(valeur_chambre == "0"){
      $V(oForm.prestation_id, "", false);
    }
  }
}

PatSelector.init = function(){
  bOldPat = document.editSejour.patient_id.value;
  this.sForm     = "editSejour";
  this.sFormEasy = "editOpEasy";

  this.sView_easy = "_patient_view"; 
  this.sId_easy   = "patient_id";

  this.sId   = "patient_id";
  this.sView = "_patient_view";

  this.pop();
}

checkCorrespondantMedical = function(form){
	var url = new Url;
  url.setModuleAction("dPplanningOp", "ajax_check_correspondant_medical");
  url.addParam("patient_id", $V(form.patient_id));
  url.addParam("sejour_id" , $V(form.sejour_id));
  url.requestUpdate("correspondant_medical" , { waitingText: null });
}

CIM10Selector.init = function(){
  this.sForm = "editSejour";
  this.sView = "DP";
  this.sChir = "praticien_id";
  this.pop();
}

Medecin = {
  form: null,
  edit : function() {
    this.form = document.forms.editSejour;
    var url = new Url();
    url.setModuleAction("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
	  $('_adresse_par_prat').show().update('Autres : '+view);
	  $V(this.form.adresse_par_prat_id, id);
	  $V(this.form._correspondants_medicaux, '', false);
  }
};

{{if $mode_operation}}
// Declaration d'un objet Sejour
var Sejour = {
  sejours_collision: [],
  
  
  // Preselectionne un sejour existant en fonction de la date d'intervention choisie
  preselectSejour: function(date_plage){
	  if (!date_plage){
	    return;
	  }
	  
	  var sejours_collision = this.sejours_collision;
	  var oForm = document.editSejour;
	  var sejour_courant_id = oForm.sejour_id.value;
	  	
		// Liste des sejours
		for (sejour_id in sejours_collision){
		  var entree_prevue = sejours_collision[sejour_id]["entree_prevue"];
		  var sortie_prevue = sejours_collision[sejour_id]["sortie_prevue"];
		  if ((entree_prevue <= date_plage) && (sortie_prevue >= date_plage)) {
		    if (sejour_courant_id != sejour_id){
		      var msg = printf("Vous êtes en train de planifier une intervention pour le %s, or il existe déjà un séjour pour ce patient du %s au %s. Souhaitez vous placer l'intervention dans ce séjour ?", 
		                Date.fromDATE(date_plage).toLocaleDate(), 
		                Date.fromDATE(entree_prevue).toLocaleDate(),
		                Date.fromDATE(sortie_prevue).toLocaleDate());
		      
		      if (confirm(msg)){
		        $V(oForm.sejour_id, sejour_id);
		      }
		    }
		  }
		}
  }
}


Main.add( function(){
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  var oForm = document.editOp;
  Sejour.preselectSejour(oForm._date.value);
});
{{/if}}

Main.add( function(){
  prepareForm(document.editSejour);
  
  dates = {
    current: {
      start: "{{$sejour->_date_entree_prevue}}",
      stop: "{{$sejour->_date_sortie_prevue}}"
    },
    spots: {{$sejour->_dates_operations|@json}}
  }
  
  Calendar.regField("editSejour", "entree_reelle", true, dates);
  Calendar.regField("editSejour", "sortie_reelle", true, dates);
  
  dates.limit = {
    start: null,
    stop: dates.spots.first()
  }
  
  Calendar.regField("editSejour", "_date_entree_prevue", false, dates);
  
  dates.limit = {
    start: dates.spots.last(),
    stop: null 
  }
  
  Calendar.regField("editSejour", "_date_sortie_prevue", false, dates);

  var sValue = document.editSejour.praticien_id.value;
  refreshListProtocolesPrescription(sValue, document.editSejour._protocole_prescription_chir_id);
  //refreshListProtocolesPrescription(sValue, document.editSejour._protocole_prescription_anesth_id);
  
  removePlageOp(false);
});
</script>

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkSejour()">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />

{{mb_field object=$sejour field="codes_ccam" hidden=1}}

{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
{{/if}}

<!-- Champ de copie des informations de l'intervention dans le cas ou il y en une -->
{{if $op->_id}}
  <input type="hidden" name="_curr_op_id"   value="{{$op->_id}}" />
  <input type="hidden" name="_curr_op_date" value="{{$op->_ref_plageop->date}}" />
{{else}}
  <input type="hidden" name="_curr_op_id"   value="" />
  <input type="hidden" name="_curr_op_date" value="" />
{{/if}}

{{mb_field object=$sejour field="saisi_SHS" hidden=1 prop=""}}
{{mb_field object=$sejour field="modif_SHS" hidden=1 prop=""}}
<input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
<input type="hidden" name="septique" value="{{$sejour->septique|default:"0"}}" />
<input type="hidden" name="pathologie" value="{{$sejour->pathologie}}" />

<input type="hidden" name="adresse_par_prat_id" value="{{$sejour->adresse_par_prat_id}}" />
{{if !$mode_operation}}
  {{mb_field object=$sejour field="sejour_id" hidden=1 prop=""}}
{{/if}}


<table class="form">

<tr>
  <th class="category" colspan="4">
    {{if $mode_operation && $sejour->sejour_id}}
    
    <div class="idsante400" id="CSejour-{{$sejour->sejour_id}}"></div>
  
    <a style="float:right;" href="#" onclick="view_log('CSejour',{{$sejour->sejour_id}})">
      <img src="images/icons/history.gif" alt="{{tr}}History.desc{{/tr}}" />
    </a>
    <div style="float:left;" class="noteDiv {{$sejour->_class_name}}-{{$sejour->_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
    </div>
    <a class="action" style="float: right"  title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
      <img src="images/icons/edit.png" alt="modifier" />
     </a>
    {{/if}}
    {{tr}}CSejour-msg-informations{{/tr}} {{if $mode_operation && $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
  </th>
</tr>

{{if $sejour->annule}}
<tr>
  <th class="category cancelled" colspan="4">
  {{tr}}CSejour-annule{{/tr}}
  </th>
</tr>
{{/if}}

{{if $mode_operation}}
<tr>
  <th>
    Sejours existants
  </th>
  <td colspan="3" id="selectSejours">
    <select name="sejour_id" onchange="reloadSejour(this.value)">
      <option value="" {{if !$sejour->sejour_id}} selected="selected" {{/if}}>
        &mdash; Créer un nouveau séjour
      </option>
      {{foreach from=$sejours item=curr_sejour}}
      <option value="{{$curr_sejour->_id}}" {{if $sejour->_id == $curr_sejour->_id}} selected="selected" {{/if}}>
        {{$curr_sejour->_view}}
        {{if $curr_sejour->annule}}({{tr}}Cancelled{{/tr}}){{/if}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>
{{/if}}

<tr>
  <th>
    {{mb_label object=$sejour field="group_id"}}
  </th>
  <td colspan="3">
    <select class="{{$sejour->_props.group_id}}" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
    {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    {{mb_label object=$sejour field="praticien_id"}}
  </th>
  <td colspan="3">
    <select name="praticien_id" onchange="modifPrat()" class="{{$sejour->_props.praticien_id}}">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" value="{{$patient->_id}}" onchange="changePat(); checkSejoursToReload(); checkCorrespondantMedical(this.form); " />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" readonly="readonly"
  	  {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	    ondblclick="PatSelector.init()"
  	  {{/if}}
  	/>
  </td>
  <td colspan="2" class="button">
    {{if $dPconfig.dPplanningOp.CSejour.patient_id || !$sejour->_id || $app->user_type == 1}}
  	<button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
  	{{/if}}
  </td>
  
  
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td>{{mb_field object=$sejour field="DP" size="10"}}</td>
  <td colspan="2" class="button"><button type="button" class="search" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="libelle"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="libelle"}}</td>
</tr>

<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form=editSejour field=_date_entree_prevue canNull=false onchange="modifSejour(); updateSortiePrevue();"}}
  </td>
  <td colspan="2">
    à
    <select name="_hour_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie();">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == $heure_entree_jour)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_duree_prevue"}}</th>
  <td>
    <input type="text" name="_duree_prevue" class="num min|0" value="{{if $sejour->sejour_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
    nuits
  </td>
  <td id="dureeEst" colspan="2">
  </td>
</tr>

<tr {{if $mode_operation && !$can->admin}} style="display: none" {{/if}}>
  <th>{{mb_label object=$sejour field="_date_sortie_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form=editSejour field=_date_sortie_prevue canNull=false onchange="updateDureePrevue(); modifSejour()"}}
  </td>
  <td colspan="2">
    à 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == $heure_sortie_ambu)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td>
    {{if !$sejour->type}}
    {{$sejour->type}}
    {{/if}}
    <select name="type" onchange="changeTypeHospi()">
    {{assign var=specType value=$sejour->_specs.type}}
    {{foreach from=$specType->_locales item="curr_type" key="key"}}
      {{if $key != 'urg' || $urgInstalled}}
      <option value="{{$key}}"
        {{if $sejour->type == $key || (!$sejour->type && $key == $specType->default)}}selected="selected"{{/if}}>
        {{$curr_type}}
      </option>
      {{/if}}
    {{/foreach}}
    </select>
  </td>
  
  <th class="reanimation">{{mb_label object=$sejour field="reanimation"}}</th>
  <td class="reanimation">
    {{mb_field object=$sejour field="reanimation"}}
    <script type="text/javascript">

    function changeTypeHospi() {
      var oForm = document.editSejour;
      var sValue = $V(oForm.type);
      if (sValue != "comp") {
        $V(oForm.reanimation, '0');
      }
      
      $(oForm).select(".reanimation").invoke(sValue == "comp" ? "show" : "hide");
    }
    
    changeTypeHospi();
    </script>
  </td>
</tr>

<tbody id="modeExpert">
{{if !$mode_operation}}

<tr>
  <th>{{mb_label object=$sejour field=entree_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=entree_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=entree_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=sortie_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=sortie_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=mode_sortie}}</th>
  <td>
    {{if $can->view}}
      {{mb_field object=$sejour defaultOption="&mdash; Mode de sortie" field=mode_sortie onchange="loadTransfert(this.form, this.value);"}}
      <span id="listEtabExterne">
        {{if $sejour->_id}}
          {{$sejour->_ref_etabExterne->_view}}
        {{/if}}
      </span>
    {{else}}
	  {{mb_value object=$sejour field=mode_sortie}}
	{{/if}}    
  </td>
  <th><strong>{{mb_label object=$sejour field=_sortie_autorisee}}</strong></th>
  <td><strong>{{mb_value object=$sejour field=_sortie_autorisee}}</strong></td>
</tr>


{{/if}}

<tr id="correspondant_medical">
  {{include file="inc_check_correspondant_medical.tpl"}}
</tr>
<tr>
  <td></td>
  <td colspan="3">
    <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
      {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
    </div>
  </td>
</tr>

{{if $listEtab|@count}}
<tr>
  <th>{{mb_label object=$sejour field=adresse_par_etab_id}}</th>
  <td colspan="3">
     <select name="adresse_par_etab_id"">
       <option value="">&mdash;{{tr}}Choose{{/tr}}</option>
       {{foreach from=$listEtab item="etab"}}
         <option value="{{$etab->_id}}" {{if $etab->_id == $sejour->adresse_par_etab_id}}selected="selected"{{/if}}>{{$etab->_view}}</option>
        {{/foreach}}
    </select>
  </td>
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="zt"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="zt"}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="facturable"}}</th>
  <td colspan="3">
  {{mb_field object=$sejour field="facturable"}}
  </td>
</tr>

<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="modalite" typeEnum="radio"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="modalite" typeEnum="radio"}}
  </td>
</tr>

<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="ATNC"}}</th>
  <td>{{mb_field object=$sejour field="ATNC"}}</td>
  <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
  <td>{{mb_field object=$sejour field="hormone_croissance"}}</td>  
</tr>

<tr>
  <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
  <td>
    {{mb_field object=$sejour field="chambre_seule" onchange="checkChambreSejour();"}}
    {{if $mode_operation}}
      {{mb_field object=$sejour field="repas_sans_sel"     hidden="hidden"}}
      {{mb_field object=$sejour field="repas_sans_porc"    hidden="hidden"}}
      {{mb_field object=$sejour field="repas_diabete"      hidden="hidden"}}
      {{mb_field object=$sejour field="repas_sans_residu"  hidden="hidden"}}
    {{/if}}
  </td>
  
  {{if $mode_operation}}
  <td colspan="2" class="button">
    <button type="button" class="new" onclick="popRegimes()">Régime alimentaire</button>
  </td>
  {{else}}
  <th>{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td>{{mb_field object=$sejour field="repas_sans_sel"}}</td>
  {{/if}}
</tr>

{{if $prestations}}
<tr>
<th>{{mb_label object=$sejour field=prestation_id}}</th>
  <td>
  <select name="prestation_id" onchange="checkPresta();">
  <option value="">&mdash; Choix d'une prestation</option>
  {{foreach from=$prestations item="_prestation"}}
    <option value="{{$_prestation->_id}}" {{if $sejour->prestation_id == $_prestation->_id}}selected = selected{{/if}}>{{$_prestation->_view}}</option>
  {{/foreach}}
  </select>
  </td>
  {{if $mode_operation}}
  <td colspan="2" />
  {{/if}}
</tr>
{{/if}}

<!-- Si on est pas en mode operation, on affiche la suite -->
{{if !$mode_operation}}
<tr>
  <th>{{mb_label object=$sejour field="lit_accompagnant"}}</th>
  <td>{{mb_field object=$sejour field="lit_accompagnant"}}</td>
  <th>{{mb_label object=$sejour field="repas_sans_porc"}}</th>
  <td>{{mb_field object=$sejour field="repas_sans_porc"}}</td>

</tr>

<tr>
  <th>{{mb_label object=$sejour field="isolement"}}</th>
  <td>{{mb_field object=$sejour field="isolement"}}</td>
  <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
  <td>{{mb_field object=$sejour field="repas_diabete"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="television"}}</th>
  <td>{{mb_field object=$sejour field="television"}}</td>
  <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
  <td>{{mb_field object=$sejour field="repas_sans_residu"}}</td>
</tr>
{{/if}}

<tr>
  <td class="text">{{mb_label object=$sejour field="convalescence"}}</td>
  <td class="text" colspan="3">{{mb_label object=$sejour field="rques"}}</td>
</tr>

<tr>
  <td>{{mb_field object=$sejour field="convalescence" rows="3"}}</td>
  <td colspan="3">{{mb_field object=$sejour field="rques" rows="3"}}</td>
</tr>

{{if !$sejour->_id && array_key_exists("dPprescription", $modules)}}
<tr>
  <td>{{tr}}CProtocole-protocole_prescription_anesth_id{{/tr}}</td>
  <td colspan="3">{{tr}}CProtocole-protocole_prescription_chir_id{{/tr}}</td>
</tr>
<tr>
  <td>
    <div id="prot_anesth_view"></div>
    <input type="hidden" name="_protocole_prescription_anesth_id" value="" />
  </td>
  <td colspan="3"><select name="_protocole_prescription_chir_id"></select></td>
</tr>
{{/if}}
</tbody>


{{if !$mode_operation}}
<tr>
  <td class="button" colspan="4">
  {{if $sejour->sejour_id}}
    <button class="modify" type="submit">Modifier</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|smarty:nodefaults|JSAttribute}}'});">
      Supprimer
    </button>
    {{mb_ternary var=annule_text test=$sejour->annule value="Rétablir" other="Annuler"}}
    {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
    <button class="{{$annule_class}}" type="button" onclick="cancelSejour();">
      {{$annule_text}}
    </button>
  {{else}}
    <button class="submit" type="submit">Créer</button>
  {{/if}}
  </td>
</tr>
{{/if}}
</table>

</form>

{{if $mode_operation && $isPrescriptionInstalled}}
<table style="width:100%" class="form"> 
  <tr>
    <td id="prescription_register">
		  <script type="text/javascript">
		    PrescriptionEditor.register('{{$sejour->_id}}','{{$sejour->_class_name}}','dhe','{{$sejour->praticien_id}}');
		  </script>
    </td>
  </tr>  
</table>
{{/if}}
