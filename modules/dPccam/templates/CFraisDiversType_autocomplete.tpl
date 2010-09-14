{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<strong>{{$match->code}} ({{$match->tarif|floatval}})</strong>
<small>{{$match->libelle}}</small>

<span class="view" style="display: none">{{$match->_view}}</span>
<span class="tarif" style="display: none">{{$match->tarif}}</span>
<span class="facturable" style="display: none">{{$match->facturable}}</span>
