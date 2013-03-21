{{*
  * Tooltip des antécédents du patient
  *  
  * @category dPpatients
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}
<table class="tbl">
  <tr>
    <th class="title">
      Antécédents
    </th>
  </tr>
  {{foreach from=$antecedents key=name item=cat}}
    {{if $name != "alle" && $cat|@count}}
      <tr>
        <th>
          {{tr}}CAntecedent.type.{{$name}}{{/tr}}
        </th>
      </tr>
      {{foreach from=$cat item=ant}}
        <tr>
          <td>
            {{if $ant->date}}
              {{mb_value object=$ant field=date}}:
            {{/if}}
            {{$ant->rques}}
          </td>
        </tr>
      {{/foreach}}
    {{/if}}
  {{/foreach}}
</table>