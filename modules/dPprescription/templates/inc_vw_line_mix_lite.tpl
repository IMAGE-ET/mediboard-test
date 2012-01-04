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

<table class="tbl">
{{assign var=prescription_line_mix_id value=$_prescription_line_mix->_id}}
<tr class="{{if $_prescription_line_mix->_fin && !$_prescription_line_mix->_protocole && !$advanced_prot}}
             {{if $_prescription_line_mix->_fin < $now}}hatching{{/if}}
             {{if $_prescription_line_mix->_fin|iso_date < $now|iso_date}}opacity-50{{/if}}
           {{/if}}">

  <td style="width: 5%; text-align: center;" class="text">
    {{if !$advanced_prot}}
      {{if $_prescription_line_mix->_can_delete_prescription_line_mix}}
         <form name="editPerfLite-{{$_prescription_line_mix->_id}}" action="" method="post">
           <input type="hidden" name="m" value="dPprescription" />
           <input type="hidden" name="dosql" value="do_prescription_line_mix_aed" />
           <input type="hidden" name="prescription_line_mix_id" value="{{$_prescription_line_mix->_id}}" />
           <input type="hidden" name="del" value="1" />
           <button type="button" class="trash notext"
             onclick="
               if (Prescription.confirmDelLine('{{$_prescription_line_mix->_view|smarty:nodefaults|JSAttribute}}')) {
                 return onSubmitFormAjax(this.form, { 
                   onComplete: function(){
									   {{if @$mode_substitution}}
                       Prescription.viewSubstitutionLines.defer('{{$_prescription_line_mix->variante_for_id}}','{{$_prescription_line_mix->variante_for_class}}');
									   {{else}}
                       Prescription.reloadPrescPerf('{{$_prescription_line_mix->prescription_id}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}');
                     {{/if}}
								 
								 }} );}"></button>
         </form>
      {{/if}}
      {{if $_prescription_line_mix->_ref_parent_line->_id}}    
        <a title="Ligne possédant un historique" class="button list notext" href="#1"
             onclick="Prescription.showLineHistory('{{$_prescription_line_mix->_guid}}')" 
             onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_ref_parent_line->_guid}}')">
          </a>
      {{/if}}
    {{else}}
      <input type="checkbox" {{if $checked_lines}}checked="checked"{{/if}} name="_view_{{$_prescription_line_mix->_guid}}"
        onchange="$V(this.next(), this.checked ? 1 : 0)"/>
      <input type="hidden" value="{{$checked_lines}}" name="lines[{{$_prescription_line_mix->_guid}}]" />
    {{/if}}
  </td>
  <td style="width: 45%" class="text {{if $_prescription_line_mix->perop}}perop{{/if}} {{if $_prescription_line_mix->premedication}}premedication{{/if}}">
    {{if $_prescription_line_mix->date_arret && $_prescription_line_mix->time_arret}}      
      <img src="style/mediboard/images/buttons/stop.png" title="{{tr}}CPrescriptionLineElement-date_arret{{/tr}} : {{$_prescription_line_mix->date_arret|date_format:$conf.date}} {{$_prescription_line_mix->time_arret|date_format:$conf.time}}"/>
    {{/if}}
    {{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line name=lines}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl" line=$_perf_line}}
      {{if $_perf_line->_can_vw_livret_therapeutique}}
        <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
      {{/if}} 
      {{if $_perf_line->stupefiant}}
        <img src="images/icons/stup.png" title="Produit stupéfiant" />
      {{/if}} 
      {{if !$_perf_line->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
      {{/if}}
      {{if $_perf_line->_can_vw_generique}}
        <img src="images/icons/generiques.gif" title="Produit générique" />
      {{/if}} 
      {{if $_perf_line->_ref_produit->_supprime}}
        <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
      {{/if}}
      
      <a href="#produit{{$_perf_line->_id}}" onclick="Prescription.viewProduit(null,'{{$_perf_line->code_ucd}}','{{$_perf_line->code_cis}}');" style="display: inline;">
        {{$_perf_line->_ucd_view}}
        <span style="font-weight: bold">
				{{if $_perf_line->_posologie}}
        : <span  {{if $_prescription_line_mix->_fin < $now && !$_prescription_line_mix->_protocole}}style="text-decoration:line-through"{{/if}}>
        {{$_perf_line->_posologie}}</span>
        {{/if}}
				</span>
      </a>
      {{if !$smarty.foreach.lines.last}}<br />{{/if}}
    {{/foreach}}
    
    {{if $_prescription_line_mix->commentaire || $_prescription_line_mix->conditionnel}}
      <br />
    {{/if}}  
    {{if $_prescription_line_mix->commentaire}}
		  {{if $_prescription_line_mix->conditionnel}}
        {{if $_prescription_line_mix->condition_active}}
          <img src="images/icons/cond.png" title="Ligne conditionnelle activée">
        {{else}}
          <img src="images/icons/cond_barre.png" title="Ligne conditionnelle désactivée">
        {{/if}}
      {{/if}}
      <span style="font-size: 0.8em;" class="opacity-70">
      {{$_prescription_line_mix->commentaire|spancate:50|smarty:nodefaults}}
      </span>
    {{/if}}
		{{if $_prescription_line_mix->ald}}{{mb_label object=$_prescription_line_mix field="ald"}}&nbsp;{{/if}}
  </td> 
  
  {{if $_prescription_line_mix->type_line == "aerosol"}}
    <td style="width: 8%;" class="text">
      {{$_prescription_line_mix->_frequence}}
    </td>
    <td style="width: 12%">
      {{if $_prescription_line_mix->interface}}
        {{tr}}CPrescriptionLineMix.interface.{{$_prescription_line_mix->interface}}{{/tr}}
      {{/if}}
    </td>
  {{else}}
    <td style="width: 5%;" class="text">
      {{$_prescription_line_mix->_frequence}}
    </td>
    <td style="width: 15%;" class="text">
      <a href=# onmouseover="ObjectTooltip.createEx(this, '{{$_prescription_line_mix->_guid}}');" style="display: inline; font-weight: bold;">
        {{mb_value object=$_prescription_line_mix field=type}}
      </a>
      <br />
      {{if $_prescription_line_mix->voie == "none"}}
        {{tr}}CPrescriptionLineMix.no_voie{{/tr}}
      {{else}}
        {{mb_value object=$_prescription_line_mix field=voie}}
      {{/if}}
    </td>
  {{/if}}
  {{if !$_prescription_line_mix->_protocole}}
  <td style="width: 10%;" class="text" {{if $_prescription_line_mix->_is_past}}warning{{/if}}>
    {{mb_value object=$_prescription_line_mix field=date_debut}}
    {{if $_prescription_line_mix->time_debut}} 
      à {{mb_value object=$_prescription_line_mix field=time_debut}}
    {{/if}}
		
		{{if $_prescription_line_mix->_avancement && $_prescription_line_mix->_ref_prescription->type == "sejour"}}
      <br />
      <strong class="compact">({{$_prescription_line_mix->_avancement}}/{{$_prescription_line_mix->_duree_avancement}})</strong>
    {{/if}}
		
		<div class="compact">
			{{if $_prescription_line_mix->jour_decalage && $_prescription_line_mix->jour_decalage != "N"}}  
	      à partir de
	      {{$_prescription_line_mix->jour_decalage}} 
	      {{if $_prescription_line_mix->decalage_line >= 0}}+{{/if}}
	      {{mb_value object=$_prescription_line_mix field=decalage_line}} {{if $_prescription_line_mix->unite_decalage == "heure"}}H{{else}}J{{/if}}
	    {{/if}} 
		</div>
  </td>
  <td style="width: 10%;" class="text">
    {{if $_prescription_line_mix->duree}}
      {{mb_value object=$_prescription_line_mix field=duree}} {{mb_value object=$_prescription_line_mix field="unite_duree"}}
    {{/if}}

		{{if $_prescription_line_mix->_ref_prescription->type == "sejour" && $_prescription_line_mix->_fin_relative != "" && $_prescription_line_mix->_fin_relative <= $conf.dPprescription.CPrescription.nb_days_relative_end}}
     <br />
     <strong>
     (Fin{{if $_prescription_line_mix->_fin_relative > 0}} - {{$_prescription_line_mix->_fin_relative}} j){{else $_prescription_line_mix->_fin_relative === 0}}){{/if}}
     </strong>
    {{/if}}
		
		<div class="compact">
			{{if $_prescription_line_mix->jour_decalage_fin}}
      jusqu'à  
      {{$_prescription_line_mix->jour_decalage_fin}} 
      {{if $_prescription_line_mix->decalage_line_fin >= 0}}+{{/if}}
      {{mb_value object=$_prescription_line_mix field=decalage_line_fin}} {{if $_prescription_line_mix->unite_decalage_fin == "heure"}}H{{else}}J{{/if}}
    {{/if}} 
		</div>
  </td>  
  {{else}}
  <td style="width: 20%" class="text">
    {{if $_prescription_line_mix->duree}}
      Durée de 
      {{mb_value object=$_prescription_line_mix field=duree}}
      {{mb_value object=$_prescription_line_mix field=unite_duree}}
    {{/if}}            
    
    {{if $_prescription_line_mix->jour_decalage}}  
      à partir de
      {{mb_value object=$_prescription_line_mix field="jour_decalage"}} 
      {{if $_prescription_line_mix->decalage_line >= 0}}+{{/if}}
      {{mb_value object=$_prescription_line_mix field=decalage_line}} {{mb_value object=$_prescription_line_mix field=unite_decalage}}
    {{/if}} 
    
    {{if $_prescription_line_mix->jour_decalage_fin}}
      jusqu'à  
      {{mb_value object=$_prescription_line_mix field="jour_decalage_fin"}} 
      {{if $_prescription_line_mix->decalage_line_fin >= 0}}+{{/if}}
      {{mb_value object=$_prescription_line_mix field=decalage_line_fin}} {{mb_value object=$_prescription_line_mix field=unite_decalage_fin}}
    {{/if}}               
  </td>
  {{/if}} 

  <td style="width: 10%" class="text">
    <button style="float: right;" class="edit notext" type="button" onclick="Prescription.reloadLine('{{$_prescription_line_mix->_guid}}','{{$_prescription_line_mix->_protocole}}','{{$mode_pharma}}','{{$operation_id}}','{{$mode_substitution}}', {{$advanced_prot}});"></button>
     {{if !$_prescription_line_mix->_protocole}}
     <div class="mediuser" style="border-color: #{{$_prescription_line_mix->_ref_praticien->_ref_function->color}};">
        {{if $_prescription_line_mix->signature_prat}}
          <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
        {{else}}
          <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
        {{/if}}
        {{if $prescription->type != "externe"}}
          {{if $_prescription_line_mix->signature_pharma}}
            <img src="images/icons/signature_pharma.png" title="Validée par le pharmacien" />
          {{else}}
            <img src="images/icons/signature_pharma_barre.png" title="Non validée par le pharmacien" />
          {{/if}} 
        {{/if}}
        <label title="{{$_prescription_line_mix->_ref_praticien->_view}}">{{$_prescription_line_mix->_ref_praticien->_shortview}}</label>
     </div>
     {{else}}
       -
     {{/if}}
  </td>
</tr>
</table>