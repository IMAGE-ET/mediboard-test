{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search"}}
<table class="main" id="table-mapping">
  <tr>
    <th class="title"> Visualisation du mapping</th>
  </tr>
  <tr>
    <td class="text compact" style="background-color:#FAF6D9 " id="mapping">
      {{$mapping|@mbTrace}}
    </td>
  </tr>
  <tr>
    <th class="title"> Modification du mapping</th>
  </tr>
  <tr>
    <td>
      <textarea name="mappingjson" id="mappingjson">
        {{$mappingjson}}
      </textarea>
    </td>
  </tr>
  <tr>
    <td>
      <button class="new" onclick="Search.showdiff('{{$mappingjson}}', $V($('mappingjson')))">Prévisualiser</button>
    </td>
  </tr>
</table>
