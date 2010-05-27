{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td class="text" colspan="2">
    <ul>
      <li>Du patient
        <ul>
          {{foreach from=$patient->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
          <li>
            {{if $curr_trmt->fin}}
              Depuis {{mb_value object=$curr_trmt field=debut}} 
              jusqu'à {{mb_value object=$curr_trmt field=fin}} :
            {{elseif $curr_trmt->debut}}
              Depuis {{mb_value object=$curr_trmt field=debut}} :
            {{/if}}
            <em>{{$curr_trmt->traitement}}</em>
          </li>
          {{foreachelse}}
          <li>{{tr}}CTraitement.none{{/tr}}</li>
          {{/foreach}}
        </ul>
      </li>
      <li>Significatifs du séjour
        <ul>
          {{foreach from=$sejour->_ref_dossier_medical->_ref_traitements item=curr_trmt}}
          <li>
            {{if $curr_trmt->fin}}
              Depuis {{mb_value object=$curr_trmt field=debut}} 
              jusqu'à {{mb_value object=$curr_trmt field=fin}} :
            {{elseif $curr_trmt->debut}}
              Depuis {{mb_value object=$curr_trmt field=debut}} :
            {{/if}}
            <em>{{$curr_trmt->traitement}}</em>
          </li>
          {{foreachelse}}
          <li>{{tr}}CTraitement.none{{/tr}}</li>
          {{/foreach}}
        </ul>
      </li>
    </ul>
  </td>
</tr>