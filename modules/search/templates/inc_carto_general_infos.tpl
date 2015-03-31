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
    <th class="category" colspan="2"> État Général</th>
  </tr>
  <tr>
    <td class="text">Test connexion</td>
    <td class="text">
      {{if $infos_index.connexion === "1"}}
        <img title="CONNECTÉ" src="images/icons/note_green.png">
      {{else}}
        <img title="NON CONNECTÉ" src="images/icons/note_red.png">
      {{/if}}
    </td>
  </tr>

  <!-- Cluster et Shards -->
  <tr>
    <td class="text"><label>Statut du cluster</label></td>
    <td class="text">
      {{if $infos_index.status !== ""}}
        <img title="{{$infos_index.status}}" src="images/icons/note_{{$infos_index.status}}.png">
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="text">Nombre de shards actifs sur le total</td>
    <td class="text">{{$infos_index.stats.shards.successful}} / {{$infos_index.stats.shards.total}}</td>
  </tr>
  <tr>
    <td class="text">Nombre de shards inactifs sur le total</td>
    <td class="text">{{$infos_index.stats.shards.failed}} / {{$infos_index.stats.shards.total}}</td>
  </tr>

  <tr>
    <th class="section" colspan="2"> SERVEUR D'EXTRACTION DE FICHIERS</th>
  </tr>
  <tr>
    <td class="text">Test connexion</td>
    <td class="text">
      {{if $infos_tika}}
        <img title="CONNECTÉ" src="images/icons/note_green.png">
      {{else}}
        <img title="NON CONNECTÉ" src="images/icons/note_red.png">
      {{/if}}
    </td>
  </tr>

  <tr>
    <th class="section" colspan="2"> STATISTIQUES</th>
  </tr>
  <tr>
    <td class="text">Nombre d'index du cluster</td>
    <td>{{$infos_index.stats.cluster.nbIndex|integer}}</td>
  </tr>
  <tr>
    <td class="text">Nombre Total de documents du cluster</td>
    <td>{{$infos_index.stats.cluster.nbDocsTotal|integer}}</td>
  </tr>

  <!-- Taille totale des index -->
  <tr>
    <td class="text">Taille des indexes en base NoSQL</td>
    <td class="text">{{$infos_index.size}}</td>
  </tr>
</table>