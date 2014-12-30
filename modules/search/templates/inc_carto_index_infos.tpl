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
    <td class="text">{{$infos_index.nbDocs_indexed|integer}}</td>
  </tr>
  {{foreach from=$infos_index.aggregation item=_object_indexed}}
    <tr>
      <td class="empty">Sous total de "{{tr}}{{$_object_indexed.key}}{{/tr}}" indexé </td>
      <td class="empty">{{$_object_indexed.doc_count|integer}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <th class="section" colspan="2"></th>
  </tr>
  <tr>
    <td class="text">Total des documents à indexer</td>
    <td class="text">{{$infos_index.nbdocs_to_index|integer}}</td>
  </tr>
  <tr>
    <td class="text">Le document le plus ancien en attente</td>
    <td class="text">{{$oldest_datetime|date_format:$conf.datetime}}</td>
  </tr>
  {{foreach from=$infos_index.nbdocs_to_index_by_type item=_object_to_index}}
    <tr>
      <td class="empty">Nombre de "{{tr}}{{$_object_to_index.object_class}}{{/tr}}" à indexer</td>
      <td class="empty">{{$_object_to_index.total|integer}}</td>
    </tr>
  {{/foreach}}

  <!-- Statistiques Index  -->
  <tr>
    <th class="section" colspan="2"> Statistiques</th>
  </tr>
  <tr>
    <td class="text">Nombre de recherches effectuées au total</td>
    <td class="text">{{$infos_index.stats.search.total|integer}}</td>
  </tr>
  <tr>
    <td class="text">Temps moyen d'une recherche</td>
    <td class="text">{{$infos_index.stats.search.average_time}}</td>
  </tr>
</table>