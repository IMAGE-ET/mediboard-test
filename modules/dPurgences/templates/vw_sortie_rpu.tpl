{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
  setInterval(refreshCanLeaveSince, 60000);
});

function modeSortieDest(mode_sortie, rpu_id) {
  var oFormRPU = document.forms["editRPU-" + rpu_id]; 
  
  // Recuperation du tableau de contrainte modeSortie/Destination en JSON
  var contrainteDestination = {{$contrainteDestination|@json}};
 
  if(mode_sortie == ""){
    $A(oFormRPU.destination).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteDestination[mode_sortie]){
    $A(oFormRPU.destination).each( function(input) {
      input.disabled = true;
    });
    return;
  }
  
  var _contrainteDestination = contrainteDestination[mode_sortie];
  $A(oFormRPU.destination).each( function(input) {
    input.disabled = !_contrainteDestination.include(input.value);
  });
}

function modeSortieOrient(mode_sortie, rpu_id){
  var oFormRPU = document.forms["editRPU-" + rpu_id]; 
  
  // Recuperation du tableau de contrainte modeSortie/Orientation en JSON
  var contrainteOrientation = {{$contrainteOrientation|@json}}
  
  if(mode_sortie == ""){
    $A(oFormRPU.orientation).each( function(input) {
      input.disabled = false;
    });
    return;
  }
  
  if(!contrainteOrientation[mode_sortie]){
    $A(oFormRPU.orientation).each( function(input) {
      input.disabled = true;
    });
    return;
  }
  
  var _contrainteOrientation = contrainteOrientation[mode_sortie];
  $A(oFormRPU.orientation).each( function(input) {
    input.disabled = !_contrainteOrientation.include(input.value);
  });
}

function loadTransfert(mode_sortie, sejour_id){
  if(mode_sortie=="transfert"){
	  $('etablissement_sortie_transfert_'+sejour_id).setVisibility(true);
  } else {
	  $('etablissement_sortie_transfert_'+sejour_id).setVisibility(false);
  }
}

function loadServiceMutation(mode_sortie, sejour_id){
  if(mode_sortie=="mutation"){
    $('service_sortie_transfert_'+sejour_id).setVisibility(true);
  } else {
    $('service_sortie_transfert_'+sejour_id).setVisibility(false);
  }
}

function initFields(rpu_id,sejour_id, mode_sortie){
  var oForm = document.forms['editRPU-'+rpu_id];
  oForm.destination.value = '';
  oForm.orientation.value = ''; 
  modeSortieDest(mode_sortie, rpu_id); 
  modeSortieOrient(mode_sortie, rpu_id); 
  loadTransfert(mode_sortie, sejour_id);
	loadServiceMutation(mode_sortie, sejour_id);
}

function refreshTime(rpus) {
  $H(rpus).each(function(s) {
    $("rpu-"+s.key).className = "";
    $("rpu-"+s.key).addClassName(s.value.alert).update(s.value.value);
  });
}

function refreshCanLeaveSince() {
  var url = new Url("dPurgences", "ajax_reload_can_leave_since");
  {{foreach from=$listSejours item=_sejour}}
    url.addParam("rpus[{{$_sejour->_ref_rpu->_id}}]", {{$_sejour->_ref_rpu->_id}});
  {{/foreach}}
  url.requestJSON(refreshTime);
}

function validCotation(consutation_id) {
  return onSubmitFormAjax(getForm('validCotation-'+consutation_id));
}

refreshSortie = function(button, rpu_id){
  var line = button.up('tr').up('tr');
  var url = new Url("dPurgences", "ajax_refresh_sortie");
  url.addParam("rpu_id", rpu_id);
  url.requestUpdate(line);
}

function filterPatient(input) {
  $$("#list-sorties tr").invoke("show");
  
  var term = $V(input);
  if (!term) return;
  
  $$("#list-sorties .CPatient-view").each(function(p) {
    if (!p.innerHTML.like(term)) {
      p.up("tr").hide();
    }
  });
}

// Fonction appelée dans inc_vw_etab_externe qui submit le sejour dans le cas de "inc_vw_rpu.tpl"
// Dans la sortie, on ne veut pas déclencher de submit
function submitSejour(){
 // Ne rien faire
}

</script>

<table class="main">
  <tr>
    <th>
      <div style="float: right;">
        Type d'affichage
        <form name="selView" action="?m=dPurgences&amp;tab=vw_sortie_rpu" method="post">
          <select name="aff_sortie" onchange="submit();">
            <option value="tous" {{if $aff_sortie == "tous"}}selected = "selected"{{/if}}>Tous</option>
            <option value="sortie" {{if $aff_sortie == "sortie"}} selected = "selected" {{/if}}>Sortie à effectuer</option>
          </select>
        </form>
      </div>
      
      <big>{{$date|date_format:$dPconfig.longdate}}</big>
      
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  </tr>
</table>

<table class="tbl" id="list-sorties">
  <tr>
    <th>{{mb_title class=CRPU field="_patient_id"}}</th>
    <th style="width: 0.1%;"><input type="text" onkeyup="filterPatient(this)" id="filter-patient-name" size="6" /></th>
    {{if $dPconfig.dPurgences.responsable_rpu_view}}
      <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th>Prise en charge</th>
    <th>{{mb_title class=CRPU field="rpu_id"}}</th>
    <th>{{mb_title class=CRPU field="sejour_id"}}</th>
    <th>{{mb_title class=CRPU field="_can_leave"}}</th>
  </tr>
  {{foreach from=$listSejours item=sejour}}
    {{assign var=rpu value=$sejour->_ref_rpu}}
    {{assign var=patient value=$sejour->_ref_patient}}
    <tr>
      {{mb_include module=dPurgences template=inc_sortie_rpu}}
    </tr>
  {{foreachelse}}
	  <tr><td colspan="10"><em>Aucune sortie à effectuer</em></td></tr>
  {{/foreach}}
</table>