{{* $Id: vw_idx_materiel.tpl 7070 2009-10-15 14:18:06Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: 7070 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table id="commande_mat_{{$commande_mat}}" style="display: none;" class="tbl">
  <tr>
    <th class="title" colspan="8">
      {{tr}}CCommandeMaterielOp{{/tr}} - {{tr}}CCommandeMaterielOp.etat.{{$commande_mat}}{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=COperation field=date}}</th>
    <th>{{mb_label class=COperation field=chir_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{mb_label class=COperation field=cote}}</th>
    <th>{{mb_label class=COperation field=materiel}}</th>
    <th>{{mb_label class=COperation field=commande_mat}}</th>
    <th style="width: 100px;">{{mb_label class=CCommandeMaterielOp field=commentaire}}</th>
  </tr>

  {{foreach from=$_operations item=_operation}}
    {{assign var=chir value=$_operation->_ref_chir}}
    {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
    {{assign var=commande value=$_operation->_ref_commande_mat}}
    <tr>
      <td style="text-align: center;">
        {{mb_ditto name=date_$commande_mat value=$_operation->_datetime|date_format:$conf.date}}<br />{{mb_ditto name=weekday_$commande_mat value=$_operation->_datetime|date_format:"%A"}}
        {{if $_operation->annulee}}
          <div class="cancelled">{{tr}}Cancelled{{/tr}}</div>
        {{/if}}
      </td>

      <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$chir}}</td>

      <td class="text">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
        {{$patient}}
        </span>
      </td>

      <td class="text">
        {{if !$dialog}}
          {{if !$_operation->plageop_id}}
            <a href="?m=planningOp&tab=vw_edit_urgence&operation_id={{$_operation->_id}}">
          {{else}}
            <a href="?m=planningOp&tab=vw_edit_planning&operation_id={{$_operation->_id}}">
          {{/if}}
        {{/if}}

        {{mb_include module=planningOp template=inc_vw_operation _operation=$_operation}}

        {{if !$dialog}}
        </a>
        {{/if}}
      </td>

      <td>{{mb_value object=$_operation field=cote}}</td>
      <td>{{mb_value object=$_operation field=materiel}}</td>

      <td class="button">

        <form name="Edit-{{$_operation->_guid}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
          {{mb_key   object=$commande}}
          {{mb_class object=$commande}}
          <input type="hidden" name="operation_id" value="{{$_operation->_id}}"/>
          <input type="hidden" name="etat" value="" />

          {{if !$commande->_id || $commande->etat == "a_commander" || $commande->etat == "modify"}}
            <button type="button" class="add" onclick="Commande.changeEtat(this.form, 'commandee');">{{tr}}CCommandeMaterielOp-title-create{{/tr}}</button>
          {{/if}}

          {{if $commande->_id}}
            {{if $commande->etat != "annulee"}}
              <button type="button" class="cancel" onclick="Commande.changeEtat(this.form, 'annulee');">{{tr}}Cancel{{/tr}}</button>
            {{/if}}

            {{if $commande->etat == "commandee"}}
              <button type="button" class="tick" onclick="Commande.changeEtat(this.form, 'recue');">{{tr}}CCommandeMaterielOp.etat.{{$commande_mat}}.action{{/tr}}</button>
            {{/if}}
          {{/if}}

        </form>

        {{if $commande->_id}}
          <button type="button" class="edit" onclick="Commande.edit('{{$commande->_id}}');">
            {{tr}}CCommandeMaterielOp{{/tr}}
          </button>
        {{/if}}

      </td>
      <td class="text">{{mb_value object=$commande field=commentaire}}</td>
    </tr>
  {{foreachelse}}
    <tr><td colspan="10" class="empty">{{tr}}COperation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>