{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage hospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var="which" value="first"}}

{{if ($which == "first")}}{{assign var=affectation value=$sejour->_ref_first_affectation}}{{/if}}
{{if ($which == "curr" )}}{{assign var=affectation value=$sejour->_ref_curr_affectation }}{{/if}}

{{if isset($affectation|smarty:nodefaults) && $affectation->_id}}
  <div>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$affectation->_guid}}')">{{$affectation->_ref_lit}}</span>
  </div>
{{else}}
  <div class="empty">Non placé</div>
{{/if}}
