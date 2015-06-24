{{mb_script module="dPcabinet" script="icone_selector" ajax=true}}

{{mb_default var=mode_vue value=vertical}}
{{assign var=patient value=$_consult->_ref_patient}}
{{assign var=sejour  value=$_consult->_ref_sejour}}

{{if !$patient->_id}}
  {{assign var="style" value="background-color: #ffa;"}}
{{elseif $_consult->premiere}} 
  {{assign var="style" value="background-color: #faa;"}}
{{elseif $_consult->derniere}} 
  {{assign var="style" value="background-color: #faf;"}}
{{elseif $sejour->_id}}
  {{assign var="style" value="background-color: #cfa;"}}
{{else}} 
  {{assign var="style" value=""}}
{{/if}}

{{assign var=prat_id value=$_plage->chir_id}}
{{assign var=consult_id value=$_consult->_id}}

{{assign var=destinations value=""}}
{{if @count($listPlages.$prat_id.destinations) && $canCabinet->edit}}
{{assign var=destinations value=$listPlages.$prat_id.destinations}}
{{/if}}

<tbody class="hoverable">

<tr class="{{if $_consult->_id == $consult->_id}}selected{{/if}}">
  {{assign var=categorie value=$_consult->_ref_categorie}}
  <td class="{{if $_consult->annule}}cancelled{{/if}} {{if $_consult->chrono == $_consult|const:'TERMINE'}}hatching{{/if}}"
      style="{{if $_consult->_id != $consult->_id}}{{$style}}{{/if}}" {{if $destinations || $_consult->motif}}rowspan="2"{{/if}} class="text">
    {{if $destinations && !@$offline && $mode_vue == "horizontal"}}
      <form name="ChangePlage-{{$_consult->_guid}}" action="?m={{$current_m}}" method="post">
        
        <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="m" value="dPcabinet" />
        <input type="hidden" name="del" value="0" />
        
        {{mb_key object=$_consult}}
        
        <select name="plageconsult_id" onchange="this.form.submit();" style="width: 2em;">
          <option value="">
            &mdash; Transférer
          </option>
          {{foreach from=$destinations item=destination}}
          <option value={{$destination->_id}}>
            {{$destination->_ref_chir->_view}}
            : {{$destination->debut|date_format:$conf.time}} 
            - {{$destination->fin|date_format:$conf.time}}
            {{if $destination->libelle}} - {{$destination->libelle}}{{/if}}
          </option>
          {{/foreach}}
        </select>
    
        </form>
        <br/>
    {{/if}}
    {{if $canCabinet->read && !@$offline}}
      <a href="#1" onclick="Consultation.editRDVModal('{{$_consult->_id}}'); return false;" title="Modifier le RDV" {{if $mode_vue == "vertical"}}style="float: right;"{{/if}}>
        <img src="images/icons/planning.png" title="{{tr}}Edit{{/tr}}" />
      </a>
      {{if $mode_vue == "horizontal"}}
        <br />
      {{/if}}
    {{/if}}
    
    {{if $patient->_id}}
    {{if $canCabinet->read && !@$offline}}
      <a href="#1" onclick="Consultation.edit('{{$_consult->_id}}'); return false;" style="margin-bottom: 4px;">
    {{else}}
      <a href="#1" title="Impossible d'accéder à la consultation"> {{if $mode_vue == "horizontal"}}<br />{{/if}}
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
          <img src="images/icons/check.png" title="Notifier l'arrivée du patient" /> {{if $mode_vue == "horizontal"}}<br />{{/if}}
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
        
        <a href="#1" onclick="modalWindow = Modal.open($('{{$patient->_guid}}-dossier'))" style="display: inline-block;">
      {{elseif $canCabinet->edit}}
        <a href="#1" onclick="Consultation.edit('{{$_consult->_id}}'); return false;" style="display: inline-block;">
      {{else}}
        <a href="#1" title="Impossible d'accéder à la consultation" style="display: inline-block;">
      {{/if}}
      
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient->_view|truncate:30:"...":true}}
          {{if $patient->_annees != "??"}}
            ({{$patient->_age}})
          {{/if}}
        </strong>
      </a>
      {{if $sejour->entree_reelle}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">({{$sejour->entree_reelle|date_format:$conf.time}})</span>
      {{/if}}
    {{else}}
      [PAUSE]
    {{/if}}
  </td>

  {{if $categorie->nom_icone}}
    <td {{if $destinations || $_consult->motif}}rowspan="2"{{/if}} style="{{$style}}" class="narrow">
      {{mb_include module=cabinet template=inc_icone_categorie_consult
        categorie=$categorie
        onclick="IconeSelector.changeCategory('$consult_id', this)"}}
    </td>
  {{/if}}

</tr>

{{if $destinations || $_consult->motif}}
<tr {{if $_consult->_id == $consult->_id}}class="selected"{{/if}}>
  <td class="text compact" style="{{$style}}" {{if !$categorie->_id}}colspan="2"{{/if}}>
    
    {{if $destinations && !@$offline && $mode_vue == "vertical"}}
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
        : {{$destination->debut|date_format:$conf.time}} 
        - {{$destination->fin|date_format:$conf.time}}
        {{if $destination->libelle}} - {{$destination->libelle}}{{/if}}
      </option>
      {{/foreach}}
    </select>

    </form>
    {{/if}}
    

    {{if $patient->_id}}
    {{if $canCabinet->edit && !@$offline}}
      <a href="#1" onclick="Consultation.edit('{{$_consult->_id}}'); return false;">
    {{else}}
      <a href="#1" title="Impossible de modifier la consultation">
    {{/if}}
        {{$_consult->motif|spancate:40:"...":true}}
      </a>
    {{else}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
      {{$_consult->motif|spancate:40:"...":true}}
      </span>
    {{/if}}
  </td>
</tr>
{{/if}}


</tbody>