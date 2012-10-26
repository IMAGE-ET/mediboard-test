{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=show_label value=1}}

{{if $operation->rques || ($consult_anesth && $consult_anesth->_intub_difficile)}}
  <div class="small-warning">
    {{if $operation->rques}}
      {{if $show_label}}
        <em>{{mb_label object=$operation field=rques}}</em> :
      {{/if}}
      {{mb_value object=$operation field=rques}}
    {{/if}}
    {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
      <div style="font-weight: bold; color:#f00;">
        {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
      </div>
    {{/if}}
  </div>
{{/if}}