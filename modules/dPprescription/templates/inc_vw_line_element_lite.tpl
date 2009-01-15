{{assign var=line value=$_line_element}}
<!-- Header de la ligne d'element -->
<table class="tbl elt {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}" id="line_element_{{$line->_id}}"> 
<tr class="hoverable">    
  <td style="width:25%;" id="th_line_CPrescriptionLineElement_{{$line->_id}}"
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
  <!--
  <td style="width:10%;" class="text">
    {{$category->_view}}
  </td>
   -->
  <td style="width:5%;" class="text">
    <label title="{{$line->_ref_praticien->_view}}">{{$line->_ref_praticien->_shortview}}</label>
		{{if $line->signee}}
		   <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
		{{else}}
		   <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
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
  <td class="text" style="width:25%;">
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
    <button style="float: right" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$category->chapitre}}', '', '{{$mode_pharma}}', null, true, true,'{{$line->_guid}}');"></button>
    {{if $line->emplacement}}
      {{mb_value object=$line field="emplacement"}}
    {{/if}}
  </td>
</tr>
</table>