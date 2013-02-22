{{*
 * $Id$
 *
 * Vue de validation du CDA
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="tbl">
  <tr>
    <th>{{tr}}Result{{/tr}}</th>
  </tr>
  <tr>
    <td>
      {{if $treecda->validate != 1}}
        {{$treecda->validate}}
      {{else}}
        {{tr}}Document valide{{/tr}}
      {{/if}}
    </td>
  </tr>
</table>