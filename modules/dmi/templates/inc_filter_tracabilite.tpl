{{* $Id: inc_div_dmi.tpl 9439 2010-07-12 16:40:48Z phenxdesign $ *}}

{{*
  * @package Mediboard
  * @subpackage dmi
  * @version $Revision: 9439 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<form class="not-printable" name="Filter" action="?" method="get">
  <input name="m" value="{{$m}}" type="hidden" />
  <input name="{{$actionType}}" value="{{$action}}" type="hidden" />
  <input name="dialog" value="{{$dialog}}" type="hidden" />

  <table class="form">
  	<tr>
      <th>{{mb_label object=$filter field=_patient_id}}</th>
      <td>{{mb_field object=$filter field=_patient_id}}</td>
      <th>{{mb_label object=$filter field=product_id}}</th>
      <td>{{mb_field object=$filter field=product_id canNull=true}}</td>
      <th>{{mb_label object=$filter field=order_item_reception_id}}</th>
      <td>{{mb_field object=$filter field=order_item_reception_id canNull=true}}</td>
  	</tr>
  </table>
  
	
</form>
