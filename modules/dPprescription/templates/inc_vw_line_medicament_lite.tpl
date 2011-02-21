{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$curr_line}}
<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}
                  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}" id="line_medicament_{{$line->_id}}">
  <!-- Header de la ligne -->
  <tr class="hoverable {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}hatching_red{{/if}}">
    <td style="text-align: center; width: 5%;" class="text">
      <!-- Suppression de la ligne -->
      {{if $line->_can_delete_line}}
        <button type="button" class="trash notext" onclick="
          if (Prescription.confirmDelLine('{{$line->_view|smarty:nodefaults|JSAttribute}}')) {
            Prescription.delLine({{$line->_id}})
           }" style="">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
		  
    </td>
    <td style="width: 25%" class="text {{if $line->traitement_personnel}}traitement{{/if}} {{if $line->perop}}perop{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole && !$line->inscription}}
          Main.add( function(){
            moveTbody($('line_medicament_{{$line->_id}}'));
          });
         {{/if}}
      </script>
      {{if !$line->inscription && $line->_ref_parent_line->_id}}
        {{assign var=parent_line value=$line->_ref_parent_line}}
        <img style="float: right" src="images/icons/history.gif" title="Ligne possédant un historique" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_line->_guid}}')"/>
      {{/if}}
			
       <span>
       	{{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
        {{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl"}}
      </span>
      
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');" style="font-weight: bold; display: inline;">
        {{$line->_ucd_view}}
      </a>
	    <br />
	 
      <span style="font-size: 0.8em; opacity: 0.7">
       {{$line->_forme_galenique}}
      </span>
			
      {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
			{{if $line->perop}}{{mb_label object=$line field="perop"}}&nbsp;{{/if}}
      {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
    </td>
    <td style="width: 40%;" class="text">
			<span {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}style="text-decoration:line-through"{{/if}}>
			{{if $line->_ref_prises|@count}}
        {{foreach from=$line->_ref_prises item=_prise name=prises}}
          {{$_prise->_view}} 
          {{if !$smarty.foreach.prises.last}}, {{/if}}
        {{/foreach}}
      {{else}}
        Aucune posologie
      {{/if}}
			</span>
    </td>
		{{if !$line->_protocole}}
	    {{if $line->fin}}
			  <td colspan="2" style="width: 25%" class="text">
			    <button style="float: right;" class="edit notext" onclick="Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}','{{$mode_pharma}}','{{$operation_id}}');"></button>
          A partir de la fin du séjour jusqu'au {{mb_value object=$line field=fin}}
				</td>
			{{else}}
				<td style="width: 10%" class="text">
	        <!-- Date de debut -->
	        {{if $line->debut}}
	          {{mb_value object=$line field=debut}}
	          {{if $line->time_debut}}
	            à {{mb_value object=$line field=time_debut}}
	          {{/if}}
	        {{/if}}
	      </td>
	      <td style="width: 10%" class="text">
	        <!-- Duree de la ligne -->
	        {{if $line->duree && $line->unite_duree}}
	          {{mb_value object=$line field=duree}}  
	          {{mb_value object=$line field=unite_duree}}
	        {{/if}}
	      </td>
			{{/if}}
		{{else}}
		 <td style="width: 20%" class="text">
			 <!-- Duree de la prise --> 
	     {{if $line->duree && $line->duree != "1"}}
	       Durée de {{mb_value object=$line field=duree}} jour(s) 
	     {{/if}}
	    
	     <!-- Date de debut de la ligne -->
	    {{if $line->jour_decalage && $line->unite_decalage}} 
	      {{if $line->duree > 1 || $line->jour_decalage_fin}} à partir de{{else}} à {{/if}}
	      {{if $prescription->object_class == "CSejour"}} {{$line->jour_decalage}} {{else}} J {{/if}}
	      {{if ($line->unite_decalage == "jour" && $line->decalage_line != 0) || ($line->unite_decalage == "heure")}}
	        {{if $line->decalage_line >= 0}}+{{/if}} 
	        {{mb_value object=$line field=decalage_line size="3"}}
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
	     {{elseif $line->jour_decalage && !$line->duree && $prescription->type == "sejour"}}
	        jusqu'à la fin du séjour.
	     {{/if}}
		 </td>
		{{/if}}
		<td class="text" style="width: 10%" >
			<button style="float: right;" class="edit notext" onclick="Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}','{{$mode_pharma}}','{{$operation_id}}','{{$mode_substitution}}');"></button>

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
          {{if $prescription->type == "sejour"}}
            {{if $line->valide_pharma}}
              <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
            {{else}}
              <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
            {{/if}}
          {{/if}}
         <label title="{{$line->_ref_praticien->_view}}">{{$line->_ref_praticien->_shortview}}</label>
       </div>
       {{else}}
       -
       {{/if}}
    </td>
  </tr>
</table>