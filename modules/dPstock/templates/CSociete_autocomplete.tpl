{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span class="view">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>

{{if $match->postal_code && $match->city}}
<div style="color: #666; font-size: 0.7em; padding-left: 0.5em;">{{$match->postal_code}} - {{$match->city}}</div>
{{/if}}