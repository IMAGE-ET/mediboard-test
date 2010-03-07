{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}
	
{{if !$rhs->_in_bounds}} 
<div class="small-warning">
	Le séjour ne comporte aucune journée dans la semaine de ce RHS.
	<br/>
	Ce RHS <strong>doit être supprimé</strong>.
</div>
{{/if}}

<form name="Edit-CRHS-{{$rhs->_date_sunday}}" action="?m={{$m}}" method="post" onsubmit="return CotationRHS.onSubmitRHS(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_rhs_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$rhs}}
{{mb_field object=$rhs field=sejour_id  hidden=1}}

<table class="form">
  <tr>
    {{if $rhs->_id}}
    <th class="title modify" colspan="4">
      {{mb_include module=system template=inc_object_notes      object=$rhs}}
      {{mb_include module=system template=inc_object_idsante400 object=$rhs}}
      {{mb_include module=system template=inc_object_history    object=$rhs}}
      
      {{tr}}CRHS-title-modify{{/tr}} 
      '{{$rhs}}'
    </th>
    {{/if}}
  </tr>
    
  {{if !$rhs->_id}}
  <tr>
    <th>{{mb_label object=$rhs field=date_monday}}</th>
    <td>{{mb_field object=$rhs field=date_monday  readonly=1}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$rhs field=_date_sunday}}</th>
    <td>{{mb_field object=$rhs field=_date_sunday  readonly=1}}</td>
  </tr>
  {{/if}}

  <tr>
    <td class="button" colspan="4">
      {{if $rhs->_id}}
        <button class="modify" type="submit">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {
          typeName:'le RHS',
          objName:'{{$rhs->_view|smarty:nodefaults|JSAttribute}}',
          ajax: 1})">
          {{tr}}Delete{{/tr}}
        </button>
            
      {{else}}
        <button class="new" type="submit">
          {{tr}}CRHS-title-create{{/tr}}
        </button>
      {{/if}}
    </td>
  </tr>
  
</table>

</form>