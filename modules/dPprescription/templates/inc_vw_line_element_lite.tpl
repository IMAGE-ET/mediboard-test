{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$_line_element}}
<!-- Header de la ligne d'element -->
<table class="tbl elt {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}" id="line_element_{{$line->_id}}"> 
<tr class="hoverable">    
  <td style="width:22%;" id="th_line_CPrescriptionLineElement_{{$line->_id}}"
      class="text {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} arretee{{/if}}">
			{{if $line->_can_delete_line}}
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$line->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
      {{/if}}
    <script type="text/javascript">
       Main.add( function(){
         moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
       });
    </script>
		<span style="float: right">
	    {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
	    {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
		</span>
    <strong>{{$line->_ref_element_prescription->_view}}</strong>
		{{if $line->cip_dm}}
		<br /><small style="opacity: 0.7;">({{$line->_ref_dm->libelle}})</small>
		{{/if}}
  </td>
  <td class="text" style="width:35%;">
    {{if $line->_ref_prises|@count}}
      {{foreach from=$line->_ref_prises item=_prise name=prises}}
        {{$_prise->_view}}{{if !$smarty.foreach.prises.last}}, {{/if}}
      {{/foreach}}
    {{/if}}
  </td>
  <td style="width:8%;" class="text">
	  {{if !$line->_protocole}}
    <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
				{{if @$modules.messagerie}}
				<a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$line->_view}}')">
				  <img src="images/icons/mbmail.png" title="Envoyer un message" />
				</a>
				{{/if}}
				{{if $line->signee}}
				  <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
				{{else}}
				  <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
				{{/if}}
			  <label title="{{$line->_ref_praticien->_view}}">{{$line->_ref_praticien->_shortview}}</label>
		</div>
		{{else}}
		- 
		{{/if}}
  </td>
	
	{{if !$line->_protocole}}
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
  {{else}}
	  <td style="width: 25%" class="text">
	  	<!-- Duree de la prise --> 
     {{if $line->duree}}
       Durée de {{mb_value object=$line field=duree}} jour(s) 
      {{/if}}
    
      <!-- Date de debut de la ligne -->
    {{if $line->jour_decalage && $line->unite_decalage}} 
      {{if $line->duree > 1 || $line->jour_decalage_fin}} à partir de {{else}} à {{/if}}
      {{if $prescription->object_class == "CSejour"}} {{$line->jour_decalage}} {{else}} J {{/if}}
      {{if ($line->unite_decalage == "jour" && $line->decalage_line != 0) || ($line->unite_decalage == "heure")}}
      {{if $line->decalage_line >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line size="3"}}
      {{if $prescription->object_class == "CSejour"}}
        {{mb_value object=$line field=unite_decalage}}
      {{else}}
       (jours)
      {{/if}} 
      {{/if}}
       {{if $line->time_debut}}
         à {{mb_value object=$line field=time_debut}}
       {{/if}}
     {{/if}}
     <!-- Date de fin -->
     {{if $line->jour_decalage_fin && $line->unite_decalage_fin}}
       jusqu'à {{$line->jour_decalage_fin}}
       {{if ($line->unite_decalage_fin == "jour" && $line->decalage_line_fin != 0) || ($line->unite_decalage_fin == "heure")}}
         {{if $line->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line_fin increment=1 }}
         {{mb_value object=$line field=unite_decalage_fin }}
       {{/if}}
       {{if $line->time_fin}} 
        à {{mb_value showPlus=1 object=$line field=time_fin}}    
       {{/if}}  
     {{/if}}
	  </td>
	{{/if}}
	
	<td style="width:10%;" class="text">
    <button style="float: right" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$category->chapitre}}', '', '{{$mode_pharma}}', null,'{{$line->_guid}}');"></button>
   {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}aucun{{/if}}
  </td>
</tr>
</table>