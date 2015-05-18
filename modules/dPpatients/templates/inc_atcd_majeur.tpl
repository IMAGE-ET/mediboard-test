{{*
 * $Id$
 *  
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$dossier_medical->_all_antecedents item=_antecedent}}
  {{if $_antecedent->majeur}}
    <span class="circled" style="border-color: #f00; color: #f00; font-size: 0.6em; background-color: #fff; font-weight: normal; text-shadow: none;">
      {{$_antecedent->rques|spancate:20:"...":false}}
    </span>
  {{/if}}
{{/foreach}}