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
          <option value="0" {{if $vue == "0"}}selected="selected"{{/if}}>Tout afficher</option>
          <option value="1" {{if $vue == "1"}}selected="selected"{{/if}}>Cacher les terminées</option>
        </select>
      </td>
      {{/if}}
    </tr>
  </table>
</form>
{{/if}}

<table class="tbl" style="{{if !@$offline}}font-size: 9px;{{/if}} {{if @$fixed_width|default:0}}width: 250px{{/if}}">
  <tr>
    <th class="title" colspan="3">Consultations</th>
  </tr>

  <tr>
    <th style="width: 50px; ">Heure</th>
    <th colspan="2">Patient / Motif</th>
  </tr>
  
  {{foreach from=$listPlage item=_plage}}
  {{if count($_plage->_ref_consultations)}}
  <tr>
    <th colspan="3">
      {{mb_include module=system template=inc_object_notes object=$_plage}}
    	{{$_plage->debut|date_format:$dPconfig.time}} 
    	- {{$_plage->fin|date_format:$dPconfig.time}}
    	{{if $_plage->libelle}}: {{$_plage->libelle}}{{/if}}
    </th>
  </tr>
  {{/if}}
  {{foreach from=$_plage->_ref_consultations item=_consult}}
  {{assign var=patient value=$_consult->_ref_patient}}
	
  {{if !$patient->_id}}
    {{assign var="style" value="background: #ffa;"}}          
  {{elseif $_consult->premiere}} 
    {{assign var="style" value="background: #faa;"}}
	{{elseif $_consult->_ref_sejour->_id}} 
    {{assign var="style" value="background: #cfa;"}}
  {{else}} 
    {{assign var="style" value=""}}
  {{/if}}

  {{assign var=prat_id value=$_plage->chir_id}}
  
	{{assign var=destinations value=""}}
  {{if @count($listPlages.$prat_id.destinations) && $canCabinet->edit}}
  {{assign var=destinations value=$listPlages.$prat_id.destinations}}
  {{/if}}

  <tbody class="hoverable">

  <tr {{if $_consult->_id == $consult->_id}}class="selected"{{/if}}>
    {{assign var=categorie value=$_consult->_ref_categorie}}
    <td {{if $_consult->annule}}class="cancelled"{{/if}} style="{{if $_consult->_id != $consult->_id}}{{$style}}{{/if}}" {{if $destinations || $_consult->motif}}rowspan="2"{{/if}} class="text">
      {{if $canCabinet->view && !@$offline}}
        <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id={{$_consult->_id}}" title="Modifier le RDV" style="float: right;">
          <img src="images/icons/planning.png" title="{{tr}}Edit{{/tr}}" />
        </a>
      {{/if}}
      
      {{if $patient->_id}}
      {{if $canCabinet->view && !@$offline}}
        <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}" style="margin-bottom: 4px;">
      {{else}}
        <a href="#1" title="Impossible de modifier le RDV">
      {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
           {{$_consult->heure|truncate:5:"":true}}
          </span>
        </a>
      {{else}}
        {{$_consult->heure|truncate:5:"":true}}
      {{/if}}
      {{if $patient->_id}}
        {{if ($_consult->chrono == $_consult|const:'PLANIFIE') && !@$offline}}
		      <form name="etatFrm{{$_consult->_id}}" action="?m={{$current_m}}" method="post">
			      <input type="hidden" name="m" value="dPcabinet" />
			      <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$_consult}}
			      <input type="hidden" name="chrono" value="{{$_consult|const:'PATIENT_ARRIVE'}}" />
			      <input type="hidden" name="arrivee" value="" />
		      </form>

          <a style="white-space: nowrap;" href="#1" onclick="putArrivee(document.etatFrm{{$_consult->_id}})">
            <img src="images/icons/check.png" title="Notifier l'arrivée du patient" />
            {{$_consult->_etat}}
          </a>
        {{else}}
          {{$_consult->_etat}}
        {{/if}}
      {{/if}}
    </td>
		
    <td class="text" style="{{$style}}" {{if !$categorie->_id}}colspan="2"{{/if}}>
      {{if $patient->_id}}
        {{if @$offline}}
          <div id="{{$patient->_guid}}-dossier" style="display: none; min-width: 600px;">
            <button class="print not-printable" onclick="modalWindow.print()">{{tr}}Print{{/tr}}</button>
            <button class="cancel not-printable" onclick="modalWindow.close();" style="float: right;">{{tr}}Close{{/tr}}</button>
            
            {{assign var=patient_id value=$patient->_id}}
            {{$patients_fetch.$patient_id|smarty:nodefaults}}
          </div>
          
          <a href="#1" onclick="modalWindow = modal($('{{$patient->_guid}}-dossier'))">
	      {{elseif $canCabinet->view}}
	        <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}">
	      {{else}}
	        <a href=#1 title="Impossible de modifier le RDV">
	      {{/if}}
        
				  <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
		        {{$patient->_view|truncate:30:"...":true}}
		        {{if $patient->_age != "??"}}
		          ({{$patient->_age}}&nbsp;ans)
	          {{/if}}
				  </strong>
	      </a>
      {{else}}
        [PAUSE]
      {{/if}}
    </td>

    {{if $categorie->nom_icone}}
    <td {{if $destinations || $_consult->motif}}rowspan="2"{{/if}} style="{{$style}}" class="narrow">
      <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" title="{{$categorie->nom_categorie}}" />
    </td>
    {{/if}}
  </tr>
	
  {{if $destinations || $_consult->motif}}
  <tr {{if $_consult->_id == $consult->_id}}class="selected"{{/if}}>
    <td class="text" style="{{$style}}" {{if !$categorie->_id}}colspan="2"{{/if}}>
      {{assign var=prat_id value=$_plage->chir_id}}
      
      {{if $destinations && !@$offline}}
      <form name="ChangePlage-{{$_consult->_guid}}" action="?m={{$current_m}}" method="post" style="float: right;">
      
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      
      {{mb_key object=$_consult}}
      
      <select name="plageconsult_id" onchange="this.form.submit();" style="font-size: 9px; width: 80px">
        <option value="">
          &mdash; Transférer
        </option>
        {{foreach from=$destinations item=destination}}
        <option value={{$destination->_id}}>
          {{$destination->_ref_chir->_view}}
          : {{$destination->debut|date_format:$dPconfig.time}} 
          - {{$destination->fin|date_format:$dPconfig.time}}
          {{if $destination->libelle}} - {{$destination->libelle}}{{/if}}
        </option>
        {{/foreach}}
      </select>

      </form>
      {{/if}}

      {{if $patient->_id}}
      {{if $canCabinet->view && !@$offline}}
        <a href="?m={{$current_m}}&amp;tab=edit_consultation&amp;selConsult={{$_consult->_id}}">
      {{else}}
        <a href="#1" title="Impossible de modifier le RDV">
      {{/if}}
          {{$_consult->motif|truncate:40:"...":true}}
        </a>
      {{else}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
        {{$_consult->motif|truncate:40:"...":true}}
        </span>
      {{/if}}
    </td>
  </tr>
	{{/if}}
	
	
  </tbody>
  {{/foreach}}
  
  {{foreachelse}}
  <tr>
    <th colspan="3" style="font-weight: bold;">{{tr}}CPlageconsult.none{{/tr}}</th>
  </tr>
  {{/foreach}}
</table>