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
    <th class="title" colspan="2">{{tr}}Result{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}Location{{/tr}}</th>
    <th>{{tr}}Error{{/tr}}</th>
  </tr>
  {{foreach from=$treecda->validateSchematron item=_error}}
    <tr>
      <td>
        {{$_error.location}}
      </td>
      <td>
        {{$_error.error}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td>
        {{tr}}Document valide{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>