<script type="text/javascript">

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
  if(!oForm.sortie_reelle.value && !oForm.mode_sortie.value){
    return true;
  }
  if(oForm.sortie_reelle.value && oForm.mode_sortie.value){
    return true;
  }
  alert("Date de sortie reelle et mode de sortie incompatible");
  return false;
}


function checkPresta(){
  var oForm = document.editSejour;
  if(oForm.prestation_id.value != ""){
    setRadioValue(oForm.chambre_seule, "1");
  } 
}


function checkChambre(){
  var oForm = document.editSejour;
  var valeur_chambre = getCheckedValue(oForm.chambre_seule);
  if(valeur_chambre == "0"){
    oForm.prestation_id.value = "";
  }
}

</script>

<!-- $Id: $ -->
{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="if(checkDureeHospi() && checkModeSortie()){return checkForm(this);}else{return false;}">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
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
{{if !$mode_operation}}
  {{mb_field object=$sejour field="sejour_id" hidden=1 prop=""}}
{{/if}}


<table class="form" onmousemove="checkSejoursToReload()">

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
    {{/if}}
    {{tr}}msg-CSejour-informations{{/tr}}
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
      <option value="{{$curr_sejour->sejour_id}}" {{if $sejour->sejour_id == $curr_sejour->sejour_id}} selected="selected" {{/if}}>
        {{$curr_sejour->_view}}
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
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="bChangePat = 1;" />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" ondblclick="PatSelector.init()" readonly="readonly" />
  </td>
  <td colspan="2" class="button">
  	<button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
    <script type="text/javascript">
      PatSelector.init = function(){
      this.sForm     = "editSejour";
      this.sFormEasy = "editOpEasy";
      
      this.sView_easy = "_patient_view"; 
      this.sId_easy   = "patient_id";
      
      this.sId   = "patient_id";
      this.sView = "_patient_view";
      this.pop();
    }
    </script>
  </td>
  
  
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td>{{mb_field object=$sejour field="DP" size="10"}}</td>
  <td colspan="2" class="button"><button type="button" class="search" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
  <script type="text/javascript">
  CIM10Selector.init = function(){
    this.sForm = "editSejour";
    this.sView = "DP";
    this.sChir = "praticien_id";
    this.pop();
  }
  </script>
  </td>
</tr>
<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form="editSejour" field="_date_entree_prevue" prop="notNull date" onchange="modifSejour(); updateSortiePrevue();"}}
  </td>
  <td colspan="2">
    à
    <select name="_hour_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == $heure_entree_jour)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$mins|smarty:nodefaults item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_duree_prevue"}}</th>
  <td>
    <input type="text" name="_duree_prevue" class="num min|0" value="{{if $sejour->sejour_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
    jours
  </td>
  <td id="dureeEst" colspan="2">
  </td>
</tr>

<tr {{if $mode_operation}} style="display: none" {{/if}}>
  <th>{{mb_label object=$sejour field="_date_sortie_prevue"}}</th>
  <td class="date">
    {{mb_field object=$sejour form="editSejour" field="_date_sortie_prevue" prop="notNull date moreEquals|_date_entree_prevue" onchange="updateDureePrevue(); modifSejour()"}}
  </td>
  <td colspan="2">
    à 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == $heure_sortie_ambu)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue">
    {{foreach from=$mins|smarty:nodefaults item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> mn
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <th>{{mb_label object=$sejour field=entree_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->admin}}
    {{mb_field object=$sejour field=entree_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=entree_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
  <td class="date" colspan="3">
    {{if $can->view}}
    {{mb_field object=$sejour field=sortie_reelle form=editSejour}}
    {{else}}
		{{mb_value object=$sejour field=sortie_reelle}}
		{{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=mode_sortie}}</th>
  <td colspan="3">
    {{if $can->view}}
      {{mb_field object=$sejour defaultOption="&mdash; Mode de sortie" field=mode_sortie onchange="loadTransfert(this.form, this.value);"}}
      <div id="listEtabExterne" style="display: inline"></div>
    {{else}}
	  {{mb_value object=$sejour field=mode_sortie}}
	{{/if}}    
  </td>
</tr>



{{/if}}


<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td>
    {{mb_field object=$sejour field="type" onchange="changeTypeHospi(this.value)"}}
  </td>
  <td colspan="2">
    <div id="showFor-comp" {{if !$sejour->_id || $sejour->type != "comp"}} style="display: none;" {{/if}}>
      {{mb_label object=$sejour field="reanimation"}}{{mb_field object=$sejour field="reanimation"}}
    </div>
    <div id="showFor-ambu" {{if $sejour->_id && $sejour->type != "ambu"}} style="display: none;" {{/if}}>
      {{mb_label object=$sejour field="zt"}}{{mb_field object=$sejour field="zt"}}
    </div>
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

{{if $can->edit}}
<tr {{if $mode_operation}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$sejour field="ATNC"}}</th>
  <td>
    {{mb_field object=$sejour field="ATNC"}}
  </td>
  <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
  <td>
    {{mb_field object=$sejour field="hormone_croissance"}}
  </td>  
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
  <td {{if $mode_operation}}colspan="3"{{/if}}>
    {{mb_field object=$sejour field="chambre_seule" onchange="checkChambre();"}}
  </td>
  
  {{if !$mode_operation}}
  <th>{{mb_label object=$sejour field="lit_accompagnant"}}</th>
  <td>
    {{mb_field object=$sejour field="lit_accompagnant"}}
  </td>
  {{/if}}
</tr>

{{if $prestations}}
<tr>
<th>{{mb_label object=$sejour field="prestation_id"}}</th>
  <td>
  <select name="prestation_id" onchange="checkPresta();">
  <option value="">&mdash; Choix d'une prestation</option>
  {{foreach from=$prestations item="_prestation"}}
    <option value="{{$_prestation->_id}}" {{if $sejour->prestation_id == $_prestation->_id}}selected = selected{{/if}}>{{$_prestation->_view}}</option>
  {{/foreach}}
  </select>
  </td>
  {{if $mode_operation}}
  <td colspan="2"></td>
  {{/if}}
</tr>
{{/if}}

<!-- Si on est pas en mode operation, on affiche la suite -->
{{if !$mode_operation}}
<tr>
	{{if $can->edit}}
  <th>{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_sel"}}
  </td>
	{{/if}}

</tr>

{{if $can->edit}}
<tr>
  <th>{{mb_label object=$sejour field="isolement"}}</th>
  <td>
    {{mb_field object=$sejour field="isolement"}}
  </td>
  <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_diabete"}}
  </td>
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="television"}}</th>
  <td>
    {{mb_field object=$sejour field="television"}}
  </td>
	{{if $can->edit}}
  <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_residu"}}
  </td>
	{{/if}}
</tr>

<tr>
  <th>{{mb_label object=$sejour field="venue_SHS"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="venue_SHS" size="8" maxlength="8"}}
  </td></tr>
{{/if}}

<tr>
  <td class="text">
    {{mb_label object=$sejour field="convalescence"}}
  </td>
  <td class="text" colspan="3">
    {{mb_label object=$sejour field="rques"}}
  </td>
</tr>

<tr>
  <td>
    {{mb_field object=$sejour field="convalescence" rows="3"}}
  </td>
  <td colspan="3">
    {{mb_field object=$sejour field="rques" rows="3"}}
  </td>
</tr>


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

<script type="text/javascript">
// Explicit form preparation for Ajax loading
prepareForm(document.editSejour);
var datesOperations = {{$sejour->_dates_operations|@json}};
var dates = {
  current: {
    start: "{{$sejour->entree_prevue}}",
    stop: "{{$sejour->sortie_prevue}}",
  },
  spots: datesOperations
}

Calendar.regField("editSejour", "entree_reelle", true, dates);
Calendar.regField("editSejour", "sortie_reelle", true, dates);

dates.limit = {
  start: null,
  stop: datesOperations.first()
}

Calendar.regField("editSejour", "_date_entree_prevue", false, dates);

dates.limit = {
  start: datesOperations.last(),
  stop: null 
}

Calendar.regField("editSejour", "_date_sortie_prevue", false, dates);

//regFieldCalendar("editSejour", "_date_entree_prevue");
//regFieldCalendar("editSejour", "_date_sortie_prevue");
//regFieldCalendar("editSejour", "entree_reelle", true);
//regFieldCalendar("editSejour", "sortie_reelle", true);
removePlageOp(false);
</script>
