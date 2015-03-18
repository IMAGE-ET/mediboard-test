{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">Éléments utiles pour la réindexation </th>
  </tr>
  <tr>
    <th class="category" colspan="3">
      <span> Nom de l'index : {{$conf.search.index_name}}</span>
    </th>
  </tr>

  <tr>
    <th class="section">Settings</th>
    <td class="text compact">
      {{$settings}}
    </td>
  </tr>

  <tr>
    <th class="section">Types</th>
    <td class="text">
      {{foreach from=$types item=_type}}
        <span>{{$_type}}</span>
        <br/>
      {{/foreach}}
    </td>
  </tr>
  <tr>
    <th class="section">Mapping</th>
    <td class="text compact">
      {{$mapping}}
    </td>
  </tr>
</table>