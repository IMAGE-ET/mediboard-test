{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="tbl">
  <!-- Etat de l'index -->
  <tr>
    <th class="category" colspan="2"> État de l'index {{$conf.db.std.dbname}}</th>
  </tr>
  <tr>
    <td class="text">Nombre de documents indexés</td>
    <td class="text">{{$infos_index.nbDocs_indexed}}</td>
  </tr>
  <tr>
    <td class="text">Nombre de documents à indexer au total</td>
    <td class="text">{{$infos_index.nbdocs_to_index}}</td>
  </tr>
  {{foreach from=$infos_index.nbdocs_to_index_by_type item=_object_to_index}}
    <tr>
      <td class="empty">Nombre de documents à indexer de type {{$_object_to_index.object_class}}</td>
      <td class="empty">{{$_object_to_index.total}}</td>
    </tr>
  {{/foreach}}

  <!-- Statistiques Index  -->
  <tr>
    <th class="section" colspan="2"> Statistiques</th>
  </tr>
  <tr>
    <td class="text">Nombre de recherches effectuées au total</td>
    <td class="text">{{$infos_index.stats.search.total}}</td>
  </tr>
  <tr>
    <td class="text">Temps moyen d'une recherche</td>
    <td class="text">{{$infos_index.stats.search.average_time}}</td>
  </tr>
</table>