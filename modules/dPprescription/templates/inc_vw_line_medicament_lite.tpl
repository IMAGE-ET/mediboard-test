{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$curr_line}}
{{mb_default var=advanced_prot value=0}}
{{mb_default var=checked_lines value=0}}

<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}
                  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}" id="line_medicament_{{$line->_id}}">
  <!-- Header de la ligne -->
  <tr class="hoverable 
     {{if !$line->_protocole && $line->_fin_reelle && !$advanced_prot}}
       {{if $line->_fin_reelle < $now}}hatching{{/if}}
       {{if $line->_fin_reelle|iso_date < $now|iso_date}}opacity-50{{/if}}
     {{/if}}
     {{if $line->recusee}}
       hatching opacity-50
     {{/if}}">
  
    <td style="text-align: center; width: 5%;" class="text">
      {{if !$advanced_prot}}
        <!-- Suppression de la ligne -->
        {{if $line->_can_delete_line}}
          {{if $line->inscription}}
            {{assign var=chapitre value="inscription"}}
          {{else}}
            {{assign var=chapitre value="medicament"}}
          {{/if}}
              
          <button type="button" class="trash notext" onclick="
            if (Prescription.confirmDelLine('{{$line->_view|smarty:nodefaults|JSAttribute}}')) {
              Prescription.delLine('{{$line->_id}}', '{{$chapitre}}')
             }" style="">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      {{else}}
        <input type="checkbox" {{if $checked_lines}}checked="checked"{{/if}} name="_view_{{$_line->_guid}}"
            onchange="$V(this.next(), this.checked ? 1 : 0)" />
        <input type="hidden" value="{{$checked_lines}}" name="lines[{{$_line->_guid}}]" />
      {{/if}}
    </td>
    
    <td style="width: 25%" class="text {{if $line->traitement_personnel}}traitement{{/if}} {{if $line->perop}}perop{{/if}} {{if $line->premedication}}premedication{{/if}}">
      {{if !$advanced_prot}}
        <script type="text/javascript">
          {{if !$line->_protocole && !$line->inscription}}
            Main.add( function(){
              moveTbody($('line_medicament_{{$line->_id}}'));
            });
           {{/if}}
        </script>
        
        {{if !$line->inscription && $line->_ref_parent_line->_id}}
          <a title="Ligne possédant un historique" class="button list notext" href="#1"
             onclick="Prescription.showLineHistory('{{$line->_guid}}')" 
             onmouseover="ObjectTooltip.createEx(this, '{{$line->_ref_parent_line->_guid}}')"></a>
        {{/if}}
      {{/if}}
      
       <span>
         {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
        {{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl"}}
      </span>
      
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit(null,'{{$line->code_ucd}}','{{$line->code_cis}}');"
        style="font-weight: bold; display: inline;" onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}')">
        {{$line->_ucd_view}}
      </a>
      <br />
   
      <span style="font-size: 0.8em;" class="opacity-70">
        {{$line->_forme_galenique}}
        {{if $line->voie != "none"}}
          - {{$line->voie}}
        {{/if}}
      </span>
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
      {{if $line->fin}}
          <td colspan="2" style="width: 20%" class="text">
            A partir de la fin du séjour jusqu'au {{mb_value object=$line field=fin}}
          </td>
      {{else}}
        <td style="width: 10%" class="text {{if $line->_is_past}}warning{{/if}}">
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
          {{elseif $line->_ref_prescription->type == "sejour"}}
            <span class="opacity-70">{{mb_value object=$line field=_duree}} Jour(s) <br />(Fin du séjour)</span>
          {{/if}}
        </td>
      {{/if}}
    {{else}}
     <td style="width: 20%" class="text">
       <!-- Duree de la prise --> 
       {{if $line->duree}}
         Durée de {{mb_value object=$line field=duree}} {{mb_value object=$line field=unite_duree}}
       {{/if}}
      
       <!-- Date de debut de la ligne -->
      {{if $line->jour_decalage && $line->unite_decalage}} 
        {{if $line->duree > 1 || $line->jour_decalage_fin}} à partir de{{else}} à {{/if}}
        {{if $prescription->object_class == "CSejour"}} {{mb_value object=$line field=jour_decalage}} {{else}} J {{/if}}
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
         jusqu'à {{mb_value object=$line field=jour_decalage_fin}}
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
      <button style="float: right;" class="edit notext" type="button" onclick="Prescription.reloadLine('{{$line->_guid}}','{{$line->_protocole}}','{{$mode_pharma}}','{{$operation_id}}','{{$mode_substitution}}', {{$advanced_prot}});"></button>
  
      {{if !$line->_protocole}}
        <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
          {{if @$modules.messagerie}}
            {{assign var=subject value="$sejour - $line"}}
            <a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$subject}}')">
              <img src="images/icons/mbmail.png" title="Envoyer un message" />
            </a>
          {{/if}}
          {{if $line->signee}}
            {{if $line->recusee}}
              <img src="images/icons/error.png" title="Ligne récusée par le praticien" />
            {{else}}
              <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
            {{/if}}
          {{else}}
            <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
          {{/if}}
          {{if $prescription->type == "sejour"}}
            {{if $line->valide_pharma}}
              <img src="images/icons/signature_pharma.png" title="Validée par le pharmacien" />
            {{else}}
              <img src="images/icons/signature_pharma_barre.png" title="Non validée par le pharmacien" />
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