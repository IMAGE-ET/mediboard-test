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
  {{foreach from=$list_products item=_product}}
    <tr>
      <td>
        {{$_product}}
      </td>
      <td>
        {{$_product->code}}
      </td>
    </tr>
  {{/foreach}}
</table>