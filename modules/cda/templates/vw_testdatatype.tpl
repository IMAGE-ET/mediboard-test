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
    <th colspan="2" class="title">
      {{$nameClass}}
    </th>
  </tr>
  <tr>
    <td colspan="2">{{$description}}</td>
  </tr>
  <tr>
    <th>Résultat</th>
    <th>Résultat attendu</th>
  </tr>
  <tr>
   <td {{if $result == $resultAttendu}}class="ok"{{else}} class="error"{{/if}}>
     {{$result}}
    </td>
    <td>
      {{$resultAttendu}}
    </td>
  </tr>
</table>
<br/>