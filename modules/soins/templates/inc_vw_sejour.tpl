{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=lite_view value=false}}
{{mb_default var=show_full_affectation value=false}}
{{mb_default var=default_tab value=""}}
{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=dossier_medical value=$patient->_ref_dossier_medical}}
{{if ($service_id && $service_id != "NP") || $show_affectation || $function->_id || $praticien->_id}}
  {{assign var=affectation value=$sejour->_ref_curr_affectation}}
  <td class="text {{if $sejour->isolement}}isolement{{/if}}">
    {{if $affectation->_id}}
      {{if $show_full_affectation}}
        {{$affectation->_ref_lit->_view}}
      {{else}}
        {{mb_value object=$affectation->_ref_lit field=nom}}
      {{/if}}
    {{/if}}
  </td>
{{/if}}

<td class="narrow">
  {{mb_include module=patients template=inc_vw_photo_identite size=32 nodebug=true}}
</td>

<td class="text">
  {{assign var=statut value="present"}}
  
  {{if !$sejour->entree_reelle || ($sejour->_ref_prev_affectation->_id && $sejour->_ref_prev_affectation->effectue == 0)}}
    {{assign var=statut value="attente"}}
  {{/if}}
  
  {{if $sejour->sortie_reelle || $sejour->_ref_curr_affectation->effectue == 1}}
    {{assign var=statut value="sorti"}}
  {{/if}}
  
  {{mb_include module=ssr template=inc_view_patient statut=$statut onclick="showDossierSoins('`$sejour->_id`','$date', '$default_tab');"}}
</td>


{{if "dPImeds"|module_active}}
<td>
    <span onclick="showDossierSoins('{{$sejour->_id}}','{{$date}}','Imeds');">
    {{mb_include module=Imeds template=inc_sejour_labo link="#"}}
    </span>
</td>
{{/if}}

{{if !$lite_view}}
<td style="text-align: center;">
  {{assign var=prescription value=$sejour->_ref_prescription_sejour}}

  {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
    {{if $sejour->_ref_prescription_sejour->_count_alertes}}
      <img src="images/icons/ampoule.png" onclick="showAlertes(this, 'tooltip-content-alertes-medium-{{$sejour->_id}}', '{{$sejour->_ref_prescription_sejour->_id}}', 'medium');"/>
      {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_ref_prescription_sejour->_count_alertes}}
      <div id="tooltip-content-alertes-medium-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px; overflow-x:auto;">
      </div>
    {{/if}}
  {{else}}
    {{if $sejour->_ref_prescription_sejour->_count_fast_recent_modif}}
      <img src="images/icons/ampoule.png" onmouseover="ObjectTooltip.createEx(this, '{{$prescription->_guid}}')"/>
      {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_ref_prescription_sejour->_count_fast_recent_modif}}
    {{/if}}
  {{/if}}
</td>
<td style="text-align: center;">
  {{if @$conf.object_handlers.CPrescriptionAlerteHandler}}
    {{if $sejour->_ref_prescription_sejour->_count_urgences}}
      <img src="images/icons/ampoule_urgence.png" onclick="showAlertes(this, 'tooltip-content-alertes-high-{{$sejour->_id}}', '{{$sejour->_ref_prescription_sejour->_id}}', 'high');"/>
      {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_ref_prescription_sejour->_count_urgences}}
      <div id="tooltip-content-alertes-high-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px; overflow-x:auto;">
      </div>
    {{/if}}
  {{/if}} 
</td>
<td style="text-align: center;">
  {{if $sejour->_count_tasks}}
    <img src="images/icons/phone_orange.png" onclick="showTasks(this, 'tooltip-content-tasks-{{$sejour->_id}}', '{{$sejour->_id}}');"/>
    {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_count_tasks}}

    <div id="tooltip-content-tasks-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px:"></div>
  {{/if}}
  
  {{if $sejour->_count_tasks_not_created}}
    <img src="images/icons/phone_red.png" onclick="showTasksNotCreated(this, 'tooltip-content-tasks-not-created-{{$sejour->_id}}', '{{$sejour->_id}}');"/>
    {{mb_include module=system template=inc_vw_counter_tip count=$sejour->_count_tasks_not_created}}

    <div id="tooltip-content-tasks-not-created-{{$sejour->_id}}" style="display: none; height: 400px; width: 400px:"></div>
  {{/if}}
</td>
{{/if}}

<td style="text-align: center;">
  {{if $dossier_medical->_ref_allergies|@count}}
    <img src="images/icons/warning.png" onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-allergies-{{$sejour->_id}}")' />
    {{mb_include module=system template=inc_vw_counter_tip count=$dossier_medical->_ref_allergies|@count}}
    <div id="tooltip-content-allergies-{{$sejour->_id}}" style="display: none;">
      <table class="tbl">
        <tr>
          <th>Allergies</th>
        </tr>
        <tr>
        {{foreach from=$dossier_medical->_ref_allergies item=_allergie}}
          <tr>
            <td>
              {{$_allergie}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </div>
  {{/if}}
</td>
<td style="text-align: center;">
  {{if $dossier_medical->_count_antecedents && ($dossier_medical->_count_antecedents > $dossier_medical->_count_allergies)}}
    {{assign var=antecedents value=$dossier_medical->_ref_antecedents_by_type}}
    <img src="images/icons/antecedents.gif" onmouseover="ObjectTooltip.createDOM(this, 'antecedents{{$sejour->_id}}')" />
    <div id="antecedents{{$sejour->_id}}" style="text-align:left;  display: none;">
      <ul>
        {{foreach from=$antecedents key=name item=cat}}
          {{if $name != "alle" && $cat|@count}}
            <li>
              <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
              <ul>
                {{foreach from=$cat item=ant}}
                  <li>
                    {{if $ant->date}}
                      {{mb_value object=$ant field=date}}:
                    {{/if}}
                    {{$ant->rques}}
                  </li>
                {{/foreach}}
              </ul>
            </li>
          {{/if}}
        {{/foreach}}
      </ul>   
    </div>  
  {{/if}}
</td>
<td>
  <span onmousemove="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
    {{mb_value object=$sejour field=entree format=$conf.date}}
  </span>

  <div style="position: relative">
    <div class="ecap-sejour-bar" title="arrivée il y a {{$sejour->_entree_relative}}j et départ prévu dans {{$sejour->_sortie_relative}}j ({{mb_value object=$sejour field=sortie}})">
      {{assign var=progress_bar_width value=0}}
      {{if $sejour->_duree}}
        {{math assign=progress_bar_width equation='100*(-entree / (duree))' entree=$sejour->_entree_relative duree=$sejour->_duree format='%.2f'}}
      {{/if}}
      
      <div style="width: {{if $sejour->_duree && $progress_bar_width <= 100}}{{$progress_bar_width}}{{else}}100{{/if}}%;"></div>
    </div>
  </div>
</td>
<td class="text">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
    {{mb_value object=$sejour field=_motif_complet}}
  </span>
  {{if $prescription->_id}}
    {{foreach from=$prescription->_jour_op item=_info_jour_op}}
      <br />
      (<span onmouseover="ObjectTooltip.createEx(this, '{{$_info_jour_op.operation_guid}}');">J{{$_info_jour_op.jour_op}}</span>)
    {{/foreach}}
  {{/if}}
</td>
<td>
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien initials=border}}
</td>

{{if !$lite_view}}
<td class="text compact">
  {{foreach from=$sejour->_ref_transmissions item=_transmission}}
    <div onmousemove="ObjectTooltip.createEx(this, '{{$_transmission->_guid}}')">
      <strong>{{$_transmission->type|substr:0:1|upper}}</strong>:{{$_transmission->text}}
    </div>
  {{/foreach}}
</td>
{{/if}}