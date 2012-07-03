{{* $Id: vw_sortie_rpu.tpl 10391 2010-10-14 14:34:09Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(window.print);
</script>

<table class="tbl" id="list-sorties">
  <tr>
    <th colspan="2">{{mb_title class=CRPU field="_patient_id"}}</th>
    {{if $conf.dPurgences.responsable_rpu_view}}
      <th>{{mb_title class=CRPU field="_responsable_id"}}</th>
    {{/if}}
    <th>Prise en charge</th>
    <th>{{mb_title class=CRPU field="rpu_id"}}</th>
    <th>
      {{mb_title class=CSejour field=_entree}} /
      {{mb_title class=CSejour field=_sortie}}
    </th>
    {{if $conf.dPurgences.check_can_leave}}
    <th>{{mb_title class=CRPU field="_can_leave"}}</th>
		{{/if}}
  </tr>
  {{foreach from=$listSejours item=sejour}}
      {{assign var=rpu value=$sejour->_ref_rpu}}
      {{assign var=patient value=$sejour->_ref_patient}}
      <tr {{if !$sejour->sortie_reelle && $sejour->_veille}}class="veille"{{/if}}>
        {{assign var=sejour_id value=$sejour->_id}}
  
    {{assign var=rpu value=$sejour->_ref_rpu}}
    {{assign var=rpu_id value=$rpu->_id}}
    
    {{assign var=patient value=$sejour->_ref_patient}}
    {{assign var=atu value=$sejour->_ref_consult_atu}}
    
    {{mb_ternary var=rpu_link_param test=$rpu->_id value="rpu_id=$rpu_id" other="sejour_id=$sejour_id"}}
    {{assign var=rpu_link value="?m=dPurgences&tab=vw_aed_rpu&$rpu_link_param"}}
    
    <td class="text {{if $sejour->annule}} cancelled {{/if}}" colspan="2">
      {{mb_include template=inc_rpu_patient}}
    </td>
    
    {{if $sejour->annule}}
    <td class="cancelled" colspan="10">
      {{if $rpu->mutation_sejour_id}}
      Hospitalisation
        dossier {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$rpu->_ref_sejour_mutation}}
      {{else}}
      {{tr}}Cancelled{{/tr}}
      {{/if}}
    </td>
    
    {{else}}
    {{if $conf.dPurgences.responsable_rpu_view}}
    <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
    </td>
    {{/if}}
    
    <td class="{{if $sejour->type != "urg"}} arretee {{/if}}">
      {{if !$rpu->_ref_consult->_id}}
        <em>{{tr}}CRPU-ATU-missing{{/tr}}</em>
      {{else}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
      {{/if}}
    </td>
    
    <td class="text">
		  <button class="search notext not-printable" style="float: right;" onclick="ObjectTooltip.createEx(this, '{{$rpu->_guid}}');">
			  {{tr}}Info{{/tr}}
		  </button>

      <!-- Vérification des champs semi obligatoires -->
      {{if $conf.dPurgences.check_ccmu}}
        {{if !$rpu->ccmu           }}<div class="warning" style="display: block;">Champ manquant {{mb_label object=$rpu field=ccmu           }}</div>{{/if}}
      {{/if}}

      {{if $conf.dPurgences.check_gemsa}}
        {{if !$rpu->gemsa          }}<div class="warning" style="display: block;">Champ manquant {{mb_label object=$rpu field=gemsa          }}</div>{{/if}}
      {{/if}}
      
      {{if $conf.dPurgences.check_cotation}}
        {{if !$rpu->_ref_consult->_ref_actes}}<div class="warning" style="display: block;">Codage des actes manquant</div>{{/if}}
        {{if $sejour->sortie_reelle && !$rpu->_ref_consult->valide}}<div class="warning" style="display: block;">La cotation n'est pas validée</div>{{/if}}
      {{/if}}
    
      {{if $conf.dPurgences.old_rpu == "1"}}
      {{if !$rpu->type_pathologie}}<div class="warning" style="display: block;">Champ manquant {{mb_label object=$rpu field=type_pathologie}}</div>{{/if}}
      {{if !$rpu->urtrau         }}<div class="warning" style="display: block;">Champ manquant {{mb_label object=$rpu field=urtrau         }}</div>{{/if}}
      {{if !$rpu->urmuta         }}<div class="warning" style="display: block;">Champ manquant {{mb_label object=$rpu field=urmuta         }}</div>{{/if}}
      {{/if}}

      {{if $sejour->destination}}
         <strong>{{tr}}CSejour-destination{{/tr}}:</strong>
         {{mb_value object=$sejour field="destination"}} <br />
      {{/if}}
      {{if $rpu->orientation}}
         <strong>{{tr}}CRPU-orientation{{/tr}}:</strong>
         {{mb_value object=$rpu field="orientation"}}      
       {{/if}}
    </td>
    
    <td class="text sortie {{$sejour->mode_sortie}}">
		  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
		    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
		    {{if !$sejour->sortie_reelle}} 
		    {{mb_title object=$sejour field=_entree}}
		    {{/if}}
		    <strong>
		      {{mb_value object=$sejour field=entree date=$date}}
		      {{if $sejour->sortie_reelle}} 
		      &gt; {{mb_value object=$sejour field=sortie date=$date}}
		      {{/if}}
		    </strong>
		    
		  </span>
		  
		   <br />
		  {{if $sejour->sortie_reelle}} 
		    {{mb_title object=$sejour field=sortie}} :
		    {{mb_value object=$sejour field=mode_sortie}}
		  
		    {{if $sejour->mode_sortie == "transfert" && $sejour->etablissement_sortie_id}}
		      <br />&gt; <strong>{{mb_value object=$sejour field=etablissement_sortie_id}}</strong>
		    {{/if}}
		  
		    {{if $sejour->mode_sortie == "mutation" && $sejour->service_sortie_id}}
		      {{assign var=service_id value=$sejour->service_sortie_id}}
		      {{assign var=service value=$services.$service_id}}
		      <br />&gt; <strong>{{$service}}</strong>
		    {{/if}}
				<em>{{mb_value object=$sejour field=commentaires_sortie}}</em>
		  {{/if}}
    </td>
      
    {{if $conf.dPurgences.check_can_leave}}
    <td id="rpu-{{$rpu->_id}}" style="font-weight: bold" class="text {{if !$rpu->sortie_autorisee}}arretee{{/if}} {{$rpu->_can_leave_level}}">
      {{if $sejour->sortie_reelle}}
        {{if !$rpu->sortie_autorisee}}
          {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
        {{/if}}
      {{elseif $rpu->_can_leave == -1}}
        {{if $sejour->type != "urg"}}
          {{mb_value object=$sejour field=type}}<br />
        {{elseif !$atu->_id}} 
          Pas encore de prise en charge<br />
        {{else}}
          {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.48{{/tr}} <br />
        {{/if}}
        {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
      {{elseif $rpu->_can_leave != -1 && !$rpu->sortie_autorisee}}
        {{tr}}CConsultation{{/tr}} {{tr}}CConsultation.chrono.64{{/tr}} <br />
        {{tr}}CRPU-sortie_assuree.0{{/tr}}
      {{else}}
        {{if $rpu->_can_leave_since}}
          {{tr}}CRPU-_can_leave_since{{/tr}}
        {{/if}}
        {{if $rpu->_can_leave_about}}
          {{tr}}CRPU-_can_leave_about{{/tr}}
        {{/if}}
        <span title="{{$sejour->sortie_prevue}}">{{mb_value object=$rpu field="_can_leave"}}</span><br />
        {{tr}}CRPU-sortie_assuree.{{$rpu->sortie_autorisee}}{{/tr}}
      {{/if}}
    </td>
		{{/if}}
		
    {{/if}}
      </tr>
  {{foreachelse}}
    <tr><td colspan="{{$conf.dPurgences.responsable_rpu_view|ternary:7:6}}"><em>Aucune sortie à effectuer</em></td></tr>
  {{/foreach}}
</table>