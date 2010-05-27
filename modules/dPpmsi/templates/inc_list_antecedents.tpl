{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  <li>Du patient
    <ul>
      {{foreach from=$patient->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
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
      <li>{{tr}}CAntecedent.none{{/tr}}</li>
      {{/foreach}}
    </ul>
  </li>
  <li>Significatifs du séjour
    <ul>
      {{foreach from=$sejour->_ref_dossier_medical->_ref_antecedents key=curr_type item=list_antecedent}}
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
      <li>{{tr}}CAntecedent.none{{/tr}}</li>
      {{/foreach}}
    </ul>
  </li>
</ul>