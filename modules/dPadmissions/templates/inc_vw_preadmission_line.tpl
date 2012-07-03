{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="patient" value=$curr_consult->_ref_patient}}
{{assign var="curr_adm" value=$curr_consult->_ref_consult_anesth->_ref_sejour}}
{{if !$curr_adm->_id}}
{{if $curr_consult->_next_sejour_and_operation.COperation->_id}}
{{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.COperation->_ref_sejour}}
{{assign var="type_event" value="COperation"}}
{{else}}
{{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.CSejour}}
{{assign var="type_event" value="CSejour"}}
{{/if}}
{{/if}}

<td class="text">
  {{if $curr_adm->_id && !$curr_adm->annule && $curr_consult->_ref_consult_anesth->_ref_sejour->_id}}
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
  </a>
</td>
<td class="text">
  <div class="{{if $curr_consult->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0px;">
  {{$curr_consult->heure|date_format:$conf.time}}
  -
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_plageconsult->_ref_chir}}
  </div>
</td>

{{if $curr_adm->_id}}

{{if $curr_adm->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $curr_adm->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $curr_adm->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $curr_adm->type == 'urg'}} {{assign var=background value="#ff6"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_adm->_ref_praticien}}
</td>
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <div style="float: right;">
    {{mb_include module=system template=inc_object_notes object=$curr_adm}}
  </div>
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$curr_adm}}
  <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_adm->_guid}}');">
  le {{$curr_adm->_entree|date_format:$conf.date}}
  </span>
</td>
{{if !$curr_adm->annule && $curr_consult->_ref_consult_anesth->_ref_sejour->_id}}
<td class="text" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include template=inc_form_prestations sejour=$curr_adm edit=$canAdmissions->edit}}
  {{mb_include module=hospi template=inc_placement_sejour sejour=$curr_adm}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
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

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $curr_adm->_couvert_cmu}}
    <img src="images/icons/tick.png" title="Droits CMU en cours" />
  {{else}}
    -
  {{/if}}
</td>

<td style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{foreach from=$curr_adm->_ref_operations item=curr_op}}
  {{if $curr_op->depassement}}
  <!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
  <!-- <a href="#" onclick="printDepassement({{$curr_adm->sejour_id}})"></a> -->
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
<td colspan="4" class="button" style="background: {{$background}}; {{if !$curr_adm->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
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
  {{mb_key object=$curr_consult->_ref_consult_anesth}}
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
  {{mb_key object=$curr_consult->_ref_consult_anesth}}
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