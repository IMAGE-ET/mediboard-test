{{* $Id: CPrescriptionLineMix_view.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{include file=CMbObject_view.tpl}}

{{assign var=prescription value=$object}}

<table class="tbl">
  {{if $prescription->_ref_prescription_lines|@count}}
    <tr>
      <th class="title" colspan="2">{{tr}}CPrescription._chapitres.med{{/tr}}</th>  
    </tr>
  {{/if}}
  {{foreach from=$prescription->_ref_prescription_lines item=_line_med}}
  <tr>
    <td colspan="2" class="text">{{$_line_med->_view}}</td>
  </tr>
  {{/foreach}}
  {{foreach from=$prescription->_ref_prescription_line_mixes item=_perf}}
  <tr>
    <td colspan="2" class="text">{{$_perf->_view}}</td>
  </tr>
  {{/foreach}}
	
  {{foreach from=$prescription->_ref_prescription_lines_element_by_cat key=chap item=_lines_by_chap}}
    <tr>
      <th class="title" colspan="2">{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
    </tr>
    {{foreach from=$_lines_by_chap key=cat item=_lines_by_cat}}
      
      {{foreach from=$_lines_by_cat.element item=_line_elt name="foreach_elt"}}
        <tr>
	        {{if $smarty.foreach.foreach_elt.first}}
					  <th class="category" rowspan="{{$_lines_by_cat.element|@count}}">
	            {{$_line_elt->_ref_element_prescription->_ref_category_prescription->_view}}
	          </th>
	        {{/if}}
          <td class="text">{{$_line_elt->_view}}</td>
        </tr>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>