{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span class="view" style="float: left;">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>

<div style="float: right; font-size: 8px; color: #999; text-align: center;">
  <span style="width:9px;height:11px;display:inline-block;border:1px solid #ccc;{{if $match->_is_manufacturer}}background-color:#73BF2F;color:#000;{{else}}background-color:#eee;{{/if}}" title="Fabricant">F</span>
  <span style="width:9px;height:11px;display:inline-block;border:1px solid #ccc;{{if $match->_is_supplier}}background-color:#73BF2F;color:#000;{{else}}background-color:#eee;{{/if}}" title="Distributeur">D</span>
</div>

<div style="color: #999; font-size: 0.7em; padding-left: 0.5em; clear: both;">
  {{if $match->postal_code && $match->city}}{{$match->postal_code}} - {{$match->city}}{{/if}}&nbsp;
</div>