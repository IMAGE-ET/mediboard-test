{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Patient -->
		  <td>
		    <a href="#" onclick="printAdmission({{$sejour->_id}})">
		      {{$patient->_view}}
		    </a>
		  </td>
		  <td>
		    <a href="#" onclick="printAdmission({{$sejour->_id}})">
		      {{$patient->_age}} ans
		    </a>
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