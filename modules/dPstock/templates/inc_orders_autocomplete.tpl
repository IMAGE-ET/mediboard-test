{{* $Id: inc_orders_list.tpl 7645 2009-12-17 16:40:57Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision: 7645 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
{{foreach from=$orders item=curr_order}}
  <li>
    <span style="display: none;" class="value">{{$curr_order->_id}}-{{$curr_order->order_number}}</span>
    
    <small style="float: right">{{$curr_order->date_ordered|date_format:$conf.date}}</small>
    <strong class="view">{{$curr_order->order_number}}</strong>
    
    {{if $curr_order->societe_id}}
     - {{$curr_order->_ref_societe->_view}}
    {{/if}}
    
    <br />
    <small class="opacity-60">
      {{$curr_order->_count.order_items}} articles - {{$curr_order->_count_received}} reçus
    </small>
  </li>
{{/foreach}}
</ul>
