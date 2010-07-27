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
    <th class="title" colspan="10">
    	{{$line_count}} {{tr}}Results{{/tr}}
		</th>
	</tr>
	
  <tr>
    <th>{{mb_title object=$filter field=_patient_id}}</th>
    <th>{{mb_title object=$filter field=product_id}}</th>
    <th>{{mb_title object=$filter field=order_item_reception_id}}</th>
  </tr>	
	
	{{foreach from=$lines item=_line}}
	<tr>
    <td>{{mb_value object=$filter field=_patient_id}}</td>
    <td>{{mb_value object=$filter field=product_id}}</td>
    <td>{{mb_value object=$filter field=order_item_reception_id}}</td>
  </tr>
	
	{{foreachelse}}
	<tr><em>{{tr}}None{{/tr}}</em></tr>
	{{/foreach}}

</table>