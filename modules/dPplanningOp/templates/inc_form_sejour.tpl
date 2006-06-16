<!-- $Id: $ -->

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_sejour_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="saisi_SHS" value="{{$sejour->saisi_SHS}}" />
<input type="hidden" name="modif_SHS" value="{{$sejour->modif_SHS}}" />
<input type="hidden" name="annule" value="{{$sejour->annule}}" />

<table class="form">

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

<tr>
  <th>
    Sejours existants :
  </th>
  <td colspan="2" id="selectSejours">
    <select name="sejour_id" onchange="reloadSejour(this.value)">
      <option value="0" {{if !$sejour->sejour_id}} selected="selected" {{/if}}>
        &mdash; Selectionner un séjour existant
      </option>
      {{foreach from=$sejours item=curr_sejour}}
      <option value="{{$curr_sejour->sejour_id}}" {{if $sejour->sejour_id == $curr_sejour->sejour_id}} selected="selected" {{/if}}>
        {{$curr_sejour->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <label for="praticien_id" title="Praticien responsable. Obligatoire">Praticien :</label>
  </th>
  <td colspan="2">
    <select name="praticien_id" title="{{$sejour->_props.praticien_id}}">
      <option value="">&mdash; Choisir un praticien</option>
      {{foreach from=$listPraticiens item=curr_praticien}}
      <option value="{{$curr_praticien->user_id}}" {{if $praticien->user_id == $curr_praticien->user_id}} selected="selected" {{/if}}>
        {{$curr_praticien->_view}}
      </option>
      {{/foreach}}
    </select>
  </td>
</tr>

<tr>
  <th>
    <input type="hidden" name="patient_id" title="{{$sejour->_props.patient_id}}" ondblclick="popPat()" value="{{$patient->patient_id}}" />
    <label for="patient_id" title="Patient concerné. Obligatoire">Patient :</label>
  </th>
  <td class="readonly">
  	<input type="text" name="_patient_view" size="30" value="{{$patient->_view}}" readonly="readonly" />
  </td>
  <td class="button">
  	<input type="button" value="Rechercher un patient" onclick="popPat()" />
  </td>
</tr>

<tr>
  <th><label for="DP" title="Code CIM du diagnostic principal">Diagnostic principal (CIM) :</label></th>
  <td><input type="text" name="DP" title="{{$sejour->_props.DP}}" size="10" value="{{$sejour->DP}}" /></td>
  <td class="button"><input type="button" value="Sélectionner un code" onclick="popCode('cim10')" /></td>
</tr>

<tr>
  <th class="category" colspan="3">Admission</th>
</tr>

<tr>
  <th>
  	<label for="_date_entree_prevue" title="Choisir une date d'entrée">Entrée prévue :</label>
  </th>
  <td class="date">
    <div id="editSejour__date_entree_prevue_da">{{$sejour->_date_entree_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_entree_prevue" title="date|notNull" value="{{$sejour->_date_entree_prevue}}" onchange="modifSejour()"/>
    <img id="editSejour__date_entree_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
  </td>
  <td>
    à
    <select name="_hour_entree_prevue">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_entree_prevue == $hour || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select>
    h
    <select name="_min_entree_prevue">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_entree_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select>
    mn
  </td>
</tr>

<tr>
  <th>
  	<label for="_date_sortie_prevue" title="Choisir une date d'entrée">Sortie prévue :</label>
  </th>
  <td class="date">
    <div id="editSejour__date_sortie_prevue_da">{{$sejour->_date_sortie_prevue|date_format:"%d/%m/%Y"}}</div>
    <input type="hidden" name="_date_sortie_prevue" title="date|moreEquals|_date_entree_prevue|notNull" value="{{$sejour->_date_sortie_prevue}}" onchange="modifSejour()"/>
    <img id="editSejour__date_sortie_prevue_trigger" src="./images/calendar.gif" alt="calendar"/>
  </td>
  <td>
    à 
    <select name="_hour_sortie_prevue">
    {{foreach from=$hours item=hour}}
      <option value="{{$hour}}" {{if $sejour->_hour_sortie_prevue == $hour  || (!$sejour->sejour_id && $hour == "8")}} selected="selected" {{/if}}>{{$hour}}</option>
    {{/foreach}}
    </select>
    h
    <select name="_min_sortie_prevue">
    {{foreach from=$mins item=min}}
      <option value="{{$min}}" {{if $sejour->_min_sortie_prevue == $min}} selected="selected" {{/if}}>{{$min}}</option>
    {{/foreach}}
    </select>
    mn
  </td>
</tr>

<tr>
  <th>Entrée réelle :</th>
  <td colspan="2">{{$sejour->entree_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>

<tr>
  <th>Sortie réelle :</th>
  <td colspan="2">{{$sejour->sortie_reelle|date_format:"%d/%m/%Y à %Hh%M"}}</td>
</tr>

<tr>
  <th><label for="type_comp" title="Type d'admission">{{tr}}Type d'admission{{/tr}} :</label></th>
  <td colspan="2">
    <input name="type" value="comp" type="radio" {{if !$sejour->sejour_id || $sejour->type == "comp"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="type_comp">{{tr}}comp{{/tr}}</label><br />
    <input name="type" value="ambu" type="radio" {{if $sejour->type == "ambu"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="type_ambu">{{tr}}ambu{{/tr}}</label><br />
    <input name="type" value="exte" type="radio" {{if $sejour->type == "exte"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="type_exte">{{tr}}exte{{/tr}}</label><br />
  </td>
</tr>

<tr>
  <th>
    <label for="modalite_libre" title="modalite d'admission">{{tr}}Modalité d'admission{{/tr}} :</label>
  </th>
  <td colspan="2">
    <input name="modalite" value="libre" type="radio" {{if !$sejour->sejour_id || $sejour->modalite == "libre"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="modalite_libre">Libre</label><br />
    <input name="modalite" value="office" type="radio" {{if $sejour->modalite == "office"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="modalite_office">Office</label><br />
    <input name="modalite" value="tiers" type="radio" {{if $sejour->modalite == "tiers"}}checked="checked"{{/if}} onchange="modifSejour()" />
    <label for="modalite_tiers">Tiers</label><br />
  </td>
</tr>

<tr>
  <th>
    <label for="chambre_seule_o" title="Patient à placer dans une chambre particulière">Chambre particulière :</label>
  </th>
  <td colspan="2">
    <input name="chambre_seule" value="o" type="radio" {{if $sejour->chambre_seule == "o" || !$sejour->sejour_id}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_o">Oui</label>
    <input name="chambre_seule" value="n" type="radio" {{if $sejour->chambre_seule == "n"}} checked="checked" {{/if}} onchange="modifSejour()" />
    <label for="chambre_seule_n">Non</label>
</tr>

<tr>
  <th>
    <label for="venue_SHS" title="Code Administratif SHS">Code de venue SHS :</label>
  </th>
  <td colspan="2">
    <input type="text" size="8" maxlength="8" name="venue_SHS" title="{{$sejour->_props.venue_SHS}}" value="{{$sejour->venue_SHS}}" />
  </td>
</tr>

<tr>
  <td class="text" colspan="3">
    <label for="rques" title="Remarques">Remarques sur le séjour</label>
  </td>
</tr>

<tr>
  <td  colspan="3">
    <textarea name="rques" title="{{$sejour->_props.rques}}" rows="3">{{$sejour->rques}}</textarea>
  </td>
</tr>

{{if !$mode_operation}}
<tr>
  <td class="button" colspan="3">
  {{if $sejour->sejour_id}}
    <input type="submit" value="Modifier" />
    <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{typeName:'le {{$sejour->_view|escape:"javascript"}}'});" />
    {{if $sejour->annule == "0"}}{{assign var="annule_text" value="Annuler"}}{{/if}}
    {{if $sejour->annule == "1"}}{{assign var="annule_text" value="Rétablir"}}{{/if}}
    <input type="button" value="{{$annule_text}}" onclick="confirmAnnulation();" />
  {{else}}
    <input type="submit" value="Créer" />
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
</script>
