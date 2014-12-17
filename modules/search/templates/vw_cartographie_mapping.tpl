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
<table class="main tbl" id="cartographie_systeme">
  <tr>
    {{if $infos_index|@count !==0}}
      <th class="title" colspan="2"> Statut de l'index {{$infos_index.name_index}}</th>
    {{/if}}
  </tr>
  <tr>
    <td class="halfPane" id="table-stats">
      {{if $infos_index|@count !==0}}
        <!-- Etat général du service ES -->
        {{mb_include module=search template=inc_carto_general_infos}}
        <!-- Etat de l'index -->
        {{mb_include module=search template=inc_carto_index_infos}}
      {{/if}}
      {{if $infos_log|@count !== 0}}
        <!-- Etat de l'index des journaux utilisateurs -->
        {{mb_include module=search template=inc_carto_log_infos}}
      {{/if}}
    </td>
    <td class="halfPane">
      {{if $infos_index|@count !==0}}
        <table class="tbl" id="table-mapping">
          <tr>
            <th class="category"> Visualisation du mapping</th>
          </tr>
          <tr>
            <td class="text compact" style="background-color:#FAF6D9 " id="mapping">
              {{$infos_index.mapping|@mbTrace}}
            </td>
          </tr>
          <tr>
            <th class="category"> Modification du mapping</th>
          </tr>
          <tr>
            <td>
             <label><textarea name="mappingjson" id="mappingjson">
                {{$infos_index.mappingjson}}
              </textarea></label>
            </td>
          </tr>
          <tr>
            <td>
              <button class="new" onclick="Search.showdiff('{{$infos_index.mappingjson}}', $V($('mappingjson')))">Prévisualiser</button>
            </td>
          </tr>
        </table>
      {{/if}}
    </td>
  </tr>
</table>
