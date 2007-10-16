{{if !$board}}
{{if $canCabinet->view}}
<script type="text/javascript">
  regRedirectPopupCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
</script>
{{/if}}
<form name="changeView" action="index.php" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
      <td colspan="6" style="text-align: center; width: 100%; font-weight: bold;">
        <div style="float: right;">{{$hour|date_format:"%Hh%M"}}</div>
        {{$date|date_format:"%A %d %B %Y"}}
        {{if $canCabinet->view}}
        <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
        {{/if}}
      </td>
    </tr>
    <tr>
      <th>
      {{if $canCabinet->view}}
      <label for="vue2" title="Type de vue du planning">Type de vue</label></th>
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
{{if $listPlage|@count}}
{{foreach from=$listPlage item=curr_plage}}
  <tr>
    <th colspan="3">{{$curr_plage->debut|date_format:"%Hh%M"}} - {{$curr_plage->fin|date_format:"%Hh%M"}}</th>
  </tr>
  {{foreach from=$curr_plage->_ref_consultations item=curr_consult}}
  {{if !$curr_consult->patient_id}}
    {{assign var="style" value="background: #ffa;"}}          
  {{elseif $curr_consult->premiere}} 
    {{assign var="style" value="background: #faa;"}}
  {{else}} 
    {{assign var="style" value=""}}
  {{/if}}
  <tbody class="hoverable">
  <tr {{if $curr_consult->_id == $consult->_id}}class="selected"{{/if}}>
    <td style="width: 42px; {{if $curr_consult->_id != $consult->_id}}{{$style|smarty:nodefaults}}{{/if}}{{$font|smarty:nodefaults}}" rowspan="2">
      {{if !$boardItem}}
      {{if $canCabinet->view}}
      <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id={{$curr_consult->_id}}" title="Modifier le RDV" style="float: right;">
        <img src="images/icons/planning.png" alt="modifier" />
      {{else}}
      <a href="#nowhere" title="Impossible de modifier le RDV">
      {{/if}}
      </a>
      {{/if}}
      {{if $curr_consult->patient_id}}
      {{if $canCabinet->view}}
        <a href="?m={{$m}}&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}" style="margin-bottom: 4px;">
      {{else}}
       <a href="#nowhere" title="Impossible de modifier le RDV">
      {{/if}}
           {{$curr_consult->heure|truncate:5:"":true}}
        </a>
      {{else}}
        {{$curr_consult->heure|truncate:5:"":true}}
      {{/if}}
      {{if $curr_consult->patient_id}}
        {{$curr_consult->_etat}}
      {{/if}}
    </td>
    <td style="{{$style|smarty:nodefaults}}{{$font|smarty:nodefaults}}">
      {{if $curr_consult->patient_id}}
      {{if $canCabinet->view}}
      <a href="?m={{$m}}&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}">
      {{else}}
      <a href=#nowhere title="Impossible de modifier le RDV">
      {{/if}}
        {{$curr_consult->_ref_patient->_view|truncate:30:"...":true}}
        {{if $curr_consult->_ref_patient->_age != "??"}}
          ({{$curr_consult->_ref_patient->_age}}&nbsp;ans)
      </a>
      {{/if}}
      {{else}}
        [PAUSE]
      {{/if}}
    </td>
    <td rowspan="2">
      {{if $curr_consult->_ref_categorie->_id}}
      <img src="./modules/dPcabinet/categories/{{$curr_consult->_ref_categorie->nom_icone}}" alt="{{$curr_consult->_ref_categorie->nom_categorie}}" title="{{$curr_consult->_ref_categorie->nom_categorie}}"/>
      {{/if}}
    </td>
  </tr>
  <tr {{if $curr_consult->_id == $consult->_id}}class="selected"{{/if}}>
    <td style="{{$style|smarty:nodefaults}}{{$font|smarty:nodefaults}}">
      {{if $curr_consult->patient_id}}
      {{if $canCabinet->view}}
        <a href="?m={{$m}}&amp;tab=edit_consultation&amp;selConsult={{$curr_consult->_id}}">
      {{else}}
      <a href="#nowhere" title="Impossible de modifier le RDV">
      {{/if}}
          {{$curr_consult->motif|truncate:30:"...":true}}
        </a>
      {{else}}
        {{$curr_consult->motif|truncate:30:"...":true}}
      {{/if}}
    </td>
  </tr>
  </tbody>
  {{/foreach}}
{{/foreach}}
{{else}}
  <tr>
    <th colspan="3" style="font-weight: bold;">Pas de consultations</th>
  </tr>
{{/if}}
</table>