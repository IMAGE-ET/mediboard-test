{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $app->user_prefs.showCounterTip && $count && ($count > 1)}}
	<span class="countertip">
	  {{$count}}
	</span>
{{/if}}