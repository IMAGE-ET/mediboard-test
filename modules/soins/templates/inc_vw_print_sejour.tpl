{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
<tbody style="{{if $smarty.session.browser.name != "msie" || $smarty.session.browser.majorver != 9}} page-break-inside: avoid; {{/if}}border: 2px solid #666; font-size: 1.2em;">
<tr>
	{{if ($service_id && $service_id != "NP") || $show_affectation}}
		{{assign var=affectation value=$sejour->_ref_curr_affectation}}
		<td rowspan="3">
		  {{if $affectation->_id}}
		    {{mb_value object=$affectation->_ref_lit field=nom}}
		  {{/if}}
			{{if $sejour->isolement}}<br /><small>(isolement)</small>{{/if}}
    </td>
  {{/if}}

	<!-- Identité du patient -->
	<td class="text" rowspan="3">
    <span onclick="showDossierSoins('{{$sejour->_id}}','{{$date}}');">
      {{assign var=statut value="present"}}
      {{if !$sejour->entree_reelle || ($sejour->_ref_prev_affectation->_id && $sejour->_ref_prev_affectation->effectue == 0)}}
      {{assign var=statut value="attente"}}
      {{/if}}
      {{if $sejour->sortie_reelle || $sejour->_ref_curr_affectation->effectue == 1}}
      {{assign var=statut value="sorti"}}
      {{/if}}
      {{mb_include module=ssr template=inc_view_patient statut=$statut}}
    </span>
  </td>

	<!-- Praticien -->
	<td>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}
  </td>

	<!-- Modif d'hospi -->
  <td class="text">
    {{if $sejour->_ref_prescription_sejour->_id}}
      {{foreach from=$sejour->_ref_prescription_sejour->_jour_op item=_info_jour_op}}
        (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
      {{/foreach}}
    {{/if}}
    
  	{{if $sejour->libelle}}
  	<strong>{{mb_label object=$sejour field=libelle}}:</strong>
    {{mb_value object=$sejour field=libelle}}
		{{/if}}
  </td>
</tr>
	
<tr>
	<!-- Transmissions -->
	<td class="text">
	  {{foreach from=$sejour->_ref_transmissions item=_transmission}}
	    <div onmousemove="ObjectTooltip.createEx(this, '{{$_transmission->_guid}}')" style="display: inline;">
	      <strong>{{$_transmission->type|substr:0:1|upper}}</strong>: {{$_transmission->text|nl2br}}
	    </div>
	  {{/foreach}}
	</td>

	<!-- Taches -->
  <td class="text">
   {{if $sejour->_ref_tasks}}
		{{foreach from=$sejour->_ref_tasks item=_task}}
    	{{$_task->description}}
      {{if $_task->prescription_line_element_id}}
        {{$_task->_ref_prescription_line_element->_view}}
      {{/if}}
			{{if $_task->resultat}}
      : {{$_task->resultat}}
      {{/if}} 
		<br />
  {{/foreach}}
	{{/if}}
	
	{{if $sejour->_ref_tasks_not_created}}
		{{foreach from=$sejour->_ref_tasks_not_created item=_task_not_created}}
		  {{$_task_not_created->_view}}
			<br />
		{{/foreach}}
  {{/if}}
  </td>
</tr>

<tr>
	<!-- Allergies -->
  <td class="text">
    <strong>Allergies:</strong>
    {{if $dossier_medical->_ref_allergies|@count}}
      {{foreach from=$dossier_medical->_ref_allergies item=_allergie}}
        {{$_allergie}}<br />
      {{/foreach}}
    {{else}}
    ?
    {{/if}}
  </td>

  <!-- Antécédents -->
   <td class="text" colspan="2">
    {{if $dossier_medical->_count_antecedents && ($dossier_medical->_count_antecedents > $dossier_medical->_count_allergies)}}
      {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
      {{foreach from=$antecedents key=name item=cat}}
        {{if $name != "alle" && $cat|@count}}
            <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
              {{foreach from=$cat item=ant name=ants}}
                  {{if $ant->date}}
                    {{mb_value object=$ant field=date}}:
                  {{/if}}
                  {{$ant->rques}}
                  {{if !$smarty.foreach.ants.last}},{{/if}}
              {{/foreach}}
           <br />
        {{/if}}
      {{/foreach}}
    {{/if}}
  </td>
</tr>
</tbody>