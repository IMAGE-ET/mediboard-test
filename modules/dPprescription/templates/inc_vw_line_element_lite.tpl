{{assign var=line value=$_line_element}}
<!-- Header de la ligne d'element -->
<tr id="line_element_{{$line->_id}}" class="hoverable elt
  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}">    
  <td style="width:15%;" id="th_line_CPrescriptionLineElement_{{$line->_id}}"
      class="text {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} arretee{{/if}}">
    <script type="text/javascript">
       Main.add( function(){
         moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
       });
    </script>
    {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
    {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
    {{$line->_ref_element_prescription->_view}}
  </td>
  <td style="width:10%;" class="text">
    {{$category->_view}}
  </td>
  <td style="width:10%;" class="text">
    <!-- Affichage de la signature du praticien -->
    {{if $line->_can_view_signature_praticien}}
      {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
    {{else if !$line->_traitement && !$line->_protocole}}
      {{$line->_ref_praticien->_view}}
    {{/if}}
  </td>

	<td style="width:15%;">
	  <!-- Date de debut -->
    {{if $line->debut}}
      {{mb_value object=$line field=debut}}
      <!-- Heure de debut -->
      {{if $line->time_debut}}
        à {{mb_value object=$line field=time_debut}}
      {{/if}}
    {{/if}}
	</td>
  <td style="width:10%;">
    <!-- Duree de la ligne -->
    {{if $line->duree}}
      {{mb_value object=$line field=duree}} {{mb_value object=$line field=unite_duree}} 
    {{/if}}
  </td>
  <td class="text" style="width:20%;">
    {{if $line->_ref_prises|@count}}
      {{foreach from=$line->_ref_prises item=_prise name=prises}}
        {{$_prise->_view}}{{if !$smarty.foreach.prises.last}}, {{/if}}
      {{/foreach}}
    {{/if}}
  </td>
  <td style="width:10%;">
  {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}aucun{{/if}}
  </td>
  <td style="width:10%;">
    {{if $line->emplacement}}
      ({{mb_value object=$line field="emplacement"}})
    {{/if}}
  </td>
</tr>

