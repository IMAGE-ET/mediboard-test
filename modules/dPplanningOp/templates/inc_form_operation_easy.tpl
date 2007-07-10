<!-- $Id: $ -->
{{mb_include_script module="dPplanningOp" script="plage_selector"}}
{{mb_include_script module="dPpatients" script="pat_selector"}}


<form name="editOpEasy" action="?m={{$m}}" method="post" onsubmit="return checkFormOperation()">
<table class="form">
  <!-- Selection du chirurgien -->
  <tr>
    <th>
      {{mb_label object=$op field="chir_id"}}
    </th>
    <td colspan="2">
      <select name="chir_id" class="{{$op->_props.chir_id}}" onchange="synchroPrat(); Value.synchronize(this); removePlageOp(true);">
        <option value="">&mdash; Choisir un chirurgien</option>
        {{foreach from=$listPraticiens item=curr_praticien}}
        <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}" {{if $chir->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  
  <!-- Affichage du libelle -->
  <tr>
    <th>{{mb_label object=$op field="libelle"}}</th>
    <td  class="readonly" colspan="2">{{mb_field object=$op field="libelle" readonly="readonly"}}</td>
  </tr>
  
  
  <!-- Liste des codes ccam -->
  <tr>
    <th>Liste des codes CCAM
    {{mb_field object=$op field="codes_ccam" onchange="refreshListCCAM('easy');" hidden=1 prop=""}}
    </th>
    <td colspan="2" class="text" id="listCodesCcamEasy">
  </td>
  </tr>
  
  
    
  <!-- Selection du coté --> 
  <tr>
    <th>{{mb_label object=$op field="cote"}}</th>
    <td colspan="2">
      {{mb_field object=$op field="cote" defaultOption="&mdash; Choisir un côté" onchange="Value.synchronize(this); modifOp();"}}
    </td>
  </tr> 


  <!-- Selection de la date -->
  {{if $modurgence}}
  <tr>
    <th>
      {{mb_label object=$op field="date"}}
    </th>
    <td>
      <input type="hidden" name="plageop_id" value="" />
      <input type="hidden" name="_date" value="" />
      <input type="hidden" name="_datestr" value="" />
      <select name="date" onchange="{{if !$op->operation_id}}updateEntreePrevue();{{/if}} Value.synchronize(this); modifSejour()">
        {{if $op->operation_id}}
        <option value="{{$op->date}}" selected="selected">
          Inchangée ({{$op->date|date_format:"%d/%m/%Y"}} )
        </option>
        {{/if}}
        <option value="{{$today}}">
          {{$today|date_format:"%d/%m/%Y"}} (aujourd'hui)
        </option>
        <option value="{{$tomorow}}">
          {{$tomorow|date_format:"%d/%m/%Y"}} (demain)
        </option>
      </select>
    </td>
    <td>
      à
      <select name="_hour_urgence" onchange="Value.synchronize(this)">
      {{foreach from=$hours_urgence|smarty:nodefaults item=hour}}
        <option value="{{$hour}}" {{if $op->_hour_urgence == $hour || (!$op->operation_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
      {{/foreach}}
      </select> h
      <select name="_min_urgence" onchange="Value.synchronize(this);">
      {{foreach from=$mins_duree|smarty:nodefaults item=min}}
        <option value="{{$min}}" {{if $op->_min_urgence == $min}}selected="selected"{{/if}}>{{$min}}</option>
      {{/foreach}}
      </select> mn
    </td>
  </tr>
  
  {{else}}
  <tr>
    <th>
      <input type="hidden" name="plageop_id" class="notNull {{$op->_props.plageop_id}}" ondblclick="PlageOpSelector.init()" value="{{$plage->plageop_id}}" />
      {{mb_label object=$op field="plageop_id"}}
      <input type="hidden" name="date" value="" />
      <input type="hidden" name="_date" value="{{$plage->date}}" />
    </th>
    <td class="readonly">
      <input type="text" name="_datestr" readonly="readonly" size="10" ondblclick="PlageOpSelector.init()" value="{{$plage->date|date_format:"%d/%m/%Y"}}" />
    </td>
    <td class="button">
      <button type="button" class="search" onclick="PlageOpSelector.init()">Choisir une date</button>
    </td>
  </tr>
  {{/if}}
  
  <!-- Selection du patient -->
  <tr>
   <th>
    <input type="hidden" name="patient_id" class="notNull {{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$patient->patient_id}}" onchange="bChangePat = 1;" />
    {{mb_label object=$sejour field="patient_id"}}
   </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" ondblclick="PatSelector.init()" readonly="readonly" />
  </td>
  <td colspan="2" class="button">
  	<button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
   </td>
  </tr>
</table>
</form>