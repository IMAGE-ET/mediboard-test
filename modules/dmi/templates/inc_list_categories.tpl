{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button type="button" class="new" onclick="removeSelectedTr(); viewCat('{{$category_class}}', '0')">
  {{tr}}{{$category_class}}-title-create{{/tr}}
</button>
<table class="tbl" id="list_categories">
  <tr>
    <th>{{mb_title class=$category_class field=nom}}</th>
    <th>{{mb_title class=$category_class field=description}}</th>
    <th>Nombre</th>
  </tr>
  {{foreach from=$categories item=_category}}
    <tr id="cat-{{$_category->_id}}">
      <td>
        <a href="#" onclick="markAsSelected(this); viewCat('{{$_category->_class_name}}','{{$_category->_id}}')">
  		  {{mb_value object=$_category field=nom}}
  	     </a>
      </td> 
      <td>
      	{{mb_value object=$_category field=description}}
      </td>
      <td>
        {{$_category->_count_elements}}
      </td>
    </tr>
   {{foreachelse}}
    <tr>
      <td colspan="10"><em>{{tr}}{{$category_class}}.none{{/tr}}</em></td>
    </tr>
  {{/foreach}}
</table>


