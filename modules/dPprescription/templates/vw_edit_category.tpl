{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_id=0" class="button new">
        Créer une catégorie
      </a>
      <table class="tbl">
        {{foreach from=$categories key=chapitre item=_categories}}
          {{if $_categories}}
          <tr>
            <th colspan="2">
              {{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}
            </th>
          </tr>
          {{foreach from=$_categories item=_cat}}
          <tr {{if $category->_id == $_cat->_id}}class="selected"{{/if}}>
            <td>
              <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_id={{$_cat->_id}}">
                {{$_cat->nom}}
              </a>
            </td>
            <td>
              {{if $_cat->group_id}}
                {{$_cat->_ref_group->_view}}
              {{else}}
                Tous
              {{/if}}
            </td>
          </tr>
          {{/foreach}}
          {{/if}}
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_prescription_aed" />
	    <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->_id}}
          <th class="category modify" colspan="2">
			      {{mb_include module=system template=inc_object_idsante400 object=$category}}
			      {{mb_include module=system template=inc_object_history object=$category}}
            Modification de la catégorie &lsquo;{{$category}}&rsquo;
          {{else}}
          <th class="category" colspan="2">
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
              {{tr}}Modify{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$category->nom|smarty:nodefaults|JSAttribute}}'})">
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
    </td>
  </tr>
</table>