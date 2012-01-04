{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=advanced_prot value=0}}
{{mb_default var=checked_lines value=0}}
{{assign var=line value=$_line_element}}

<!-- Header de la ligne d'element -->
<table class="tbl elt {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}" id="line_element_{{$line->_id}}"> 
<tr class="hoverable 
          {{if $line->_fin_reelle && !$line->_protocole && !$line->inscription && !$advanced_prot}}
					  {{if $line->_fin_reelle < $now}}hatching{{/if}}
					  {{if $line->_fin_reelle|iso_date < $now|iso_date}}opacity-50{{/if}}
					{{/if}}
          {{if $line->recusee}}
            hatching opacity-50
          {{/if}}">
  <td style="width: 5%; text-align: center">
    {{if !$advanced_prot}}
    	{{if $line->_can_delete_line}}
        <button type="button" class="trash notext"
          onclick="
            if (Prescription.confirmDelLine('{{$line->_view|smarty:nodefaults|JSAttribute}}')) { 
              Prescription.delLineElement('{{$line->_id}}','{{$element}}');
            }">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
    {{else}}
      <input type="checkbox" {{if $checked_lines}}checked="checked"{{/if}} name="_view_{{$_line->_guid}}"
        onchange="$V(this.next(), this.checked ? 1 : 0)"/>
      <input type="hidden" value="{{$checked_lines}}" name="lines[{{$_line->_guid}}]" />
    {{/if}}
	</td>	
	
	<td style="width:20%;" class="text {{if $line->perop}}perop{{/if}} {{if $line->premedication}}premedication{{/if}}">
    {{if !$advanced_prot}}
      <script type="text/javascript">
      	{{if !$line->inscription}}
          Main.add( function(){
            moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
          });
  			{{/if}}
      </script>
			
			{{if !$line->inscription && $line->_ref_parent_line->_id}}
          <a title="Ligne possédant un historique" class="button list notext" href="#1"
             onclick="Prescription.showLineHistory('{{$line->_guid}}')" 
             onmouseover="ObjectTooltip.createEx(this, '{{$line->_ref_parent_line->_guid}}')"></a>
      {{/if}}
				
    {{/if}}
    {{if $line->date_arret && $line->time_arret}}      
      <img src="style/mediboard/images/buttons/stop.png" title="{{tr}}CPrescriptionLineElement-date_arret{{/tr}} : {{$line->date_arret|date_format:$conf.date}} {{$line->time_arret|date_format:$conf.time}}"/>
    {{/if}}
		<span style="float: right">
	    {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
			{{if $line->perop}}{{mb_label object=$line field="perop"}}&nbsp;{{/if}}
		</span>
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}');">{{$line->_ref_element_prescription->_view}}</strong>
		{{if $line->cip_dm}}
		<br /><small class="opacity-70">({{$line->_ref_dm->libelle}})</small>
		{{/if}}
  </td>
	
  <td class="text" style="width:35%;">
		<span {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole && !$line->inscription}}style="text-decoration:line-through"{{/if}}>
		{{if $line->_ref_prises|@count}}
      {{foreach from=$line->_ref_prises item=_prise name=prises}}
        {{$_prise->_view}}{{if !$smarty.foreach.prises.last}}, {{/if}}
      {{/foreach}}
    {{/if}}
		</span>
		
	  {{if $line->commentaire}}
      <br />
	    {{if $line->conditionnel}}
        {{if $line->condition_active}}
          <img src="images/icons/cond.png" title="Ligne conditionnelle activée">
        {{else}}
          <img src="images/icons/cond_barre.png" title="Ligne conditionnelle désactivée">
        {{/if}}
      {{/if}}
			<span style="font-size: 0.8em;" class="opacity-70">
      {{$line->commentaire|spancate:50|smarty:nodefaults}}
      </span>
    {{/if}}
		
  </td>
  
	{{if !$line->_protocole}}
		<td style="width:10%;" class="text {{if $line->_is_past}}warning{{/if}}">
		  <!-- Date de debut -->
	    {{if $line->debut}}
	      {{mb_value object=$line field=debut}}
	      <!-- Heure de debut -->
	      {{if $line->time_debut}}
	        à {{mb_value object=$line field=time_debut}}
	      {{/if}}
	    {{/if}}
			
      {{if $line->_avancement && $line->_ref_prescription->type == "sejour"}}
        <br />
        <strong class="compact">({{$line->_avancement}}/{{$line->_duree_avancement}})</strong>
      {{/if}}
			
			<div class="compact">
				{{if $line->jour_decalage && $line->unite_decalage && $line->jour_decalage != "N"}} 
		      {{if $line->duree > 1 || $line->jour_decalage_fin}} à partir de {{else}} à {{/if}} 
					<span style="letter-spacing:-1px">
					{{if $prescription->object_class == "CSejour"}}{{$line->jour_decalage}}{{else}}J{{/if}}
		      {{if ($line->unite_decalage == "jour" && $line->decalage_line != 0) || ($line->unite_decalage == "heure")}}
		      {{if $line->decalage_line >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line}}
		      {{if $prescription->object_class == "CSejour"}}
            {{if $line->unite_decalage == "heure"}}H{{else}}J{{/if}}
		      {{else}}
		        J
		      {{/if}} 
		      {{/if}}
					</span>
		     {{/if}}
			</div>
		</td>
	  <td style="width:10%;">
	    <!-- Duree de la ligne -->
	    {{if $line->duree}}
	      {{mb_value object=$line field=duree}} {{mb_value object=$line field=unite_duree}} 
	    {{elseif $line->_ref_prescription->type == "sejour"}}
			  {{assign var=_line_chapitre value=$line->_chapitre}}
        {{if $conf.dPprescription.CCategoryPrescription.$_line_chapitre.fin_sejour}}
				  <span class="opacity-70">{{mb_value object=$line field=_duree}} Jour(s) <br />(Fin du séjour)</span>
				{{else}}
				  1 Jour(s)
				{{/if}}
      {{/if}}
			
			 {{if $line->_ref_prescription->type == "sejour" && $line->_fin_relative != "" && $line->_fin_relative <= $conf.dPprescription.CPrescription.nb_days_relative_end}}
	     <br />
	     <strong>
	     (Fin{{if $line->_fin_relative > 0}} - {{$line->_fin_relative}} j){{else $line->_fin_relative === 0}}){{/if}}
	     </strong>
	    {{/if}}
			
	    <div class="compact">
       {{if $line->jour_decalage_fin && $line->unite_decalage_fin && $line->jour_decalage_fin != "N"}}
         jusqu'à {{$line->jour_decalage_fin}}
         <span style="letter-spacing:-1px">
         {{if ($line->unite_decalage_fin == "jour" && $line->decalage_line_fin != 0) || ($line->unite_decalage_fin == "heure")}}
           {{if $line->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$line field=decalage_line_fin increment=1 }}
           {{if $line->unite_decalage_fin == "heure"}}H{{else}}J{{/if}}
         {{/if}}
         </span>
       {{/if}}
      </div>
	  </td>
  {{else}}
	  <td style="width: 20%" class="text">
	  	<!-- Duree de la prise --> 
      {{if $line->duree}}
        Durée de {{mb_value object=$line field=duree}} {{mb_value object=$line field=unite_duree}}
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
   {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}{{tr}}None{{/tr}}{{/if}}
  </td>

  <td style="width:10%;" class="text">
    <button style="float: right" class="edit notext" type="button" onclick="Prescription.reloadLine('{{$line->_guid}}','{{$mode_protocole}}','{{$mode_pharma}}','{{$operation_id}}', null, {{$advanced_prot}});"></button>
    {{if !$line->_protocole}}
      <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
        {{if @$modules.messagerie}}
        {{assign var=patient value=$line->_ref_prescription->_ref_object}}
        {{assign var=subject value="$sejour - $line"}}
        <a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$subject}}')">
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
</tr>
</table>