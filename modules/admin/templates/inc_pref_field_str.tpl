{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8692 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<input class="str" type="text" size="40" name="pref[{{$var}}]" value="{{$pref.user}}" {{if $readonly}}readonly="readonly"{{/if}}/>
