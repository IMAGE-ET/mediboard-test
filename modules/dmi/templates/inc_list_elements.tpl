{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage$object_class" 
    total=$total current=$start step=30}}

<table class="tbl" id="list_elements">
  <tr>
    <th>{{mb_title class=$object_class field=nom}}</th>
    <th>{{mb_title class=$object_class field=code}}</th>
    <th>{{mb_title class=$object_class field=in_livret}}</th>
    <th>{{mb_title class=$object_class field=_produit_existant}}</th>
  </tr>
	{{foreach from=$list_elements item=_element}}
  <tr id="element-{{$_element->_id}}">
    <td>
      <a href="#" onclick="$(this).up('tr').addUniqueClassName('selected'); viewElement('{{$_element->_class_name}}','{{$_element->_id}}')">
    	{{mb_value object=$_element field=nom}}
    	</a>
    </td>
    <td>
    	{{mb_value object=$_element->_ref_product field=code}}
    </td>
    <td>
    	{{mb_value object=$_element field=in_livret}}
    </td>
    <td>
      {{assign var=_ref_product value=$_element->_ref_product}}
      
      <span {{if $_ref_product->_id}}onmouseover="ObjectTooltip.createEx(this, '{{$_ref_product->_guid}}')"{{/if}}> 
      {{if $_ref_product->_id}}
        <a href="?m=dPstock&amp;tab=vw_idx_product&amp;product_id={{$_ref_product->_id}}">
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
</table>