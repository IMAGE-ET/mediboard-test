{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr id="consultation{{$curr_consult->consultation_id}}">

  {{assign var="patient" value=$curr_consult->_ref_patient}}
  {{assign var=dossiers_anesth value=$curr_consult->_refs_dossiers_anesth}}
  {{if is_array($curr_consult->_next_sejour_and_operation)}}
    {{if $curr_consult->_next_sejour_and_operation.COperation->_id}}
      {{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.COperation->_ref_sejour}}
      {{assign var="type_event" value="COperation"}}
    {{else}}
      {{assign var="curr_adm" value=$curr_consult->_next_sejour_and_operation.CSejour}}
      {{assign var="type_event" value="CSejour"}}
    {{/if}}
  {{/if}}

  <td class="text" rowspan="{{$dossiers_anesth|@count}}">
    <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
      {{$patient->_view}}
    </span>
  </td>
  <td class="text" rowspan="{{$dossiers_anesth|@count}}">
    <div class="{{if $curr_consult->chrono == 64}}small-success{{else}}small-info{{/if}}" style="margin: 0;">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_consult->_guid}}')">{{$curr_consult->heure|date_format:$conf.time}}</span>
      <br/>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_consult->_ref_plageconsult->_ref_chir}}
    </div>
  </td>

  {{foreach from=$dossiers_anesth item=_dossier name=dossiers_anesth}}
    {{assign var=_sejour value=""}}
    {{if !$smarty.foreach.dossiers_anesth.first}}
      <tr>
    {{/if}}
    {{if $_dossier->_ref_sejour->_id}}
      {{assign var=_sejour value=$_dossier->_ref_sejour}}
    {{elseif $curr_consult->_next_sejour_and_operation.CSejour->_id}}
      {{assign var=_sejour value=$curr_consult->_next_sejour_and_operation.CSejour}}
    {{/if}}

    {{if $_sejour && $_sejour->_id}}

      {{assign var=cell_style value="background: #ccc;"}}

      {{if     $_sejour->type == 'ambu'}} {{assign var=cell_style value="background: #faa;"}}
      {{elseif $_sejour->type == 'comp'}} {{assign var=cell_style value="background: #fff;"}}
      {{elseif $_sejour->type == 'exte'}} {{assign var=cell_style value="background: #afa;"}}
      {{elseif $_sejour->type == 'urg'}}  {{assign var=cell_style value="background: #ff6;"}}
      {{/if}}

      {{if !$_sejour->facturable}}
        {{assign var=cell_style value="$cell_style background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;"}}
      {{/if}}

      <td class="text" style="{{$cell_style}}">
        {{foreach from=$_sejour->_ref_operations item=_op name=op_sejour}}
          {{if $smarty.foreach.op_sejour.first}}
            <a class="action" style="float: right;" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$_op->_id}}); return false;">
              <img src="images/icons/print.png" />
            </a>
          {{/if}}
        {{foreachelse}}
          <a class="action" style="float: right;" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
            <img src="images/icons/print.png" />
          </a>
        {{/foreach}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>

      <td class="text" style="{{$cell_style}}">
        <div style="">
          {{mb_include module=system template=inc_object_notes object=$_sejour float=right}}
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour _show_numdoss_modal=1}}
        </div>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
        {{$_sejour->_entree|date_format:$conf.date}}
        </span>
      </td>

      {{if !$_sejour->annule && $_dossier && $_dossier->_ref_sejour->_id}}

        <td class="text" style="{{$cell_style}}">
          {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
          {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
        </td>

        <td style="{{$cell_style}}">
          {{if $canAdmissions->edit}}
            <form name="editSaisFrm{{$_sejour->_id}}" action="?" method="post">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="dosql" value="do_sejour_aed" />
              <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
              <input type="hidden" name="patient_id" value="{{$_sejour->patient_id}}" />

              {{mb_include module=forms template=inc_widget_ex_class_register_multiple object=$_sejour cssStyle="display: inline-block;"}}

              {{if !$_sejour->entree_preparee}}
                <input type="hidden" name="entree_preparee" value="1" />
                <input type="hidden" name="_entree_preparee_trigger" value="1" />
                <button class="tick" type="button" onclick="submitPreAdmission(this.form);">
                  {{tr}}CSejour-entree_preparee{{/tr}}
                </button>
              {{else}}
                <input type="hidden" name="entree_preparee" value="0" />
                <button class="cancel" type="button" onclick="submitPreAdmission(this.form);">
                  {{tr}}Cancel{{/tr}}
                </button>
              {{/if}}
              {{if ($_sejour->entree_modifiee == 1) && ($conf.dPplanningOp.CSejour.entree_modifiee == 1)}}
                <img src="images/icons/warning.png" title="Le dossier a été modifié, il faut le préparer" />
              {{/if}}
            </form>
          {{else}}
            {{mb_value object=$_sejour field="entree_preparee"}}
          {{/if}}
        </td>

        <td style="{{$cell_style}}">
          {{if $_sejour->_couvert_cmu}}
            <img src="images/icons/tick.png" title="Droits CMU en cours" />
          {{else}}
            -
          {{/if}}
        </td>

        <td style="{{$cell_style}}">
          {{foreach from=$_sejour->_ref_operations item=_op}}
          {{if $_op->depassement}}
            {{mb_value object=$_op field="depassement"}}
            <br />
          {{/if}}
          {{foreachelse}}
          -
          {{/foreach}}
        </td>

      {{elseif $_sejour->annule}}

        <td colspan="4" class="cancelled">
          Annulé
        </td>

      {{else}}
        <td colspan="4" class="button" style="{{$cell_style}}">
          {{if $type_event == "COperation"}}
            Intervention non associée à la consultation
            {{if $canAdmissions->edit}}
              <br />
              <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="m" value="dPcabinet" />
              {{mb_key object=$_dossier}}
              <input type="hidden" name="operation_id" value="{{$curr_consult->_next_sejour_and_operation.COperation->_id}}" />
              <input type="hidden" name="postRedirect" value="m={{$m}}" />
              <button type="submit" class="tick">
                Associer l'intervention
              </button>
              </form>
            {{/if}}
          {{else}}
            Séjour non associé à la consultation
            {{if $canAdmissions->edit}}
              <br />
              <form name="addOpFrm-{{$curr_consult->_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="m" value="dPcabinet" />
              {{mb_key object=$_dossier}}
              <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
              <input type="hidden" name="postRedirect" value="m={{$m}}" />
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
          <button onclick="openDHEModal('{{$curr_consult->patient_id}}');" class="button new">
            Créer une demande d'hospitalisation
          </button>
        {{/if}}
      </td>

    {{/if}}

  {{/foreach}}

</tr>