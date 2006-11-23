<!-- $Id: $ -->

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="if(checkDureeHospi()){return checkForm(this);}else{return false;}">

<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
{{if $mode_operation}}
  <input type="hidden" name="callback" value="submitFormOperation" />
{{/if}}
<input type="hidden" name="saisi_SHS" value="{{$sejour->saisi_SHS}}" />
<input type="hidden" name="modif_SHS" value="{{$sejour->modif_SHS}}" />
<input type="hidden" name="annule" value="{{$sejour->annule}}" />
{{if !$mode_operation}}
  <input type="hidden" name="sejour_id" value="{{$sejour->sejour_id}}" />
{{/if}}


<table class="form" onmousemove="checkSejoursToReload()">

<tr>
  <th class="category" colspan="3">
    Informations concernant le séjour
  </th>
</tr>

{{if $sejour->annule == 1}}
<tr>
  <th class="category" colspan="3" style="background: #f00;">
  SEJOUR ANNULE
  </th>
</tr>
{{/if}}

{{if $mode_operation}}
<tr>
  <th>
    Sejours existants
  </th>
  <td colspan="2" id="selectSejours">
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
    <label for="group_id" title="Etablissement ou se déroule le séjour. Obligatoire">Etablissement</label>
  </th>
  <td colspan="2">
    <select title="{{$sejour->_props.group_id}}" name="group_id" onchange="removePlageOp(true);">
    {{foreach from=$etablissements item=curr_etab}}
      <option value="{{$curr_etab->group_id}}" {{if ($sejour->sejour_id && $sejour->group_id==$curr_etab->group_id) || (!$sejour->sejour_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
    {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <label for="praticien_id" title="Praticien responsable. Obligatoire">Praticien</label>
  </th>
  <td colspan="2">
    <select name="praticien_id" title="{{$sejour->_props.praticien_id}}">
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
    <input type="hidden" name="patient_id" title="{{$sejour->_props.patient_id}}" ondblclick="popPat()" value="{{$patient->patient_id}}" />
    <label for="patient_id" title="Patient concerné. Obligatoire">Patient</label>
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" ondblclick="popPat()" readonly="readonly" />
  </td>
  <td class="button">
  	<button type="button" class="search" onclick="popPat()">Choisir un patient</button>
  </td>
</tr>

<tr>
  <th><label for="DP" title="Code CIM du diagnostic principal">Diagnostic principal (CIM)</label></th>
  <td><input type="text" name="DP" title="{{$sejour->_props.DP}}" size="10" value="{{$sejour->DP}}" /></td>
  <td class="button"><button type="button" class="search" onclick="popCode('cim10')">Choisir un code</button></td>
</tr>

<tr>
  <th class="category" colspan="3">Admission</th>
</tr>

<tr>
  <th>
  	<label for="_date_entree_prevue" title="Choisir une date d'entrée">Entrée prévue</label>
  </th>
  <td class="date">
    <div id="editSejour__date_entree_prevue_da">{{$sejour->_date_entree_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_entree_prevue" title="date|notNull" value="{{$sejour->_date_entree_prevue}}" onchange="modifSejour(); updateSortiePrevue();"/>
    <img id="editSejour__date_entree_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
  </td>
  <td>
    à
    <select name="_hour_entree_prevue" onchange="updateHeureSortie()">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
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
  <th>
    <label for="_duree_prevue" title="Choisir une durée prévue de séjour">Durée du séjour</label>
  </th>
  <td>
    <input type="text" name="_duree_prevue" title="num|min|0" value="{{if $sejour->sejour_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
    jours
  </td>
  <td id="dureeEst">
  </td>
</tr>

<tr {{if $mode_operation}} style="display: none" {{/if}}>
  <th>
  	<label for="_date_sortie_prevue" title="Choisir une date de sortie">Sortie prévue</label>
  </th>
  <td class="date">
    <div id="editSejour__date_sortie_prevue_da">{{$sejour->_date_sortie_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_sortie_prevue" title="date|moreEquals|_date_entree_prevue|notNull" value="{{$sejour->_date_sortie_prevue}}" onchange="updateDureePrevue(); modifSejour()" />
    <img id="editSejour__date_sortie_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
  </td>
  <td>
    à 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours|smarty:nodefaults item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
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
  <td colspan="2">{{$sejour->entree_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>

<tr>
  <th>Sortie réelle :</th>
  <td colspan="2">{{$sejour->sortie_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>
{{/if}}

<tr>
  <th><label for="type_ambu" title="Type d'admission">{{tr}}Type d'admission{{/tr}}</label></th>
  <td colspan="2">
    {{if $sejour->sejour_id}}
    {{assign var="checked" value=$sejour->type}}
    {{else}}
    {{assign var="checked" value="ambu"}}
    {{/if}}
    {{html_radios name="type" options=$sejour->_enumsTrans.type separator="<br />" title=$sejour->_props.type checked=$checked}}
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <th>
    <label for="modalite_libre" title="modalite d'admission">{{tr}}Modalité d'admission{{/tr}}</label>
  </th>
  <td colspan="2">
    {{if $sejour->sejour_id}}
    {{assign var="checked" value=$sejour->modalite}}
    {{else}}
    {{assign var="checked" value="libre"}}
    {{/if}}
    {{html_radios name="modalite" options=$sejour->_enumsTrans.modalite separator="<br />" title=$sejour->_props.modalite checked=$checked}}
  </td>
</tr>

<tr>
  <th><label for="ATNC_1">ATNC</label></th>
  <td colspan="2">
    <input name="ATNC" value="1" type="radio" {{if $sejour->ATNC == "1"}} checked="checked" {{/if}}/>
    <label for="ATNC_1">Oui</label>
    <input name="ATNC" value="0" type="radio" {{if !$sejour->ATNC || $sejour->ATNC == "0"}} checked="checked" {{/if}}/>
    <label for="ATNC_0">Non</label>
  </td>
</tr>

<tr>
  <th><label for="hormone_croissance_1">Traitement hormonal</label></th>
  <td colspan="2">
    <input name="hormone_croissance" value="1" type="radio" {{if $sejour->hormone_croissance == "1"}} checked="checked" {{/if}}/>
    <label for="hormone_croissance_1">Oui</label>
    <input name="hormone_croissance" value="0" type="radio" {{if !$sejour->hormone_croissance || $sejour->hormone_croissance == "0"}} checked="checked" {{/if}}/>
    <label for="hormone_croissance_0">Non</label>
  </td>
</tr>
{{/if}}
<tr>
  <th>
    <label for="chambre_seule_1" title="Patient à placer dans une chambre particulière">Chambre particulière</label>
  </th>
  <td colspan="2">
    <input name="chambre_seule" value="1" type="radio" {{if $sejour->chambre_seule}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_1">Oui</label>
    <input name="chambre_seule" value="0" type="radio" {{if !$sejour->chambre_seule || !$sejour->sejour_id}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_0">Non</label>
</tr>

{{if !$mode_operation}}
<tr>
  <th>
    <label for="venue_SHS" title="Code Administratif SHS">Code de venue SHS</label>
  </th>
  <td colspan="2">
    <input type="text" size="8" maxlength="8" name="venue_SHS" title="{{$sejour->_props.venue_SHS}}" value="{{$sejour->venue_SHS}}" />
  </td>
</tr>
{{/if}}

<tr>
  <td class="text">
    <label for="convalescence" title="Convalescence">Convalescence</label>
  </td>
  <td class="text" colspan="2">
    <label for="rques" title="Remarques">Remarques sur le séjour</label>
  </td>
</tr>

<tr>
  <td>
    <textarea name="convalescence" title="{{$sejour->_props.convalescence}}" rows="3">{{$sejour->convalescence}}</textarea>
  </td>
  <td colspan="2">
    <textarea name="rques" title="{{$sejour->_props.rques}}" rows="3">{{$sejour->rques}}</textarea>
  </td>
</tr>


{{if !$mode_operation}}
<tr>
  <td class="button" colspan="3">
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
