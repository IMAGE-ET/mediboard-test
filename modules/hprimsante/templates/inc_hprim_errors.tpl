{{*
 * $Id$
 *
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=level value=""}}

<table class="main tbl">
  <tr>
    <th>Ligne</th>
    <th>Entité</th>
    <th></th>
    <th></th>
  </tr>
  {{foreach from=$errors item=_error}}
    {{if $level && ($level == $_error->level)}}
      <tr>
        <td class="narrow">
          {{$_error->line}}
        </td>
        <td class="narrow">
          {{if $_error->entity}}
            <pre style="border: none;">{{$_error->entity->getPathString()}}</pre>
          {{/if}}
        </td>
        <td>
          {{if $_error->code|is_numeric}}
            {{tr}}CHL7v2Exception-{{$_error->code}}{{/tr}}
          {{else}}
            {{$_error->code}}
          {{/if}}
        </td>
        <td>
          {{$_error->data}}
        </td>
      </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">Aucune erreur</td>
    </tr>
  {{/foreach}}
</table>