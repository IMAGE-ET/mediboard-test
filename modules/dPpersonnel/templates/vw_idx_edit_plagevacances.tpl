{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<form name="editplage" action="" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: function() { loadUser({{$user_id}}) ; editPlageVac('',{{$user_id}})} } );">
  <input type="hidden" name="dosql" value="do_plagevac_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="user_id" value="{{$user_id}}" />
	<input type="hidden" name="plage_id" value="{{$plage_id}}" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
  
	  {{if $plage_id}}
		  <th class = "title modify" colspan="6">
		  	{{mb_include module=system template=inc_object_history object=$plagevac}}
	    {{tr}}CPlageVacances-title-modify {{/tr}} {{$user->_user_last_name}} {{$user->_user_first_name}}
	  </th>
		</tr>
		<tr>
	    <td>
	      {{mb_label object=$plagevac field="libelle"}}
	    </td>
	    <td>
	      {{mb_field object=$plagevac field="libelle"}}
	    </td>
	    <td>
	      {{mb_label object=$plagevac field="date_debut"}}
	    </td>
	    <td>
	      {{mb_field object=$plagevac field="date_debut" form="editplage" register="true"}}
	    </td>
	    <td>
	      {{mb_label object=$plagevac field="date_fin"}}
	    </td>
	    <td>
	      {{mb_field object=$plagevac field="date_fin" form="editplage" register="true"}}
	    </td>
		</tr>
	  <tr>
	    <td colspan="6" class="button">
	    	<button class = "submit" type="submit">{{tr}}Save{{/tr}}</button>
				{{if $plage_id}}
	        <button class="trash" type="submit" onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plagevac->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
	      {{/if}}
	    </td>
    </tr>
	  {{else}}
	  <tr>
		  <th class = "title" colspan="6">
	     {{tr}}CPlageVacances-title-create{{/tr}} {{$user->_user_last_name}} {{$user->_user_first_name}}
	    </th>
    </tr>
    <tr>
	    <td>
	      {{mb_label object=$new_plagevac field="libelle"}}
	    </td>
	    <td>
	      {{mb_field object=$new_plagevac field="libelle"}}
	    </td>
	    <td>
	      {{mb_label object=$new_plagevac field="date_debut"}}
	    </td>
	    <td>
	      {{mb_field object=$new_plagevac field="date_debut" form="editplage" register="true"}}
	    </td>
	    <td>
	      {{mb_label object=$new_plagevac field="date_fin"}}
	    </td>
	    <td>
	      {{mb_field object=$new_plagevac field="date_fin" form="editplage" register="true"}}
	    </td>
	  </tr>
	  <tr>
	    <td colspan="6" class="button">
	      <button class = "submit" type="submit">{{tr}}Save{{/tr}}</button>
	    </td>
	  </tr>
		{{/if}}
  </table>
</form>