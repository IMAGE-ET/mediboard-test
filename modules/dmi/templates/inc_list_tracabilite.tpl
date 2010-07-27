{{* $Id: inc_div_dmi.tpl 9439 2010-07-12 16:40:48Z phenxdesign $ *}}

{{*
  * @package Mediboard
  * @subpackage dmi
  * @version $Revision: 9439 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<table class="tbl">
  <tr>
    <th>{{mb_title object=$filter field=_patient_id}}</th>
    
    <th colspan="3">
      {{mb_title object=$filter field=product_id}} /
      {{mb_title class=CProduct field=code}}
    </th>
    
    <th colspan="3">
      {{mb_title object=$filter field=order_item_reception_id}} / 
      {{mb_title class=CProductOrderItemReception field=lapsing_date}}
    </th>
  </tr>	
	
	{{foreach from=$lines item=_line}}
  	<tr>
      <td style="width: 15%;">{{mb_value object=$_line->_ref_operation->_ref_sejour field=patient_id}}</td>
      
      <th style="width: 1px;"></th>
      <td style="width: 25%;">{{mb_value object=$_line->_ref_product field=name}}</td>
      <td style="width: 25%;">{{mb_value object=$_line->_ref_product field=code}}</td>
      
      <th style="width: 1px;"></th>
      <td style="width: 17%;">{{mb_value object=$_line->_ref_product_order_item_reception field=code}}</td>
      <td style="width: 17%;">{{mb_value object=$_line->_ref_product_order_item_reception field=lapsing_date}}</td>
    </tr>
	{{foreachelse}}
  	<tr>
  	  <td colspan="3">{{tr}}None{{/tr}}</td>
    </tr>
	{{/foreach}}

</table>