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
  {{foreach from=$treecda->validate item=_error}}
    {{if $_error !== "1"}}
    <tr>
      <td>
        {{$_error}}
      </td>
    </tr>
    {{/if}}
   {{foreachelse}}
    <tr>
      <td>
        {{tr}}Document valide{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>