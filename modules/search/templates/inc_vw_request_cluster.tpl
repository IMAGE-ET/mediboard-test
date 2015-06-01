{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl">
  <tr>
    <th class="title"> Résultat de la requête de type {{$type}}</th>
  </tr>
  <tr>
    <td class="text">{{$request}}</td>
  </tr>
  <tr>
    <td class="text">{{$content|@mbTrace}}</td>
  </tr>
</table>