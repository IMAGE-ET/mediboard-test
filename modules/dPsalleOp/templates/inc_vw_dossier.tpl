<table class="form">
<tr>
	<th class="category" colspan="10">Dossier médical</th>
</tr>

<tr>
 <td class="text">
  <strong>Addictions significatifs</strong>
<ul>
{{if $dossier_medical->_ref_addictions}}
  {{foreach from=$dossier_medical->_ref_types_addiction key=curr_type item=list_addiction}}
  {{if $list_addiction|@count}}
  <li>
    {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
    {{foreach from=$list_addiction item=curr_addiction}}
    <ul>
      <li>
        {{mb_field object=$curr_addiction field="addiction_id" hidden=1 prop=""}}
        {{$curr_addiction->addiction}}
      </li>
    </ul>
    {{/foreach}}
  </li>
  {{/if}}
  {{/foreach}}
{{else}}
  <li><em>Pas d'addictions</em></li>
{{/if}}
</ul>

<strong>Antécédents significatifs</strong>
<ul>
  {{foreach from=$dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
  {{if $list_antecedent|@count}}
  <li>
    {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
    {{foreach from=$list_antecedent item=curr_antecedent}}
    <ul>
      <li>
      {{if $curr_antecedent->date}}
          {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
        {{/if}}
        {{$curr_antecedent->rques}}
      </li>
    </ul>
    {{/foreach}}
  </li>
  {{/if}}
  {{foreachelse}}
  <li><em>Pas d'antécédents</em></li>
  {{/foreach}}
</ul>

<strong>Traitements significatifs</strong>
<ul>
  {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
  <li>
    {{if $curr_trmt->fin}}
      Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
    {{elseif $curr_trmt->debut}}
      Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
    {{/if}}
    {{$curr_trmt->traitement}}
  </li>
  {{foreachelse}}
  <li><em>Pas de traitements</em></li>
  {{/foreach}}
</ul>

<strong>Diagnostics significatifs de l'opération</strong>
<ul>
  {{foreach from=$dossier_medical->_ext_codes_cim item=curr_code}}
  <li>
    {{$curr_code->code}}: {{$curr_code->libelle}}
  </li>
  {{foreachelse}}
  <li><em>Pas de diagnostic</em></li>
  {{/foreach}}
</ul>
 
  </td>
  <td>
  <form name="newDocumentFrm" action="?m={{$m}}" method="post">
   <select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$selOp->_id}})">
        <option value="">&mdash; Choisir un modèle</option>
        <optgroup label="Opération">
        {{foreach from=$crList item=curr_cr}}
        <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
        {{/foreach}}
        </optgroup>
        <optgroup label="Hospitalisation">
        {{foreach from=$hospiList item=curr_hospi}}
        <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
        {{/foreach}}
        </optgroup>
      </select>
      <br />
      <select name="_choix_pack" onchange="if (this.value) DocumentPack.create(this.value, {{$selOp->_id}})">
        <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
        {{foreach from=$packList item=curr_pack}}
          <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
        {{foreachelse}}
          <option value="">{{tr}}pack-none{{/tr}}</option>
        {{/foreach}}
      </select>
     </form>
 
 <!-- Affichage de la liste des documents de l'operation -->
<div id="documents">

</div>
</td></tr></table>