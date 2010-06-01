{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editCategory" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">
	<input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_category_prescription_aed" />
	<input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="callback" value="refreshListCategoriesCallback" />
	<table class="form">
	  <tr>
	    {{if $category->_id}}
	    <th class="title text modify" colspan="2">
	      {{mb_include module=system template=inc_object_idsante400 object=$category}}
	      {{mb_include module=system template=inc_object_history object=$category}}
	      Modification de la catégorie &lsquo;{{$category}}&rsquo;
	    {{else}}
	    <th class="title text" colspan="2">
	      Création d'une catégorie
	    {{/if}}
	    </th>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="chapitre"}}</th>
	    <td>{{mb_field object=$category field="chapitre" defaultOption="&mdash; Sélection d'un chapitre"}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="nom"}}</th>
	    <td>{{mb_field object=$category field="nom"}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="description"}}</th>
	    <td>{{mb_field object=$category field="description"}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="header"}}</th>
	    <td>{{mb_field object=$category field="header"}}</td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="color"}}</th>
	    <td>
	      <a href="#1" id="select_color_cat" style="background: #{{$category->color}}; padding: 0 3px; border: 1px solid #aaa;" onclick="ColorSelector.init('editCategory','select_color_cat')">Cliquer pour changer</a>
	      {{mb_field object=$category field="color" hidden=1}}
	      <button type="button" class="cancel" onclick="$('select_color_cat').setStyle({ background: '' }); $V(this.form.color, '');">Vider</button>
	    </td>
	  </tr>
	  <tr>
	    <th>{{mb_label object=$category field="group_id"}}</th>
	    <td>
	      <select name="group_id">
	        <option value="">Tous</option>
	      {{foreach from=$groups item=_group}}
	        <option value="{{$_group->_id}}" {{if $category->group_id == $_group->_id}}selected="selected"{{/if}}>{{$_group->_view}}</option>
	      {{/foreach}}
	      </select>
	    </td>
	  </tr>
	  <tr>
	    <td class="button" colspan="2">
	    {{if $category->_id}}
	      <button class="modify" type="submit" name="modify">
	        {{tr}}Save{{/tr}}
	      </button>
	      <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'la catégorie', objName:'{{$category->nom|smarty:nodefaults|JSAttribute}}'})">
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
