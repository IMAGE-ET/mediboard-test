{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-CTechnicien" action="?m={{$m}}" method="post" onsubmit="return Technicien.onSubmit(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_technicien_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$technicien}}
{{mb_field object=$technicien field=plateau_id hidden=1}}

<a class="button new" href="#Edit-CTechnicien-0" onclick="Technicien.edit('{{$plateau->_id}}', '0')">
  {{tr}}CTechnicien-title-create{{/tr}}
</a>
<table class="form">
  <tr>
    {{if $technicien->_id}}
    <th class="title modify" colspan="4">
      {{mb_include module=system template=inc_object_notes      object=$technicien}}
      {{mb_include module=system template=inc_object_idsante400 object=$technicien}}
      {{mb_include module=system template=inc_object_history    object=$technicien}}

      
	    {{tr}}CTechnicien-title-modify{{/tr}} 
			'{{$technicien}}'
    </th>
    {{else}}
    <th class="title" colspan="4">
	    {{tr}}CTechnicien-title-create{{/tr}} 
	  </th>
    {{/if}}
  </tr>
    
  <tr>
    <th>{{mb_label object=$technicien field=kine_id}}</th>
    <td>
      <select name="kine_id" class="{{$technicien->_props.kine_id}}">
        <option value="0">&mdash; {{tr}}Choose{{/tr}}</option>
				{{mb_include module=mediusers template=inc_options_mediuser list=$kines selected=$technicien->kine_id}}
        </select>
    </td>
  </tr>

  <tr>
		<td class="button" colspan="4">
		  {{if $technicien->_id}}
			  <button class="modify" type="submit">
			  	{{tr}}Save{{/tr}}
				</button>
			  <button class="trash" type="button" onclick="confirmDeletion(this.form, {
				  typeName:'le technicien ',
					objName:'{{$technicien->_view|smarty:nodefaults|JSAttribute}}',
					ajax: 1})">
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