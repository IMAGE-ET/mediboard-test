<script type="text/javascript">
// Notification de l'arrivée du patient
if (!window.updateConsultations) {
  updateConsultations = function() {
  	window.location.reload();
  }
}

putArrivee = function(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  submitFormAjax(oForm, 'systemMsg', { onComplete: updateConsultations } );
}
</script>


{{if !$board}}
{{if $canCabinet->view}}
<script type="text/javascript">
Main.add( function () {
  Calendar.regField(getForm("changeView").date, null, {noView: true});
} );
</script>
{{/if}}
<form name="changeView" action="?" method="get">
  <input type="hidden" name="m" value="{{$current_m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
      <td colspan="6" style="text-align: left; width: 100%; font-weight: bold; height: 20px;">
        <div style="float: right;">{{$hour|date_format:$dPconfig.time}}</div>
        {{$date|date_format:$dPconfig.longdate}}
        {{if $canCabinet->view}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        {{/if}}
      </td>
    </tr>
    <tr>
      {{if $canCabinet->view}}
      <th><label for="vue2" title="Type de vue du planning">Type de vue</label></th>
      <td colspan="5">
        <select name="vue2" onchange="this.form.submit()">
          <option value="0"{{if $vue == "0"}}selected="selected"{{/if}}>Tout afficher</option>
          <option value="1"{{if $vue == "1"}}selected="selected"{{/if}}>Cacher les Terminées</option>
        </select>
      </td>
      {{/if}}
    </tr>
  </table>
</form>
{{/if}}

{{if $boardItem}}
{{assign var="font" value="font-size: 9px;"}} 
<table class="tbl">
{{elseif $board}}
{{assign var="font" value="font-size: 100%;"}} 
<table class="tbl">
{{else}}
{{assign var="font" value="font-size: 9px;"}} 
<table class="tbl" style="width: 250px">
{{/if}}

  <tr>
    <th class="title" colspan="3">Consultations</th>
  </tr>

  <tr>
    <th>Heure</th>
    <th colspan="2">Patient / Motif</th>
  </tr>
  
  {{foreach from=$listPlage item=_plage}}
  {{if count($_plage->_ref_consultations)}}
  <tr>
    <th colspan="3">
    	{{$_plage->debut|date_format:$dPconfig.time}} 
    	- {{$_plage->fin|date_format:$dPconfig.time}}
    	{{if $_plage->libelle}}: {{$_plage->libelle}}{{/if}}
    </th>
  </tr>
  {{/if}}
  {{foreach from=$_plage->_ref_consultations item=_consult}}
  {{if !$_consult->patient_id}}
    {{assign var="style" value="background: #ffa;"}}          
  {{elseif $_consult->premiere}} 
    {{assign var="style" value="background: #faa;"}}
	{{elseif $_consult->_ref_sejour->_id}} 
    {{assign var="style" value="background: #CFFFAD;"}}
  {{else}} 
    {{assign var="style" value=""}}
  {{/if}}
  <tbody class="hoverable">
  <tr {{if $_consult->_id == $consult->_id}}class="selected"{{/if}}>
    <td style="width: 42px; {{if $_consult->_id != $consult->_id}}{{$style|smarty:nodefaults}}{{/if}}{{$font|smarty:nodefaults}}" rowspan="2" class="text">
      {{if $canCabinet->view}}
        <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id={{$_consult->_id}}" title="Modifier le RDV" style="float: right;">
          <img src="images/icons/planning.png" alt="modifier" />
        </a>
      {{else}}
        <a href="#nowhere" title="Impossible de modifier le RDV"></a>
      {{/if}}
      
      
      {{if $_consult->patient_id}}
      {{if $canCabinet->view}}
        <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}" style="margin-bottom: 4px;">
      {{else}}
        <a href="#nowhere" title="Impossible de modifier le RDV">
      {{/if}}
           {{$_consult->heure|truncate:5:"":true}}
        </a>
      {{else}}
        {{$_consult->heure|truncate:5:"":true}}
      {{/if}}
      {{if $_consult->patient_id}}
        {{if ($_consult->chrono == $_consult|const:'PLANIFIE')}}
		      <form name="etatFrm{{$_consult->_id}}" action="?m={{$current_m}}" method="post">
			      <input type="hidden" name="m" value="dPcabinet" />
			      <input type="hidden" name="dosql" value="do_consultation_aed" />
			      {{mb_field object=$_consult field="consultation_id" hidden=1 prop=""}}
			      <input type="hidden" name="chrono" value="{{$_consult|const:'PATIENT_ARRIVE'}}" />
			      <input type="hidden" name="arrivee" value="" />
		      </form>

          <a style="white-space: nowrap;" href="#" onclick="putArrivee(document.etatFrm{{$_consult->_id}})">
            <img src="images/icons/check.png" title="Notifier l'arrivée du patient" alt="arrivee" />
            {{$_consult->_etat}}
          </a>
        {{else}}
          {{$_consult->_etat}}
        {{/if}}
      {{/if}}
    </td>
    <td style="{{$style|smarty:nodefaults}}{{$font|smarty:nodefaults}}">
      {{if $_consult->patient_id}}
      {{if $canCabinet->view}}
      <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}">
      {{else}}
      <a href=#nowhere title="Impossible de modifier le RDV">
      {{/if}}
        {{$_consult->_ref_patient->_view|truncate:30:"...":true}}
        {{if $_consult->_ref_patient->_age != "??"}}
          ({{$_consult->_ref_patient->_age}}&nbsp;ans)
      </a>
      {{/if}}
      {{else}}
        [PAUSE]
      {{/if}}
    </td>
    <td rowspan="2">
    	{{assign var=categorie value=$_consult->_ref_categorie}}
      {{if $categorie && $categorie->nom_icone}}
        <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" alt="{{$categorie->nom_categorie}}" title="{{$categorie->nom_categorie}}"/>
      {{/if}}
    </td>
  </tr>
  <tr {{if $_consult->_id == $consult->_id}}class="selected"{{/if}}>
    <td style="{{$style|smarty:nodefaults}}{{$font|smarty:nodefaults}}">
      {{if $_consult->patient_id}}
      {{if $canCabinet->view}}
        <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}">
      {{else}}
        <a href="#nowhere" title="Impossible de modifier le RDV">
      {{/if}}
          {{$_consult->motif|truncate:40:"...":true}}
        </a>
      {{else}}
        {{$_consult->motif|truncate:40:"...":true}}
      {{/if}}
            
      {{assign var=prat_id value=$_plage->chir_id}}
      
      {{if @count($listPlages.$prat_id.destinations) && $canCabinet->edit}}
      <form name="ChangePlage-{{$_consult->_guid}}" action="?m={{$current_m}}" method="post">
      
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      
      {{mb_key object=$_consult}}
      
      <select name="plageconsult_id" onchange="this.form.submit();" style="font-size: 9px; width: 150px">
        <option value="" style="font-size: 9px;">
          &mdash; Changer de praticien
        </option>
        {{foreach from=$listPlages.$prat_id.destinations item=destination}}
        <option value={{$destination->_id}} style="font-size: 9px;">
          {{$destination->_ref_chir->_view}}
		    	: {{$destination->debut|date_format:$dPconfig.time}} 
		    	- {{$destination->fin|date_format:$dPconfig.time}}
		    	{{if $destination->libelle}} - {{$destination->libelle}}{{/if}}
        </option>
        {{/foreach}}
      </select>

      </form>
      {{/if}}
    </td>
  </tr>
  </tbody>
  {{/foreach}}
  
  {{foreachelse}}
  <tr>
    <th colspan="3" style="font-weight: bold;">{{tr}}CPlageconsult.none{{/tr}}</th>
  </tr>
  {{/foreach}}
</table>