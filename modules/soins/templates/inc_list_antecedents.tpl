{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<table class="tbl">
  {{foreach from=$antecedents key=name item=cat}}
    {{if $name != "alle" && $cat|@count}}
      <tr>
        <th>
          {{tr}}CAntecedent.type.{{$name}}{{/tr}}
        </th>
      </tr>
      {{foreach from=$cat item=ant}}
        <tr>
          <td {{if $ant->majeur}}style="color: #f00;"{{/if}}>
            {{if $dossier_medical->object_class == 'CSejour'}}
              <strong>
            {{/if}}
            {{if $ant->date}}
              {{mb_value object=$ant field=date}}:
            {{/if}}
            {{$ant->rques}}
            {{if $dossier_medical->object_class == 'CSejour'}}
              </strong>
            {{/if}}
          </td>
        </tr>
      {{/foreach}}
    {{/if}}
  {{/foreach}}
</table>