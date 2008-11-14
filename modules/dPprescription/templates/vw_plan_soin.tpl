<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>
  
<style type="text/css">

ul {
  padding-left: 11px;
}

.signature_ide {
  border: 1px solid #ccc;
}

</style>

<div class="plan_soin" {{if !$patient->_id}}style="overflow: auto; height: 500px;"{{/if}}>

<!-- Header -->
{{if $patient->_id}}
<table style="width: 100%">
  <tr>
    <td>
      IPP: {{$patient->_IPP}}<br />
      <strong>{{$patient->_view}}</strong>
    </td>
    <td>
      Age {{$patient->_age}}{{if $patient->_age != "??"}} ans{{/if}}<br />
      Poids {{$poids}}{{if $poids}} kg{{/if}}
    </td>
    <td>
      Début du séjour: {{$sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}<br />
      {{if $sejour->_ref_curr_affectation->_id}}
        Chambre {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}
      {{/if}}
    </td>
    <td>
      Feuille de soin du {{$date|date_format:"%d/%m/%Y"}}
    </td>
  </tr>
</table>
{{/if}}

<table style="border-collapse: collapse; border: 1px solid #ccc" class="tbl">
  <!-- Header du tableau -->
  <tr>
    <th colspan="2" class="title" style="width: 7cm">Prescription</th>
    <th rowspan="2" class="title" style="width: 1cm">Prescripteur</th>
    <th rowspan="2" style="width: 5px"></th>
    {{foreach from=$dates item=date}}
    <th colspan="{{$tabHours.$date|@count}}" class="title" style="width: 5cm; border-right: 1px solid black; border-left: 1px solid black;">{{$date|date_format:"%d/%m/%Y"}}</th>
    {{/foreach}}
  </tr>
  <tr>
    <th class="title" style="width: 4cm">Produit</th>
    <th class="title" style="width: 3cm">Posologie</th>
    {{foreach from=$dates item=date }}
      {{foreach from=$tabHours.$date item=_hour name=foreach_date}}
        <th style="{{if $smarty.foreach.foreach_date.first}}border-left: 1px solid black;{{/if}}
                   {{if $smarty.foreach.foreach_date.last}}border-right: 1px solid black;{{/if}}">
          {{$_hour}}h
        </th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  <!-- Affichage des medicaments -->
  {{foreach from=$prescription->_ref_lines_med_for_plan item=_all_lines_unite_prise_cat}}
    {{foreach from=$_all_lines_unite_prise_cat item=_all_lines_unite_prise}}
      {{foreach from=$_all_lines_unite_prise key=unite_prise item=_line}}
        {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$_line suffixe=med}}
    {{/foreach}}
   {{/foreach}} 
  {{/foreach}}
  
  <!-- Affichage des perfusions -->
  {{foreach from=$prescription->_ref_perfusions_for_plan item=_perfusion}}
    {{include file="../../dPprescription/templates/inc_vw_perf_plan_soin.tpl"}}
  {{/foreach}}
  
  
  <!-- Séparation entre les medicaments et les elements -->
  <tr>
    <td colspan="1000" style="padding:0; height: 1px; border: 1px solid black;"></td>
  </tr>
   
  <!-- Affichage des elements -->
  {{foreach from=$prescription->_ref_lines_elt_for_plan key=name_chap item=elements_chap}}
    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}
          {{include file="../../dPprescription/templates/inc_vw_line_plan_soin.tpl" line=$element suffixe=elt}}
          <!-- Affichage d'une barre de separation entre chaque categorie -->
          {{if $smarty.foreach.foreach_elt.last && $smarty.foreach.foreach_cat.last}}
            <tr>
              <td colspan="1000" style="padding:0; height: 1px; border: 1px solid black;"></td>
            </tr>
          {{/if}}
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
  
  {{if $patient->_id}}
  <!-- Footer du tableau -->
  <tbody class="hoverable">
    <tr>
	  <th class="title" colspan="2">Remarque</th>
	  <th class="title" colspan="2">Pharmacien</th>
	  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
	  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
	  <th class="title" colspan="{{$tabHours.$date|@count}}">Signature IDE</th>
	</tr>
	<tr>
	  <td style="border: 1px solid #ccc; height: 1.5cm" colspan="2" rowspan="3"></td>
	  <td class="text" style="border: 1px solid #ccc; text-align: center" colspan="2" rowspan="3">
	  {{if $pharmacien->_id}}
	    {{$pharmacien->_view}} {{$last_log->date|date_format:"%d/%m/%Y à %Hh%M"}}
	  {{/if}}  
	  </td>
	  <td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td>
	</tr>
	<tr><td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td></tr>
	<tr><td class="signature_ide" colspan="{{$tabHours.$date|@count}}" ></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td><td class="signature_ide" colspan="{{$tabHours.$date|@count}}"></td></tr>
  </tbody>
  {{/if}}
</table>

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>