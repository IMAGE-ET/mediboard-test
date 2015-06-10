{{*
 * $Id$
 *  
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<td class="text" style="width: 20%">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
    {{if !$_sejour->sortie_reelle}}
      {{mb_title object=$_sejour field=_entree}}
    {{/if}}
    <strong>
      {{mb_value object=$_sejour field=entree date=$date}}
      {{if $_sejour->sortie_reelle}}
        &gt; {{mb_value object=$_sejour field=sortie date=$date}}
      {{/if}}
    </strong>
  </span>
  {{if $_sejour->mode_sortie}}
    <br />{{mb_title object=$_sejour field=sortie}} :
    {{mb_value object=$_sejour field=mode_sortie}}
  {{/if}}
  {{if $_sejour->mode_sortie == "transfert" && $_sejour->etablissement_sortie_id}}
    <br />&gt; <strong>{{$_sejour->_ref_etablissement_transfert->nom|spancate:26}}</strong>
  {{/if}}
  {{if $canAdmissions->edit}}
    {{if $_sejour->sortie_reelle}}
      <button style="float: right" class="edit notext" type="button" onclick="Admissions.validerSortie('{{$_sejour->_id}}', false, reloadSortieLine.curry('{{$_sejour->_id}}'));">
        {{tr}}Modify{{/tr}} {{mb_label object=$_sejour field=sortie}}
      </button>
    {{else}}
      <div style="white-space: nowrap;">
        <button class="tick" type="button" onclick="Admissions.validerSortie('{{$_sejour->_id}}', false, reloadSortieLine.curry('{{$_sejour->_id}}'));">
          Valider la sortie
        </button>
      </div>
    {{/if}}
  {{/if}}
</td>

<td>
  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
</td>

{{if "dPplanningOp CSejour use_phone"|conf:"CGroups-$g"}}
  <td class="button">
    {{mb_include module=planningOp template=vw_appel_sejour type=sortie sejour=$_sejour}}
  </td>
{{/if}}

<td class="text CPatient-view" colspan="2">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}

      <button type="button" class="print notext" onclick="Admissions.showDocs('{{$_sejour->_id}}')"></button>

      {{foreach from=$_sejour->_ref_operations item=curr_op}}
        <a class="action" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$curr_op->_id}}); return false;">
          <img src="images/icons/print.png" />
        </a>
        {{foreachelse}}
        <a class="action" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
          <img src="images/icons/print.png" />
        </a>
      {{/foreach}}

      <a class="action" title="Modifier le séjour" href="#editDHE"
         onclick="Sejour.editModal({{$_sejour->_id}}, reloadSorties); return false;">
        <img src="images/icons/planning.png" />
      </a>

      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
  {{/if}}

  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}

  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}');">
    {{$_sejour->_ref_patient->_view}}
  </span>
</td>
<td class="text">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>
<td>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{if ($_sejour->sortie_prevue < $date_min) || ($_sejour->sortie_prevue > $date_max)}}
      {{$_sejour->sortie_prevue|date_format:$conf.datetime}}
    {{else}}
      {{$_sejour->sortie_prevue|date_format:$conf.time}}
    {{/if}}
  </span>
  {{if $_sejour->confirme}}
    <img src="images/icons/tick.png" title="Sortie confirmée par le praticien" />
  {{/if}}
</td>
<td class="text">
  {{if !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{if $conf.dPadmissions.show_prestations_sorties}}
      {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{/if}}

    {{foreach from=$_sejour->_ref_affectations item=_aff}}
      <div {{if $_aff->effectue}} class="effectue" {{/if}}>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_aff->_guid}}');">
              {{$_aff->_ref_lit}}
            </span>
      </div>
      {{foreachelse}}
      <div class="empty">Non placé</div>
    {{/foreach}}
  {{/if}}
</td>
<td>
  {{if $_sejour->sortie_preparee}}
    <button type="button" class="cancel" onclick="sortie_preparee('{{$_sejour->_id}}', '0');">{{tr}}Cancel{{/tr}}</button>
  {{else}}
    <button type="button" class="tick" onclick="sortie_preparee('{{$_sejour->_id}}', '1');">{{tr}}CSejour-sortie_preparee{{/tr}}</button>
  {{/if}}
</td>
{{if $conf.dPadmissions.show_dh}}
  <td>
    {{foreach from=$_sejour->_ref_operations item=curr_op}}
      {{if $curr_op->_ref_actes_ccam|@count}}
        <span style="color: #484;">
          {{foreach from=$curr_op->_ref_actes_ccam item=_acte}}
            {{if $_acte->montant_depassement}}
              {{if $_acte->code_activite == 1}}
                Chir :
              {{elseif $_acte->code_activite == 4}}
                Anesth :
              {{else}}
                Activité {{$_acte->code_activite}} :
              {{/if}}
              {{mb_value object=$_acte field=montant_depassement}}
              <br />
            {{/if}}
          {{/foreach}}
        </span>
      {{/if}}
      {{if $curr_op->depassement}}
        Prévu chir : {{mb_value object=$curr_op field="depassement"}}
        <br />
      {{/if}}
      {{if $curr_op->depassement_anesth}}
        Prévu anesth : {{mb_value object=$curr_op field="depassement_anesth"}}
        <br />
      {{/if}}
      {{foreachelse}}
      -
    {{/foreach}}
  </td>
{{/if}}