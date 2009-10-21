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
    <th>{{mb_title class=COperation field=date}}</th>
    <th>{{mb_label class=COperation field=chir_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{mb_label class=COperation field=cote}}</th>
    <th>{{mb_label class=COperation field=materiel}}</th>
    {{if !$dialog}}
    <th>{{mb_label class=COperation field=commande_mat}}</th>
    {{/if}}

  </tr>

  {{foreach from=$_operations item=_operation}}
  <tr>
    <td style="text-align: center;">
      {{mb_ditto name=date value=$_operation->_datetime|date_format:$dPconfig.date}}
      <br />{{mb_ditto name=weekday value=$_operation->_datetime|date_format:"%A"}}
			{{if $_operation->annulee}}
			<div class="cancelled">{{tr}}Cancelled{{/tr}}</div>
			{{/if}}
    </td>
    
    {{assign var=chir value=$_operation->_ref_chir}}
    <td><span class="mediuser" style="border-color: #{{$chir->_ref_function->color}};">{{$chir}}</span></td>
    
		{{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
    <td class="text">
    	<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
    	{{$patient}}
			</span>
		</td>
		
    <td class="text">
    	{{if !$dialog}}
			<a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$_operation->_id}}">
      {{/if}}

      {{mb_include module=dPplanningOp template=inc_vw_operation _operation=$_operation}}

      {{if !$dialog}}
      </a>
      {{/if}}
    </td>
  
    <td>{{mb_value object=$_operation field=cote}}</td>
    <td>{{mb_value object=$_operation field=materiel}}</td>

    {{if !$dialog}}
    <td>
      <form name="Edit-{{$_operation->_guid}}" action="?m=dPbloc" method="post">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        {{mb_key object=$_operation}}
        {{if $commande_mat}}
        <input type="hidden" name="commande_mat" value="0" />
        <button type="submit" class="cancel">{{tr}}Cancel{{/tr}}</button>
        {{else}}
        <input type="hidden" name="commande_mat" value="1" />
        <button type="submit" class="submit">{{tr}}COperation.commande_mat.1{{/tr}}</button>
        {{/if}}
      </form>
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
	<tr><td colspan="10"><em>{{tr}}COperation.none{{/tr}}</em></td></tr>
  {{/foreach}}
</table>