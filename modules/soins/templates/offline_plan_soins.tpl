{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$sejours item=sejour name=sejour}}
  {{mb_include module=soins template=inc_offline_plan_soins}}

  {{if !$smarty.foreach.sejour.last}}
    <hr style="border: 0; page-break-after: always" />
  {{/if}}
{{/foreach}}