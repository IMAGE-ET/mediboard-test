{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage pmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="halfPane category">{{tr}}CPatient{{/tr}}</th>
    <th class="category">{{tr}}CSejour{{/tr}}</th>
  </tr>
  <tr>
    <td class="text">
      <ul>
        {{foreach from=$sejour->_ref_patient->_ref_dossier_medical->_ref_antecedents_by_type key=curr_type item=list_antecedent}}
          {{if $list_antecedent|@count}}
            <li>
              {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
              {{foreach from=$list_antecedent item=curr_antecedent}}
                <ul>
                  <li>
                    {{if $curr_antecedent->date}}
                      {{mb_value object=$curr_antecedent field=date}} -
                    {{/if}}
                    <em>{{$curr_antecedent->rques}}</em>
                  </li>
                </ul>
              {{/foreach}}
            </li>
          {{/if}}
          {{foreachelse}}
          <li class="empty">{{tr}}CAntecedent.none{{/tr}}</li>
        {{/foreach}}
      </ul>
    </td>
    <td class="text">
      <ul>
        {{foreach from=$sejour->_ref_dossier_medical->_ref_antecedents_by_type key=curr_type item=list_antecedent}}
          {{if $list_antecedent|@count}}
            <li>
              {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
              {{foreach from=$list_antecedent item=curr_antecedent}}
                <ul>
                  <li>
                    {{if $curr_antecedent->date}}
                      {{mb_value object=$curr_antecedent field=date}} -
                    {{/if}}
                    <em>{{$curr_antecedent->rques}}</em>
                  </li>
                </ul>
              {{/foreach}}
            </li>
          {{/if}}
          {{foreachelse}}
          <li class="empty">{{tr}}CAntecedent.none{{/tr}}</li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
</table>