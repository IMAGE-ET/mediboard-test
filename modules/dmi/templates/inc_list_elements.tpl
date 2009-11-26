{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<button type="button" class="new" onclick="removeSelectedTr(); viewElement('{{$object_class}}', '0')">
  {{tr}}{{$object_class}}-title-create{{/tr}}
</button>

<table class="tbl" id="list_elements">
  <tr>
    <th>{{mb_title class=$object_class field=nom}}</th>
    <th>{{mb_title class=$object_class field=description}}</th>
    <th>{{mb_title class=$object_class field=code}}</th>
    <th>{{mb_title class=$object_class field=in_livret}}</th>
    <th>{{mb_title class=$object_class field=_produit_existant}}</th>
  </tr>
	{{foreach from=$categories item=_category}}
		<tr>
		  <th class="category" colspan="10">
			    {{$_category->_view}}
		  </th>
		</tr>
		{{foreach from=$_category->_ref_elements item=_element}}
	  <tr id="element-{{$_element->_id}}">
	    <td>
	      <a href="#" onclick="markAsSelected(this); viewElement('{{$_element->_class_name}}','{{$_element->_id}}')">
	    	{{mb_value object=$_element field=nom}}
	    	</a>
	    </td>
	    <td>
	    	{{mb_value object=$_element field=description}}
	    </td>
	    <td>
	    	{{mb_value object=$_element field=code}}
	    </td>
	    <td>
	    	{{mb_value object=$_element field=in_livret}}
	    </td>
	    <td>
	      <span {{if $_element->_ext_product->_id}}onmouseover="ObjectTooltip.createEx(this, '{{$_element->_ext_product->_guid}}')"{{/if}}> 
        {{if $_element->_produit_existant}}
          <a href="?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$_element->_ext_product->_id}}">
          {{mb_value object=$_element field=_produit_existant}}
          </a>
        {{else}}
          {{mb_value object=$_element field=_produit_existant}}
        {{/if}}
        </span>
      </td>
	  </tr>
	  {{foreachelse}}
	  <tr>
	    <td colspan="10"><em>{{tr}}{{$object_class}}.none{{/tr}}</em></td>
	  </tr>
		{{/foreach}}
  {{foreachelse}}
  <tr>
    <td colspan="10"><em>{{tr}}{{$category_class}}.none{{/tr}}</em></td>
  </tr>
	{{/foreach}}
</table>