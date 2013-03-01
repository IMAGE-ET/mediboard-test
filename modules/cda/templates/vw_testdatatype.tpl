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
      Description
    </th>
    <th>
      Résultat attendu
    </th>
    <th>
      Résultat
    </th>
  </tr>
  {{foreach from=$result item=_class}}
    {{foreach from=$_class key=name item=_test}}
      <tr>
        <th colspan="3" class="title">
          {{$name}}
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
  {{/foreach}}
</table>
<br/>