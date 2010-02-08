{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Edit-CEquipement" action="?m={{$m}}" method="post" onsubmit="return Equipement.onSubmit(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_equipement_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$equipement}}
{{mb_field object=$equipement field=plateau_id hidden=1}}

<table class="form">
  <tr>
    {{if $equipement->_id}}
    <th class="title modify" colspan="4">
      {{mb_include module=system template=inc_object_notes      object=$equipement}}
      {{mb_include module=system template=inc_object_idsante400 object=$equipement}}
      {{mb_include module=system template=inc_object_history    object=$equipement}}

      
	    {{tr}}CEquipement-title-modify{{/tr}} 
			'{{$equipement}}'
    </th>
    {{else}}
    <th class="title" colspan="4">
	    {{tr}}CEquipement-title-create{{/tr}} 
	  </th>
    {{/if}}
  </tr>
    
  <tr>
    <th>{{mb_label object=$equipement field=nom}}</th>
    <td>{{mb_field object=$equipement field=nom}}</td>
  </tr>

  <tr>
		<td class="button" colspan="4">
		  {{if $equipement->_id}}
			  <button class="modify" type="submit">
			  	{{tr}}Save{{/tr}}
				</button>
			  <button class="trash" type="button" onclick="confirmDeletion(this.form, {
				  typeName:'l\'équipement ',
					objName:'{{$equipement->_view|smarty:nodefaults|JSAttribute}}',
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