<!-- $Id: $ -->

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="if(checkDureeHospi()){return checkForm(this);}else{return false;}">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
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
    {{/if}}
    {{tr}}msg-CSejour-informations{{/tr}}
  </th>
</tr>

{{if $sejour->annule == 1}}
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
    <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="popPat()" value="{{$patient->patient_id}}" />
    {{mb_label object=$sejour field="patient_id"}}
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" ondblclick="popPat()" readonly="readonly" />
  </td>
  <td colspan="2" class="button">
  	<button type="button" class="search" onclick="popPat()">Choisir un patient</button>
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="DP"}}</th>
  <td>{{mb_field object=$sejour field="DP" size="10"}}</td>
  <td colspan="2" class="button"><button type="button" class="search" onclick="popCode('cim10')">{{tr}}button-CCodeCIM10-choix{{/tr}}</button></td>
</tr>

<tr>
  <th class="category" colspan="4">Admission</th>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="_date_entree_prevue"}}</th>
  <td class="date">
    <div id="editSejour__date_entree_prevue_da">{{$sejour->_date_entree_prevue|date_format:"%d/%m/%Y"}}</div>
    {{mb_field object=$sejour field="_date_entree_prevue" hidden=1 prop="notNull date" onchange="modifSejour(); updateSortiePrevue();"}}
    <img id="editSejour__date_entree_prevue_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
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
    <div id="editSejour__date_sortie_prevue_da">{{$sejour->_date_sortie_prevue|date_format:"%d/%m/%Y"}}</div>
    {{mb_field object=$sejour field="_date_sortie_prevue" hidden=1 prop="notNull date moreEquals|_date_entree_prevue" onchange="updateDureePrevue(); modifSejour()"}}
    <img id="editSejour__date_sortie_prevue_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
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
  <th>Entrée réelle :</th>
  <td colspan="3">{{$sejour->entree_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>

<tr>
  <th>Sortie réelle :</th>
  <td colspan="3">{{$sejour->sortie_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>
{{/if}}

<tr>
  <th>{{mb_label object=$sejour field="type"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="type"}}
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
  <td>
    {{mb_field object=$sejour field="ATNC"}}
  </td>
  <th>{{mb_label object=$sejour field="hormone_croissance"}}</th>
  <td colspan="2">
    {{mb_field object=$sejour field="hormone_croissance"}}
  </td>  
</tr>

<tr>
  <th>{{mb_label object=$sejour field="chambre_seule"}}</th>
  <td {{if $mode_operation}}colspan="3"{{/if}}>
    {{mb_field object=$sejour field="chambre_seule"}}
  </td>
  {{if !$mode_operation}}
  <th>{{mb_label object=$sejour field="lit_accompagnant"}}</th>
  <td>
    {{mb_field object=$sejour field="lit_accompagnant"}}
  </td>
  {{/if}}
</tr>

{{if !$mode_operation}}
<tr>
  <th>{{mb_label object=$sejour field="repas_sans_sel"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_sel"}}
  </td>
  <th>{{mb_label object=$sejour field="isolement"}}</th>
  <td>
    {{mb_field object=$sejour field="isolement"}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="repas_diabete"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_diabete"}}
  </td>
  <th>{{mb_label object=$sejour field="television"}}</th>
  <td>
    {{mb_field object=$sejour field="television"}}
  </td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="repas_sans_residu"}}</th>
  <td>
    {{mb_field object=$sejour field="repas_sans_residu"}}
  </td>
  <td colspan="2"></td>
</tr>

<tr>
  <th>{{mb_label object=$sejour field="venue_SHS"}}</th>
  <td colspan="3">
    {{mb_field object=$sejour field="venue_SHS" size="8" maxlength="8"}}
  </td>
</tr>
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
    {{if $sejour->annule == "0"}}
      {{assign var="annule_text" value="Annuler"}}
      {{assign var="annule_class" value="cancel"}}
    {{/if}}
    {{if $sejour->annule == "1"}}
      {{assign var="annule_text" value="Rétablir"}}
      {{assign var="annule_class" value="change"}}
    {{/if}}
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
regFieldCalendar("editSejour", "_date_entree_prevue");
regFieldCalendar("editSejour", "_date_sortie_prevue");
removePlageOp(false);
</script>
