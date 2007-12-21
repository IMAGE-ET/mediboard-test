{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">

function verifNonEmpty(oElement){
  var notWhitespace = /\S/;
  if(notWhitespace.test(oElement.value)){
    return true;
  }
  return false;
}

function pageMain() {
  regFieldCalendar("editRpu", "_entree", true);
  
  {{if $rpu->_id && $can->edit}}
    reloadDossierMedicalPatient();
  {{/if}}
  
  if (document.editAntFrm){
    document.editAntFrm.type.onchange();
    Try.these(document.editAddictFrm.type.onchange);
  } 
  

}

</script>

<form name="editRpu" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPurgences" />
<input type="hidden" name="dosql" value="do_rpu_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
<input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
<input type="hidden" name="_bind_sejour" value="1" />
<a class="buttonnew" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
  Ajouter un patient
</a>
<table class="form">
  <tr>
    {{if $rpu->_id}}
    <th class="title modify" colspan="4">
    <a style="float:right;" href="#" onclick="view_log('CRPU',{{$rpu->_id}})">
      <img src="images/icons/history.gif" alt="historique" />
    </a>
    Modification de l'urgence de {{$rpu->_view}}
    {{if $rpu->_ref_sejour->_num_dossier}}
      [{{$rpu->_ref_sejour->_num_dossier}}]
    {{/if}}
    </th>
    {{else}}
    <th class="title" colspan="4">Création d'une urgence</th>
    {{/if}}
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="_responsable_id"}}</th>
    <td>
      <select name="_responsable_id">
        {{foreach from=$listResponsables item=curr_user}}
        <option value="{{$curr_user->_id}}" {{if $curr_user->_id == $rpu->_responsable_id}}selected="selected"{{/if}}>
          {{$curr_user->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
    
    <th>{{mb_label object=$rpu field="mode_entree"}}</th>
    <td>{{mb_field object=$rpu field="mode_entree" defaultOption="&mdash; Mode d'entrée"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$rpu field="_entree"}}</th>
    <td class="date">{{mb_field object=$rpu field="_entree" form="editRpu"}}</td>
    
    <th>{{mb_label object=$rpu field="provenance"}}</th>
    <td>{{mb_field object=$rpu field="provenance" defaultOption="&mdash; Provenance"}}</td>
  </tr>
  
  <tr>
  <th>
    <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$rpu->_patient_id}}" />
    {{mb_label object=$rpu field="_patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" ondblclick="PatSelector.init()" readonly="readonly" />
   
    <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
    <script type="text/javascript">
      PatSelector.init = function(){
        this.sForm = "editRpu";
        this.sId   = "_patient_id";
        this.sView = "_patient_view";
        this.pop();
      }
    </script>
    </td>
    
    <th>{{mb_label object=$rpu field="transport"}}</th>
    <td>{{mb_field object=$rpu field="transport" defaultOption="&mdash; Type de transport"}}</td>
  </tr>
  
  <tr>
  {{if $can->edit}}
    <th>{{mb_label object=$rpu field="ccmu"}}</th>
    <td>{{mb_field object=$rpu field="ccmu" defaultOption="&mdash; Degré d'urgence"}}</td>
  {{/if}}
    <th>{{mb_label object=$rpu field="prise_en_charge"}}</th>
    <td>{{mb_field object=$rpu field="prise_en_charge" defaultOption="&mdash; Prise en charge"}}</td>
  </tr>
  
  {{if $can->edit}}
  <tr>
    <th>{{mb_label object=$rpu field="diag_infirmier"}} <br />
     <select name="_helpers_diag_infirmier" size="1" onchange="pasteHelperContent(this);">
        <option value="">&mdash; Choisir une aide</option>
        {{html_options options=$rpu->_aides.diag_infirmier.no_enum}}
     </select><br />
     <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CRPU', this.form.diag_infirmier)">{{tr}}New{{/tr}}</button><br />
      </th> 
    <td colspan="3">
   
      <!-- Aide a la saisie -->
     {{mb_field object=$rpu field="diag_infirmier"}}
      </td>
  </tr>
  {{/if}}
  
  <tr>
		<td class="button" colspan="4">
		  {{if $rpu->_id}}
		  <button class="modify" type="submit">Valider</button>
		  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'urgence ',objName:'{{$rpu->_view|smarty:nodefaults|JSAttribute}}'})">
		    Supprimer
		  </button>
	   {{else}}
	   <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
  </tr>
</table>
</form>

<!-- Dossier Médical du patient -->
{{if $rpu->_id && $can->edit}}
<table width="100%" class="form">
  <tr id="ant-trigger">
    <th colspan="6" class="category">
     Dossier Médical du patient
    </th>
  </tr>
  <tbody id="ant">
    <tr class="script">
      <td>
        <script type="text/javascript">new PairEffect("ant");</script>
      </td>
    </tr>
    <tr>
      <td>
        {{assign var="current_m" value="dPurgences"}}
        {{assign var="_is_anesth" value="0"}}
        {{assign var="consult" value=$rpu->_ref_consult}}
        {{include file="../../dPcabinet/templates/inc_ant_consult.tpl"}}
      </td>
    </tr>
  </tbody>
</table>
{{/if}}