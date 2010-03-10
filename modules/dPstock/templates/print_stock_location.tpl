{{* $Id: vw_idx_stock_location.tpl 8024 2010-02-08 09:20:55Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8024 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(print);
</script>

<h2>{{$stock_location->name}}</h2>

<table class="main tbl">
  {{foreach from=$stock_location->_back.group_stocks item=_stock}}
    <tr>
      <td>{{$_stock}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductStockGroup.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>