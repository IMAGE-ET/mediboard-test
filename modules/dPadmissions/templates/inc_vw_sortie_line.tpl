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

<td class="text">
  {{if $canAdmissions->edit}}
    <script>
      Main.add(function() {
        // Ceci doit rester ici !! prepareForm necessaire car pas appelé au premier refresh d'un periodical update
        prepareForm("editFrm{{$_sejour->_guid}}");
      });
    </script>

    <form name="editFrm{{$_sejour->_guid}}" action="?m={{$m}}" method="post" data-patient_view="{{$_sejour->_ref_patient->_view}}">
      <input type="hidden" name="m" value="planningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
      <input type="hidden" name="type" value="{{$_sejour->type}}" />
      {{if $_sejour->grossesse_id}}
        <input type="hidden" name="_sejours_enfants_ids" value="{{","|implode:$_sejour->_sejours_enfants_ids}}" />
      {{/if}}
      {{if $_sejour->sortie_reelle}}
        <input type="hidden" name="mode_sortie" value="{{$_sejour->mode_sortie}}" />
        <input type="hidden" name="etablissement_sortie_id" value="{{$_sejour->etablissement_sortie_id}}" />
        <input type="hidden" name="_modifier_sortie" value="0" />
        <button class="cancel" type="button" onclick="submitSortie(this.form)">
          Annuler la sortie
        </button>

        <br />
        {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
          {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
        {{else}}
          {{$_sejour->sortie_reelle|date_format:$conf.time}}
        {{/if}}

        - {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}

        {{if $_sejour->etablissement_sortie_id}}
          - {{$_sejour->_ref_etablissement_transfert}}
        {{/if}}
      {{else}}
        <input type="hidden" name="_modifier_sortie" value="1" />
        <input type="hidden" name="entree_reelle" value="{{$_sejour->entree_reelle}}" />

        <div style="white-space: nowrap;">
          {{if $conf.dPplanningOp.CSejour.use_custom_mode_sortie && $list_mode_sortie|@count}}
            {{mb_field object=$_sejour field=mode_sortie onchange="\$V(this.form._modifier_sortie, 0); submitSortie(this.form);" hidden=true}}
            <select name="mode_sortie_id" class="{{$_sejour->_props.mode_sortie_id}}" style="width: 15em" onchange="updateModeSortie(this)">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{foreach from=$list_mode_sortie item=_mode}}
                <option value="{{$_mode->_id}}" data-mode="{{$_mode->mode}}" {{if $_sejour->mode_sortie_id == $_mode->_id}}selected{{/if}}>
                  {{$_mode}}
                </option>
              {{/foreach}}
            </select>
          {{else}}
            {{mb_field object=$_sejour field="mode_sortie" onchange="this.form._modifier_sortie.value = '0'; submitSortie(this.form);"}}
          {{/if}}
          <button class="tick" type="button" onclick="confirmation('{{$date_actuelle}}', '{{$date_demain}}', '{{$_sejour->sortie_prevue}}', '{{$_sejour->entree_reelle}}', this.form);">
            Effectuer la sortie
          </button>
        </div>
        <div id="listEtabExterne-editFrm{{$_sejour->_guid}}" {{if $_sejour->mode_sortie != "transfert"}} style="display: none;" {{/if}}>
          {{mb_field object=$_sejour field="etablissement_sortie_id" form="editFrm`$_sejour->_guid`"
          autocomplete="true,1,50,true,true" onchange="changeEtablissementId(this.form)"}}
        </div>
      {{/if}}
    </form>
  {{elseif $_sejour->sortie_reelle}}
    {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
      {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
    {{else}}
      {{$_sejour->sortie_reelle|date_format:$conf.time}}
    {{/if}}

    {{if $_sejour->mode_sortie}}
      <br />
      {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}
    {{/if}}

    {{if $_sejour->etablissement_sortie_id}}
      <br />{{$_sejour->_ref_etablissement_transfert}}
    {{/if}}
  {{else}}
    -
  {{/if}}
</td>

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

  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>

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