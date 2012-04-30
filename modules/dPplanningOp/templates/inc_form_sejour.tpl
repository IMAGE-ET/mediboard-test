{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage [subpackage]
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
 
{{mb_script module="dPpatients" script="pat_selector"}}
{{mb_script module="dPpatients" script="medecin"}}
{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_default var=count_prestations value=0}}
{{mb_default var=_duree_prevue value=0}}
{{mb_default var=dialog value=0}}

{{if "maternite"|module_active}}
  {{assign var=maternite_active value="1"}}
  {{if !$sejour->_id && $sejour->grossesse_id}}
    {{assign var=_duree_prevue value=$sejour->_duree_prevue}}
  {{/if}}
{{else}}
  {{assign var=maternite_active value="0"}}
{{/if}}

<script type="text/javascript">  
function modifLits(lit_id){
  var form = getForm('editSejour');
  
  var service = $('CLit-'+lit_id).className;
  service = service.split("-");
  form.service_sortie_id.value = service[1];
  
  form.service_sortie_id_autocomplete_view.value = service[2];
}

function checkHeureSortie(){
  var oForm = getForm("editSejour");
  var heure_entree = parseInt(oForm._hour_entree_prevue.value, 10);
  
  if (oForm._hour_sortie_prevue.value < heure_entree + 1) {
    heure_entree++;
    oForm._hour_sortie_prevue.value = heure_entree;
  }
}

function loadTransfert(mode_sortie){
  $('listEtabExterne').setVisible(mode_sortie == "transfert");
}

function loadServiceMutation(mode_sortie){
  $('services').setVisible(mode_sortie == "mutation");
  $('lit_sortie_transfert').setVisible(mode_sortie == "mutation");
}

function changeModeSortie(mode_sortie){
  loadTransfert(mode_sortie);
  loadServiceMutation(mode_sortie);
}

function checkModeSortie(){
  var oForm = getForm("editSejour");
  
  if(oForm.sortie_reelle && oForm.sortie_reelle.value && !oForm.mode_sortie.value) {
    alert("Date de sortie réelle et mode de sortie incompatibles");
    return false;
  }
  
  return true;
}

function checkSejour() {
  var oForm = getForm("editSejour");
  return checkDureeHospi() && checkModeSortie() && OccupationServices.testOccupation() && checkForm(oForm);
}

function checkPresta(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
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
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  var valeur_chambre = $V(oForm.chambre_seule);
  
  if (oFormEasy)
    $V(oFormEasy.chambre_seule, valeur_chambre, false);
  
  if(valeur_chambre == "0")
    $V(oForm.prestation_id, "", false);
}


function checkChambreSejourEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy){
    var valeur_chambre = $V(oFormEasy.chambre_seule);
    $V(oForm.chambre_seule, valeur_chambre);
    
    if(valeur_chambre == "0"){
      $V(oForm.prestation_id, "", false);
    }
  }
}

function checkConsultAccompSejour(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  var valeur_consult = $V(oForm.consult_accomp);
  
  if (oFormEasy)
    $V(oFormEasy.consult_accomp, valeur_consult, false);
}


function checkConsultAccompSejourEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy){
    var valeur_consult = $V(oFormEasy.consult_accomp);
    $V(oForm.consult_accomp, valeur_consult);
  }
}

function checkAccident(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  var date_accident    = $V(oForm.date_accident);
  var date_accident_da = $V(oForm.date_accident_da);
  var nature_accident  = $V(oForm.nature_accident);
  
  if (oFormEasy) {
    $V(oFormEasy.date_accident   , date_accident   , false);
    $V(oFormEasy.date_accident_da, date_accident_da, false);
    $V(oFormEasy.nature_accident , nature_accident , false);
  }
}


function checkAccidentEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy) {
    var date_accident    = $V(oFormEasy.date_accident);
    var date_accident_da = $V(oFormEasy.date_accident_da);
    var nature_accident  = $V(oFormEasy.nature_accident);
    $V(oForm.date_accident   , date_accident   , false);
    $V(oForm.date_accident_da, date_accident_da, false);
    $V(oForm.nature_accident , nature_accident , false);
  }
}

function checkAssurances(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  var assurance_maladie        = $V(oForm.assurance_maladie);
  var rques_assurance_maladie  = $V(oForm.rques_assurance_maladie);
  var assurance_accident       = $V(oForm.assurance_accident);
  var rques_assurance_accident = $V(oForm.rques_assurance_accident);
  
  if (oFormEasy) {
    $V(oFormEasy.assurance_maladie       , assurance_maladie       , false);
    $V(oFormEasy.rques_assurance_maladie , rques_assurance_maladie , false);
    $V(oFormEasy.assurance_accident      , assurance_accident      , false);
    $V(oFormEasy.rques_assurance_accident, rques_assurance_accident, false);
  }
}


function checkAssurancesEasy(){
  var oForm = getForm("editSejour");
  var oFormEasy = getForm("editOpEasy");
  
  if (oFormEasy) {
    var assurance_maladie        = $V(oFormEasy.assurance_maladie);
    var rques_assurance_maladie  = $V(oFormEasy.rques_assurance_maladie);
    var assurance_accident       = $V(oFormEasy.assurance_accident);
    var rques_assurance_accident = $V(oFormEasy.rques_assurance_accident);
    $V(oForm.assurance_maladie       , assurance_maladie       , false);
    $V(oForm.rques_assurance_maladie , rques_assurance_maladie , false);
    $V(oForm.assurance_accident      , assurance_accident      , false);
    $V(oForm.rques_assurance_accident, rques_assurance_accident, false);
  }
}

function printFormSejour() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning"); 
  url.addParam("sejour_id", $V(getForm("editSejour").sejour_id));
  url.popup(700, 500, "printSejour");
  return;
}

PatSelector.init = function(){
  window.bOldPat = $V(getForm("editSejour").patient_id);
  this.sForm     = "editSejour";
  this.sFormEasy = "editOpEasy";

  this.sView_easy = "_patient_view"; 
  this.sId_easy   = "patient_id";

  this.sId   = "patient_id";
  this.sView = "_patient_view";
  this.sSexe = "_patient_sexe";
  this.pop();
}

CIM10Selector.init = function(){
  this.sForm = "editSejour";
  this.sView = "DP";
  this.sChir = "praticien_id";
  this.pop();
}

{{if $mode_operation}}
// Declaration d'un objet Sejour
var Sejour = {
  sejours_collision: null,
  preselected: false,
  // Preselectionne un sejour existant en fonction de la date d'intervention choisie
  preselectSejour: function(date_plage){
    if (!date_plage || this.preselected){
      return;
    }
    
    var sejours_collision = this.sejours_collision;
    var oForm = getForm("editSejour");
    var sejour_courant_id = $V(oForm.sejour_id);
    // Liste des sejours
    if(sejours_collision instanceof Array) {
      return;
    }
    for (sejour_id in sejours_collision){
      var entree = sejours_collision[sejour_id]["entree"];
      var sortie = sejours_collision[sejour_id]["sortie"];
      if ((entree <= date_plage) && (sortie >= date_plage)) {
        if (sejour_courant_id != sejour_id){
          var msg = printf("Vous êtes en train de planifier une intervention pour le %s, or il existe déjà un séjour pour ce patient du %s au %s. Souhaitez-vous placer l'intervention dans ce séjour ?", 
                    Date.fromDATE(date_plage).toLocaleDate(), 
                    Date.fromDATE(entree).toLocaleDate(),
                    Date.fromDATE(sortie).toLocaleDate());
                    
          if (confirm(msg)){
            this.preselected = true;
            $V(oForm.sejour_id, sejour_id);
            return;
          }
        }
      }
    }
  }
}

Main.add( function(){
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  var oForm = getForm("editOp");
  Sejour.preselectSejour($V(oForm._date));
});
{{/if}}

Main.add( function(){
  var form = getForm("editSejour");
  
  dates = {
    current: {
      start: "{{$sejour->_date_entree_prevue}}",
      stop: "{{$sejour->_date_sortie_prevue}}"
    },
    spots: []
  };

  var options = {
    exactMinutes: false, 
    minInterval: {{$conf.dPplanningOp.CSejour.min_intervalle}},
    minHours: {{$conf.dPplanningOp.CSejour.heure_deb}},
    maxHours: {{$conf.dPplanningOp.CSejour.heure_fin}}
  };
  
  // Object.value takes the internal functions too :(
  var dates_operations = {{$sejour->_dates_operations|@json}};
  $H(dates_operations).each(function(p){
    if (!Object.isFunction(p.value))
      dates.spots.push(p.value);
  });
  
  Calendar.regField(form.entree_reelle, dates);
  Calendar.regField(form.sortie_reelle, dates);
  
// Constraints make intervention moving fastidious
//  dates.limit = {
//    start: null,
//    stop: dates.spots.first()
//  };
  
  Calendar.regField(form._date_entree_prevue, dates);
  
// Constraints make intervention moving fastidious
//  dates.limit = {
//    start: dates.spots.last(),
//    stop: null 
//  };
  
  Calendar.regField(form._date_sortie_prevue, dates);
  
  removePlageOp(false);
  OccupationServices.initOccupation();
  OccupationServices.configBlocage = ({{$conf.dPplanningOp.CSejour.blocage_occupation|@json}} == "1") && !{{$modules.dPcabinet->_can->edit|@json}};
});
</script>

<!-- div de confirmation de changement de patient lorsqu'on a un sejour_id -->
{{mb_include template=inc_modal_change_patient}}

<form name="patAldForm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_patients_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="patient_id" value="">
  <input type="hidden" name="ald" value="">
  <input type="hidden" name="cmu" value="">
</form>


<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkSejour()">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />

{{if $dialog}}
  <input type="hidden" name="postRedirect" value="m=planningOp&a=vw_edit_sejour&dialog=1" />
{{/if}}

{{if $sejour->sortie_reelle && !$can->admin}}
<!-- <input type="hidden" name="_locked" value="1" /> -->
{{/if}}

{{mb_field object=$sejour field="codes_ccam" hidden=1}}

{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
{{/if}}

<!-- Champ de copie des informations de l'intervention dans le cas ou il y en une -->
{{if $op->_id}}
  <input type="hidden" name="_curr_op_id"   value="{{$op->_id}}" />
  <input type="hidden" name="_curr_op_date" value="{{$op->_datetime|iso_date}}" />
{{else}}
  <input type="hidden" name="_curr_op_id"   value="" />
  <input type="hidden" name="_curr_op_date" value="" />
{{/if}}

{{mb_field object=$sejour field="entree_preparee" hidden=1}}
{{mb_field object=$sejour field="entree_modifiee" hidden=1}}
<input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
<input type="hidden" name="septique" value="{{$sejour->septique|default:"0"}}" />
<input type="hidden" name="pathologie" value="{{$sejour->pathologie}}" />

<input type="hidden" name="adresse_par_prat_id" value="{{$sejour->adresse_par_prat_id}}" />
{{if !$mode_operation}}
  {{mb_key object=$sejour}}
{{/if}}

<table class="form">
<col style="width:20%" />
<col style="width:40%" />
<col style="width:20%" />
<col style="width:20%" />
<tr>
  <th class="category" colspan="4">
    {{if $mode_operation && $sejour->_id}}

    {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
    {{mb_include module=system template=inc_object_history    object=$sejour}}
    {{mb_include module=system template=inc_object_notes      object=$sejour}}

    <a class="action" style="float: right"  title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
      <img src="images/icons/edit.png" alt="modifier" />
     </a>
    {{/if}}
    {{tr}}CSejour-msg-informations{{/tr}} 
    {{if $mode_operation && $sejour->_NDA}}
    {{mb_include module=planningOp template=inc_vw_numdos nda=$sejour->_NDA}}
    {{/if}}
  </th>
</tr>

{{if $sejour->annule}}
<tr>
  <th class="category cancelled" colspan="4">
  {{tr}}CSejour-{{$sejour->recuse|ternary:"recuse":"annule"}}{{/tr}}
  </th>
</tr>
{{/if}}

{{if $mode_operation}}
<tr>
  <th>
    Sejours existants
  </th>
  <td colspan="3" id="selectSejours">
    <select name="sejour_id" style="width: 15em" onchange="reloadSejour(this.value)">
      <option value="" {{if !$sejour->_id}} selected="selected" {{/if}}>
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
    {{if !$sejour->_id || $can->admin}}
    <select class="{{$sejour->_props.group_id}}" style="width: 15em" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->_view}}</option>
    {{/foreach}}
    </select>
    {{else}}
      {{$sejour->_ref_group->_view}}
      {{mb_field object=$sejour field=group_id hidden=hidden}}
    {{/if}}
  </td>
</tr>

<tr>
  <th>
    {{mb_label object=$sejour field=praticien_id}}
  </th>
  <td colspan="3">
    {{if $sejour->praticien_id && !array_key_exists($sejour->praticien_id, $listPraticiens)}}
    {{mb_field object=$sejour field=praticien_id hidden=1}}
    {{mb_value object=$sejour field=praticien_id}}
    {{else}} 
    <select name="praticien_id" onchange="modifPrat()" class="{{$sejour->_props.praticien_id}}" style="width: 15em">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $praticien->_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien}}
      </option>
      {{/foreach}}
    </select>
    {{/if}}
  </td>
</tr>

<tr>
  <th>
    <script type="text/javascript">
      if (window.reloadSejours == null) {
        reloadSejours = Prototype.emptyFunction;
      }
    </script>
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" value="{{$patient->_id}}" 
      onchange="changePat(); reloadSejours();" />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td colspan="3">
    <input type="text" name="_patient_view" style="width: 15em" value="{{$patient->_view}}" readonly="readonly"
           onfocus="
      {{if !$sejour->_id}}
        PatSelector.init();
      {{else}}
        confirmChangePatient();
      {{/if}}"/>
    <button type="button" class="search notext" onclick="
      {{if !$sejour->_id}}
        PatSelector.init();
      {{else}}
        confirmChangePatient();
    {{/if}}">
      Choisir un patient
    </button>
    <button id="button-edit-patient" type="button" 
            onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value" 
            class="edit notext" {{if !$patient->_id}}style="display: none;"{{/if}}>
      {{tr}}Edit{{/tr}}
    </button>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="libelle"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="libelle" form="editSejour" style="width: 12em" autocomplete="true,1,50,true,true"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td colspan="3">
    <script type="text/javascript">
    Main.add(function(){
      var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
      url.autoComplete(getForm("editSejour").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        select: "code",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editSejour").DP, oHidden.value);
        }
      });
    });
    </script>
    
    <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$sejour->DP}}" onchange="if(getForm('editOp')) {synchroService(this)};" style="width: 12em" />
    <button type="button" class="cancel notext" onclick="$V(this.form.DP, '');" />
    <button type="button" class="search notext" onclick="CIM10Selector.init()">{{tr}}button-CCodeCIM10-choix{{/tr}}</button>
    <input type="hidden" name="DP" value="{{$sejour->DP}}" onchange="$V(this.form.keywords_code, this.value); if(getForm('editOp')) {synchroService(this)};"/>
  </td>
</tr>

<tbody id="ald_patient">
  {{mb_include module=planningOp template=inc_check_ald patient=$sejour->_ref_patient}}
</tbody>

{{if $conf.dPplanningOp.CSejour.accident}}
<tr>
  <th>{{mb_label object=$sejour field="date_accident"}}</th>
  <td colspan="3">{{mb_field object=$sejour form="editSejour" field="date_accident" register=true onchange="checkAccident();"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="nature_accident"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="nature_accident" emptyLabel="Choose" style="width: 15em;" onchange="checkAccident();"}}</td>
</tr>
{{/if}}

<tr>
  <th>
    {{mb_label object=$sejour field="service_id"}}
  </th>
  <td colspan="3">
    <select name="service_id" class="{{$sejour->_props.service_id}}" style="width: 15em"
      onchange="if(getForm('editOp')) {synchroService(this)};">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$listServices item=_service}}
      <option value="{{$_service->_id}}" {{if $sejour->service_id == $_service->_id}} selected="selected" {{/if}}>
        {{$_service->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

{{if $can->admin}}
<tr>
  <th>
    {{mb_label object=$sejour field=_unique_lit_id}}
  </th>
  <td colspan="3">
    {{mb_field object=$sejour field=_unique_lit_id hidden=true}}
    <input type="text" name="_unique_lit_id_view" style="width: 12em" value="" />
    <script type="text/javascript">
      Main.add(function(){
        var form = getForm("editSejour");
        
        var url = new Url("system", "ajax_seek_autocomplete");
        url.addParam("object_class", "CLit");
        url.addParam("field", "_unique_lit_id");
        url.addParam("input_field", "_unique_lit_id_view");
        url.autoComplete(form.elements._unique_lit_id_view, null, {
          minChars: 2,
          method: "get",
          select: "view",
          dropdown: true,
          afterUpdateElement: function(field, selected){
            var value = selected.id.split('-')[2];
            $V(form._unique_lit_id, value);
          },
          callback: function(input, queryString){
            if (service_id = $V(form.service_id)) {
              queryString += "&where[chambre.service_id]="+service_id;
              queryString += "&ljoin[chambre]=chambre.chambre_id = lit.chambre_id";
            }
            return queryString;
          }
          });
      });
    </script>
  </td>
</tr>
{{/if}}
{{if $maternite_active && !$mode_operation && @$modules.maternite->_can->read}}
  <tr>
    <th>{{tr}}CGrossesse{{/tr}}</th>  
    <td colspan="3">
      {{mb_include module=maternite template=inc_input_grossesse object=$sejour patient=$patient}}
    </td>
  </tr>
{{/if}}
{{if $sejour->annule}}
<tr>
  <th>{{mb_label object=$sejour field="recuse"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="recuse"}}</td>
</tr>
{{/if}}

<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td colspan="2">
    {{mb_field object=$sejour form=editSejour field=_date_entree_prevue canNull=false 
      onchange="OccupationServices.updateOccupation(); modifSejour(); updateSortiePrevue(); reloadSejours(true);"}}
    à
    <select name="_hour_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie(); reloadSejours(true);">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == $heure_entree_jour)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_entree_prevue" onchange="updateHeureSortie(); checkHeureSortie(); reloadSejours(true);">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
  <td>
    {{if $can->admin}}
      (admin: {{mb_value object=$sejour field=entree_prevue}})
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_duree_prevue"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="_duree_prevue" increment=true form=editSejour prop="num min|0" size=2 onchange="updateSortiePrevue(); checkDureeHospi('syncType');" value=$sejour->sejour_id|ternary:$sejour->_duree_prevue:$_duree_prevue}}
    {{tr}}night{{/tr}}(s)
    - (<span id="dureeEst"></span>)
  </td>
</tr>

<tr {{if $mode_operation && !$can->admin}} style="display: none" {{/if}}>
  <th>{{mb_label object=$sejour field="_date_sortie_prevue"}}</th>
  <td colspan="2">
    {{mb_field object=$sejour form=editSejour field=_date_sortie_prevue canNull=false 
      onchange="updateDureePrevue(); modifSejour(); reloadSejours(true);"}}
    à 
    <select name="_hour_sortie_prevue" onchange="reloadSejours(true);">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == $heure_sortie_ambu)}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select> h
    <select name="_min_sortie_prevue"  onchange="reloadSejours(true);">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select> min
  </td>
  <td>
    {{if $can->admin}}
      (admin: {{mb_value object=$sejour field=sortie_prevue}})
    {{/if}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td>
    {{mb_field object=$sejour field=type style="width: 15em;"
      onchange="changeTypeHospi(); OccupationServices.updateOccupation(); checkDureeHospi('syncDuree');"}}
  </td>
  
  <td colspan="2" rowspan="2">
    <table>
      <tr class="reanimation">
        <th>{{mb_label object=$sejour field="reanimation"}}</th>
        <td colspan="3"> {{mb_field object=$sejour field="reanimation"}} </td>
      </tr>
      
      <tr class="UHCD">
        <th>{{mb_label object=$sejour field="UHCD"}}</th>
        <td colspan="3">
          {{mb_field object=$sejour field="UHCD"}}
          <script type="text/javascript">
            function changeTypeHospi() {
              var oForm = getForm("editSejour");
              var sValue = $V(oForm.type);
              if (sValue != "comp") {
                $V(oForm.reanimation, '0');
                $V(oForm.UHCD       , '0');
              }
              
              $(oForm).select(".reanimation").invoke(sValue == "comp" ? "show" : "hide");
              $(oForm).select(".UHCD").invoke(sValue == "comp" ? "show" : "hide");
            }
      
          Main.add(changeTypeHospi);
          </script>
        </td>
      </tr>
    </table>
  </td>
</tr>

{{if $conf.dPplanningOp.CSejour.show_type_pec}}
  <tr>
    <th>{{mb_label object=$sejour field="type_pec"}}</th>
    <td>
      {{mb_field object=$sejour field="type_pec" emptyLabel="Choose" typeEnum="radio"}}
    </td>
  </tr>
{{/if}}

<tr>
  <th>Taux d'occupation</th>
  <td colspan="3" id="occupation">
  </td>
</tr>

{{if $conf.dPplanningOp.CSejour.consult_accomp}}
<tr>
  <th>{{mb_label object=$sejour field=consult_accomp}}</th>
  <td colspan="3">{{mb_field object=$sejour field=consult_accomp typeEnum=radio onchange="checkConsultAccompSejour();"}}</td>
</tr>
{{/if}}

<tbody class="modeExpert">
{{if !$mode_operation}}

<tr>
  <th>{{mb_label object=$sejour field=entree_reelle}}</th>
  <td colspan="3">
    {{if $can->edit}}
    {{mb_field object=$sejour field=entree_reelle form=editSejour}}
    {{else}}
    {{mb_value object=$sejour field=entree_reelle}}
    {{/if}}    
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field=sortie_reelle}}</th>
  <td colspan="3">
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
      {{mb_field object=$sejour emptyLabel="Choose" field=mode_sortie onchange="changeModeSortie(this.value);" style="width: 15em;"}}
      <div id="listEtabExterne" {{if !$sejour->etablissement_sortie_id}}style="display:none"{{/if}}>
        {{mb_field object=$sejour field="etablissement_sortie_id" form="editSejour" autocomplete="true,1,50,true,true" style="width: 12em;"}}
      </div>
      <div id="services" {{if !$sejour->service_sortie_id}}style="display:none"{{/if}}>
        {{mb_field object=$sejour field="service_sortie_id" form="editSejour" autocomplete="true,1,50,true,true" style="width: 12em;"}}
        <input type="hidden" name="cancelled" value="0" />
      </div>
      
      <div id="lit_sortie_transfert" {{if $sejour->mode_sortie != "mutation"}} style="display:none;" {{/if}} >
        <select name="lit_id" style="width: 15em;" onchange="modifLits(this.value);this.form.sortie_reelle.value = '';">
          <option value="0">&mdash; Choisir Lit </option>
          {{foreach from=$blocages_lit item=blocage_lit}}
            <option id="{{$blocage_lit->_ref_lit->_guid}}" value="{{$blocage_lit->lit_id}}" class="{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->_guid}}-{{$blocage_lit->_ref_lit->_ref_chambre->_ref_service->nom}}">
              {{$blocage_lit->_ref_lit->_view}}
            </option>
          {{/foreach}}
        </select>
      </div>
    {{else}}
      {{mb_value object=$sejour field=mode_sortie}}
    {{/if}}    
  </td>
  <th><strong>{{mb_label object=$sejour field=confirme}}</strong></th>
  <td><strong>{{mb_value object=$sejour field=confirme}}</strong></td>
</tr>
{{/if}}

<tr id="correspondant_medical">
  {{assign var="object" value=$sejour}}
  {{mb_include template="inc_check_correspondant_medical"}}
</tr>
<tr>
  <td></td>
  <td colspan="3">
    <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
      {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
    </div>
  </td>
</tr>

{{if $count_etab_externe}}
<tr>
  <th>{{mb_label object=$sejour field=etablissement_entree_id}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field=etablissement_entree_id form="editSejour" autocomplete="true,1,50,true,true"}}
  </td>
</tr>
{{/if}}


{{if $conf.dPplanningOp.CSejour.show_discipline_tarifaire}}
<tr>
  <th>{{mb_label object=$sejour field=discipline_id}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field=discipline_id form="editSejour" autocomplete="true,1,50,true,true"}}
  </td>
</tr>
{{/if}}

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
</tbody>

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
  <td colspan="2">
    <button type="button" class="new" onclick="popRegimes()">Régime alimentaire</button>
  </td>
  {{else}}
  <th class="modeExpert">{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td class="modeExpert">{{mb_field object=$sejour field="repas_sans_sel"}}</td>
  {{/if}}
</tr>
<tbody class="modeExpert">
{{if $prestations}}
<tr>
<th>{{mb_label object=$sejour field=prestation_id}}</th>
  <td colspan="3">
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
{{if $sejour->_id && $count_prestations}}
<tr>
  <td></td>
  <td colspan="3">
    <button type="button" class="search" onclick="editPrestations('{{$sejour->_id}}')">Prestations</button>
  </td>
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

<tr>
  <th>{{mb_label object=$sejour field="forfait_se"}}</th>
  <td>{{mb_field object=$sejour field="forfait_se"}}</td>
  <th>{{mb_label object=$sejour field="forfait_sd"}}</th>
  <td>{{mb_field object=$sejour field="forfait_sd"}}</td>
</tr>
{{/if}}
</tbody>
<tr>
  <td class="text">{{mb_label object=$sejour field="convalescence"}}</td>
  <td class="text" colspan="3">{{mb_label object=$sejour field="rques"}}</td>
</tr>
<tr>
  <td>
    {{mb_field object=$sejour field="convalescence" rows="3" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}
  </td>
  <td colspan="3">
    {{mb_field object=$sejour field="rques" rows="3" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}
  </td>
</tr>

{{if $conf.dPplanningOp.CSejour.assurances}}
<tr>
  <th colspan="4" class="category">Assurance</th>
</tr>
<tr>
  <th>{{mb_label object=$sejour field="assurance_maladie"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="assurance_maladie" form="editSejour" style="width: 12em" autocomplete="true,1,50,true,true" onchange="checkAssurances();"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$sejour field="rques_assurance_maladie"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="rques_assurance_maladie" onchange="checkAssurances();" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$sejour field="assurance_accident"}}</th>
  <td colspan="3">{{mb_field object=$sejour field="assurance_accident" form="editSejour" style="width: 12em" autocomplete="true,1,50,true,true" onchange="checkAssurances();"}}</td>
</tr>
<tr>
  <th>{{mb_label object=$sejour field="rques_assurance_accident"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="rques_assurance_accident" onchange="checkAssurances();" form="editSejour"
        aidesaisie="validateOnBlur: 0"}}</td>
</tr>
{{/if}}

<tbody class="modeExpert">
{{if !$sejour->_id && array_key_exists("dPprescription", $modules)}}
<tr>
  <th>{{tr}}CProtocole-protocole_prescription_chir_id{{/tr}}</th>
  <td colspan="3">
    <script type="text/javascript">
    Main.add(function(){
      var form = getForm("editSejour");
      var url = new Url("dPprescription", "httpreq_vw_select_protocole");
      var autocompleter = url.autoComplete(form.libelle_protocole, 'protocole_auto_complete', {
        minChars: 2,
        dropdown: true,
        width: "250px",
        updateElement: function(selectedElement) {
          var node = $(selectedElement).down('.view');
          $V(form.libelle_protocole, node.innerHTML.replace("&lt;", "<").replace("&gt;",">"));
          if (autocompleter.options.afterUpdateElement)
            autocompleter.options.afterUpdateElement(autocompleter.element, selectedElement);
        },
        callback: function(input, queryString){
          return (queryString + "&praticien_id=" + $V(form.praticien_id));
        },
        valueElement: form.elements._protocole_prescription_chir_id
      });
    });
    </script>
    
    <input type="text" name="libelle_protocole" class="autocomplete str" value=""/>
    <div style="display:none; width: 150px;" class="autocomplete" id="protocole_auto_complete"></div>
    <input type="hidden" name="_protocole_prescription_chir_id" />
  </td>
</tr>
{{/if}}
</tbody>


{{if !$mode_operation}}
<tr>
  <td class="button text" colspan="4">
  {{if $sejour->sejour_id}}
  {{if !$sejour->sortie_reelle || $can->admin}}
    <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
    {{mb_ternary var=annule_text test=$sejour->annule value="Restore" other="Cancel"}}
    {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
    <button class="{{$annule_class}}" type="button" onclick="cancelSejour();">
      {{tr}}{{$annule_text}}{{/tr}}
    </button>
    {{if !$conf.dPplanningOp.CSejour.delete_only_admin || $can->admin}}
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|smarty:nodefaults|JSAttribute}}'});">
        {{tr}}Delete{{/tr}}
      </button>
    {{/if}}
    
    <button class="print" type="button" onclick="printFormSejour();">{{tr}}Print{{/tr}}</button>
  {{else}}
    <div class="big-info">
      Les informations sur le séjour ne peuvent plus être modifiées car <strong>le patient est déjà sorti de l'établissement</strong>.
      Veuillez contacter le <strong>responsable du service d'hospitalisation</strong> pour annuler la sortie ou
      <strong>un administrateur</strong> si vous devez tout de même modifier certaines informations.
    </div>
  {{/if}}
  {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
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
        PrescriptionEditor.register('{{$sejour->_id}}','{{$sejour->_class}}','dhe','{{$sejour->praticien_id}}');
      </script>
    </td>
  </tr>  
</table>
{{/if}}