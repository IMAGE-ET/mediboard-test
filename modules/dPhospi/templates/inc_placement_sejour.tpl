{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage hospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=affectation value=$sejour->_ref_first_affectation}}
{{if $affectation->_id}}
  <div>{{$affectation->_ref_lit}}</div>
{{else}}
  <div class="empty">Non placé</div>
{{/if}}
