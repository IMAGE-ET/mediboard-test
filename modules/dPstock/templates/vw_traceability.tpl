{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<form action="?" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="text" name="code" value="{{$code}}" />
  <button type="submit" class="tick notext">{{tr}}Filter{{/tr}}</button>
</form>

<table class="main tbl">
  <tr>
    <th>{{tr}}CProductOrderItemReception{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-date_delivery{{/tr}}</th>
    <th>{{tr}}CProductDeliveryTrace-date_reception{{/tr}}</th>
  </tr>
  {{foreach from=$codes item=curr_code key=code}}
    {{assign var=product value=$products.$code}}
    <tr>
      <th colspan="10" class="category">{{$code}} &mdash; {{$product->_view}}</th>
    </tr>
    {{foreach from=$curr_code item=curr_evt key=date}}
      <tr>
        <!-- <td style="width: 1%;">{{$date|date_format:"%m/%d/%Y %H:%M:%S"}}</td>-->
        <td style="width: 30%; text-align: center;">{{if $curr_evt.date_reception}}<img src="images/icons/tick.png" /> {{$curr_evt.date_reception}}{{/if}}</td>
        <td style="width: 30%; text-align: center;">{{if $curr_evt.date_delivery}}<img src="images/icons/tick.png" /> {{$curr_evt.date_delivery}}{{/if}}</td>
        <td style="width: 30%; text-align: center;">{{if $curr_evt.date_delivery_reception}}<img src="images/icons/tick.png" /> {{$curr_evt.date_delivery_reception}}{{/if}}</td>
      </tr>
    {{foreachelse}}
      <tr>
        <td colspan="10">Aucun évenement</td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="10">Aucun code correspondant</td>
    </tr>
  {{/foreach}}
</table>