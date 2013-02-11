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

<ul>
  {{foreach from=$antecedents key=name item=cat}}
    {{if ($type == "" || ($type == $name) ) && $cat|@count}}
      <li>
        <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
        <ul>
          {{foreach from=$cat item=ant}}
            <li>
              {{if $ant->date}}
                {{mb_value object=$ant field=date}}:
              {{/if}}
              {{$ant->rques}}
            </li>
          {{/foreach}}
        </ul>
      </li>
    {{/if}}
  {{/foreach}}
</ul>
