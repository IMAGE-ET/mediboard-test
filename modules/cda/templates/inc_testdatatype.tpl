{{*
 * test
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th>
      {{tr}}Description{{/tr}}
    </th>
    <th>
      {{tr}}ResultPlanned{{/tr}}
    </th>
    <th>
      {{tr}}Result{{/tr}}
    </th>
  </tr>
  {{foreach from=$result key=name item=_test}}
    <tr>
      <th colspan="3" class="title">
        <A NAME="{{$name}}">{{$name}}</A>
      </th>
    </tr>
    {{foreach from=$_test item=_ligne}}
      <tr>
        <td>{{$_ligne.description}}</td>
        <td>{{$_ligne.resultatAttendu}}</td>
        <td {{if $_ligne.resultatAttendu == $_ligne.resultat}}class="ok"{{else}}class="error"{{/if}}>{{$_ligne.resultat}}</td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>
<br/>