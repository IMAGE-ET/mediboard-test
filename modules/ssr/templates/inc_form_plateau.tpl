{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-CPlateauTechnique" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_plateau_technique_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$plateau}}
{{mb_field object=$plateau field=group_id hidden=1}}

<input type="hidden" name="_id" value="{{$plateau->_id}}" />

<a class="button new" href="?m={{$m}}&amp;tab=vw_idx_plateau&amp;plateau_id=0">
  {{tr}}CPlateauTechnique-title-create{{/tr}}
</a>
<table class="form">
  <tr>
    {{if $plateau->_id}}
    <th class="title modify" colspan="4">
      {{mb_include module=system template=inc_object_notes      object=$plateau}}
      {{mb_include module=system template=inc_object_idsante400 object=$plateau}}
      {{mb_include module=system template=inc_object_history    object=$plateau}}

      
	    {{tr}}CPlateauTechnique-title-modify{{/tr}} 
			'{{$plateau}}'
    </th>
    {{else}}
    <th class="title" colspan="4">
	    {{tr}}CPlateauTechnique-title-create{{/tr}} 
	  </th>
    {{/if}}
  </tr>
  
  {{if $plateau->group_id != $g}}
  <tr>
    <th>{{mb_label object=$plateau field=group_id}}</th>
    <td>{{mb_value object=$plateau field=group_id}}</td>
  </tr>
  {{/if}}
  
  <tr>
    <th>{{mb_label object=$plateau field=nom}}</th>
    <td>{{mb_field object=$plateau field=nom}}</td>
  </tr>

  <tr>
		<td class="button" colspan="4">
		  {{if $plateau->_id}}
			  <button class="modify" type="submit">
			  	{{tr}}Save{{/tr}}
				</button>
			  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le plateau ',objName:'{{$plateau->_view|smarty:nodefaults|JSAttribute}}'})">
			    {{tr}}Delete{{/tr}}
			  </button>
				    
	    {{else}}
		    <button class="submit" type="submit">
		    	{{tr}}Create{{/tr}}
				</button>
      {{/if}}
  	</td>
  </tr>
  
</table>

</form>