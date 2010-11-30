{{* $Id: vw_idx_delivrance.tpl 9733 2010-08-04 14:03:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 9733 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
  
<div class="small-info">En cours de développement</div>

<table class="main tbl">
  <tr>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-quantity{{/tr}}</th>
  </tr>
    
  {{foreach from=$list_products item=_product}}
    <tr>
      <td>{{$_product}}</td>
      <td>{{$_product->code}}</td>
      <td>{{mb_value object=$_product->_ref_stock_group field=quantity}}</td>
    </tr>
  {{/foreach}}
</table>