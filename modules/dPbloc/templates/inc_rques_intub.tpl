{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{assign var=sejour value=$operation->_ref_sejour}}

{{if $operation->rques || ($sejour && $sejour->_id && $sejour->ATNC != "") || ($consult_anesth && $consult_anesth->_intub_difficile)}}
  <div class="small-warning">
    {{mb_value object=$operation field=rques}}
    {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
      <div style="font-weight: bold; color:#f00;">
        {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
      </div>
    {{/if}}

    {{if $sejour && $sejour->_id && $sejour->ATNC != ""}}
      <div style="font-weight: bold; {{if $sejour->ATNC}}color: #f00;{{/if}}">
        Risque ATNC: {{tr}}{{if $sejour->ATNC}}Yes{{else}}No{{/if}}{{/tr}}
      </div>
    {{/if}}
  </div>
{{/if}}