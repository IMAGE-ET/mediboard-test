{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Patient -->
		  <td class="text">
		    <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
		      {{$patient->_view}}
		    </span>
		  </td>
		  <td>
		    {{$patient->_age}}
		  </td>
		  {{if $_coordonnees}}
		  <td>
		    {{if $patient->tel}}
		    {{mb_value object=$patient field="tel"}}
		    <br />
		    {{/if}}
		    {{if $patient->tel2}}
		    {{mb_value object=$patient field="tel2"}}
		    {{/if}}
		  </td>
		  {{/if}}