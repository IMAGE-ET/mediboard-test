{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $category_prescription->_id}}
  <hr />
	<form name="editElement" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
		<input type="hidden" name="m" value="dPprescription" />
		<input type="hidden" name="dosql" value="do_element_prescription_aed" />
		<input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
		<input type="hidden" name="category_prescription_id" value="{{$category_prescription->_id}}" />
		<input type="hidden" name="del" value="0" />
		<input type="hidden" name="cancelled" value="{{$element_prescription->cancelled}}" />
		<input type="hidden" name="callback" value="refreshListElement" />
		<table class="form">
		 <tr>
		   {{if $element_prescription->_id}}
			   <th class="title text modify" colspan="2">
			     {{mb_include module=system template=inc_object_idsante400 object=$element_prescription}}
			     {{mb_include module=system template=inc_object_history object=$element_prescription}}
			     Modification de l'element &lsquo;{{$element_prescription->libelle}}&rsquo;
		   {{else}}
			   <th class="title text" colspan="2">
			     Création d'un élément
		   {{/if}}
		   </th>
		 </tr>
		 <tr>
		   <th>{{mb_label object=$element_prescription field="libelle"}}</th>
		   <td>{{mb_field object=$element_prescription field="libelle"}}</td>
		 </tr>
		 <tr>
		   <th>{{mb_label object=$element_prescription field="description"}}</th>
		   <td>{{mb_field object=$element_prescription field="description"}}</td>
		 </tr>
		 <tr>
		   <th>{{mb_label object=$element_prescription field="color"}}</th>
		   <td class="text">
		     <a href="#1" id="select_color_elt" style="background: #{{$element_prescription->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init('editElement','select_color_elt');">Cliquer pour changer</a>
		     {{mb_field object=$element_prescription field="color" hidden=1}}
		     <button type="button" class="cancel" onclick="$('select_color_elt').setStyle({ background: '' }); $V(this.form.color, '');">Vider</button>
		   </td>
		 </tr>
		 <tr>
		   <td colspan="2" class="text">
		   </td>
		 </tr>
		 <tr>
		   <td class="button" colspan="2">
		   {{if $element_prescription->_id}}
		     <button class="modify" type="submit" name="modify">
		       {{tr}}Save{{/tr}}
		     </button>
		     {{if $element_prescription->cancelled}}
		       <button class="tick" type="submit" name="restore" onclick="$V(this.form.cancelled, '0');">
		         {{tr}}Restore{{/tr}}
		       </button>
		     {{else}}
		       <button class="cancel" type="submit" name="cancel" onclick="$V(this.form.cancelled, '1');">
		         {{tr}}Cancel{{/tr}}
		       </button>
		     {{/if}}
		     <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'l\'element', objName:'{{$element_prescription->libelle|smarty:nodefaults|JSAttribute}}'})">
		       {{tr}}Delete{{/tr}}
		     </button>
		   {{else}}
		     <button class="new" type="submit" name="create">
		       {{tr}}Create{{/tr}}
		     </button>
		   {{/if}}
		   </td>
		 </tr>
		</table>
	</form> 
{{/if}}