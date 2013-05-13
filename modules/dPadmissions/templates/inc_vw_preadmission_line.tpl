{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="patient" value=$curr_consult->_ref_patient}}
{{assign var=dossiers_anesth value=$curr_consult->_refs_dossiers_anesth}}
{{assign var=dossier_anesth value=""}}

{{if $curr_consult->_dossier_anesth_completed_id}}
  {{assign var=dossier_anesth_id value=$curr_consult->_dossier_anesth_completed_id}}
  {{assign var=dossier_anesth value=$dossiers_anesth.$dossier_anesth_id}}
  {{assign var="curr_adm" value=$dossier_anesth->_ref_sejour}}
{{else}}
  {{if $curr_consult->_next_sejour_and_operation.COperation->_id}}
    {{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.COperation->_ref_sejour}}
    {{assign var="type_event" value="COperation"}}
  {{else}}
    {{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.CSejour}}
    {{assign var="type_event" value="CSejour"}}
  {{/if}}
{{/if}}

<td class="text">
  {{if $curr_adm->_id && !$curr_adm->annule && $dossier_anesth && $dossier_anesth->_ref_sejour->_id}}
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  <a class="action" style="float: right" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$curr_op->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{foreachelse}}
  <a class="action" style="float: right" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$curr_adm->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{/foreach}}
  {{/if}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient->_view}}
  </span>
</td>
<td class="text">
  <div class="{{if $curr_consult->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0;">
  {{$curr_consult->heure|date_format:$conf.time}}
  -
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_plageconsult->_ref_chir}}
  </div>
</td>

{{if $curr_adm->_id}}

{{assign var=cell_style value="background: #ccc;"}}

{{if     $curr_adm->type == 'ambu'}} {{assign var=cell_style value="background: #faa;"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=cell_style value="background: #fff;"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=cell_style value="background: #afa;"}}
{{elseif $curr_adm->type == 'urg'}}  {{assign var=cell_style value="background: #ff6;"}}
{{/if}}

{{if !$curr_adm->facturable}}
  {{assign var=cell_style value="$cell_style background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;"}}
{{/if}}

<td class="text" style="{{$cell_style}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_adm->_ref_praticien}}
</td>
<td class="text" style="{{$cell_style}}">
  <div style="float: right;">
    {{mb_include module=system template=inc_object_notes object=$curr_adm}}
  </div>
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$curr_adm _show_numdoss_modal=1}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_adm->_guid}}');">
  le {{$curr_adm->_entree|date_format:$conf.date}}
  </span>
</td>
{{if !$curr_adm->annule && $dossier_anesth && $dossier_anesth->_ref_sejour->_id}}
<td class="text" style="{{$cell_style}}">
  {{mb_include template=inc_form_prestations sejour=$curr_adm edit=$canAdmissions->edit}}
  {{mb_include module=hospi template=inc_placement_sejour sejour=$curr_adm}}
</td>

<td style="{{$cell_style}}">
  {{if $canAdmissions->edit}}
  <form name="editSaisFrm{{$curr_adm->_id}}" action="?" method="post">

  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
  <input type="hidden" name="patient_id" value="{{$curr_adm->patient_id}}" />
  {{if !$curr_adm->entree_preparee}}
  <input type="hidden" name="entree_preparee" value="1" />
  <button class="tick" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}CSejour-entree_preparee{{/tr}}
  </button>
  {{else}}
  <input type="hidden" name="entree_preparee" value="0" />
  <button class="cancel" type="button" onclick="submitPreAdmission(this.form);">
    {{tr}}Cancel{{/tr}}
  </button>
  {{/if}}
  {{if ($curr_adm->entree_modifiee == 1) && ($conf.dPplanningOp.CSejour.entree_modifiee == 1)}}
    <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
  {{/if}}
  </form>
  {{else}}
  {{mb_value object=$curr_adm field="entree_preparee"}}
  {{/if}}
</td>

<td style="{{$cell_style}}">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

<td style="{{$cell_style}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->depassement}}
    {{mb_value object=$curr_op field="depassement"}}
    <br />
  {{/if}}
  {{foreachelse}}
  -
  {{/foreach}}
</td>
{{elseif $curr_adm->annule}}
<td colspan="4" class="cancelled">
  Annulé
</td>
{{else}}
<td colspan="4" class="button" style="{{$cell_style}}">
  {{if $type_event == "COperation"}}
  <a class="action" style="float: right" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$curr_consult->_next_sejour_and_operation.COperation->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  Intervention non associé à la consultation
  {{if $canAdmissions->edit}}
  <br />
  <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{if $dossiers_anesth|@count > 1}}
    <select name="consultation_anesth_id">
      {{foreach from=$dossiers_anesth item=_dossier_anesth}}
        <option value="{{$_dossier_anesth->_id}}">{{$_dossier_anesth}}</option>
      {{/foreach}}
    </select>
  {{else}}
    {{mb_key object=$dossiers_anesth|@reset}}
  {{/if}}
  <input type="hidden" name="operation_id" value="{{$curr_consult->_next_sejour_and_operation.COperation->_id}}" />
  <button type="submit" class="tick">
    Associer l'intervention
  </button>
  </form>
  {{/if}}
  {{else}}
  <a class="action" style="float: right" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$curr_adm->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  Séjour non associé à la consultation
  {{if $canAdmissions->edit}}
  <br />
  <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPcabinet" />
  {{if $dossiers_anesth|@count > 1}}
    <select name="consultation_anesth_id">
      {{foreach from=$dossiers_anesth item=_dossier_anesth}}
        <option value="{{$_dossier_anesth->_id}}">{{$_dossier_anesth}}</option>
      {{/foreach}}
    </select>
  {{else}}
    {{mb_key object=$dossiers_anesth|@reset}}
  {{/if}}
  <input type="hidden" name="sejour_id" value="{{$curr_adm->_id}}" />
  <button type="submit" class="tick">
    Associer le séjour
  </button>
  </form>
  {{/if}}
  {{/if}}
</td>
{{/if}}
{{else}}
<td colspan="6" class="button">
  DHE non trouvée
  {{if $canPlanningOp->edit}}
  :
  <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;pat_id={{$curr_consult->patient_id}}&amp;operation_id=0&amp;sejour_id=0" class="button new">
    Créer une demande d'hospitalisation
  </a>
  {{/if}}
</td>
{{/if}}