{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search"}}
<script>
  Main.add(function () {
    Search.updateListStats();
  });
</script>
<table class="main" id="cartographie_systeme">
  <tbody>
  <tr>
    <th class="title" colspan="2"> Statut de l'index {{$name_index}}</th>
  </tr>
  <tr>
    <td class="halfPane">
      <table class="tbl" id="table-stats">
        <tr>
          <th class="category"> État de l'index</th>
          <th class="narrow"></th>
        </tr>
        <tr>
          <td class="text">
            <label>Test connexion</label>
          </td>
          <td class="text">
            {{if $connexion === "1"}}
              <img title="CONNECTÉ" src="images/icons/note_green.png">
            {{else}}
              <img title="NON CONNECTÉ" src="images/icons/note_red.png">
            {{/if}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <label>Nombre de documents indexés</label>
          </td>
          <td class="text">
            {{$nbDocs_indexed}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <label>Nombre de documents à indexer</label>
          </td>
          <td class="text">
            {{$nbdocs_to_index}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <label>Statut du cluster</label>
          </td>
          <td class="text">
            {{if $status !== ""}}
              <img title="{{$status}}" src="images/icons/note_{{$status}}.png">
            {{/if}}
          </td>
        </tr>
        <tr>
          <td class="text">
            <label>Taille des indexes en base NoSQL</label>
          </td>
          <td class="text">
            {{$size}}
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table class="tbl" id="table-mapping">
        <tr>
          <th class="category"> Visualisation du mapping</th>
        </tr>
        <tr>
          <td class="text compact" style="background-color:#FAF6D9 " id="mapping">
            {{$mapping|@mbTrace}}
          </td>
        </tr>
        <tr>
          <th class="category"> Modification du mapping</th>
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
    </td>
  </tr>
  </tbody>
</table>
