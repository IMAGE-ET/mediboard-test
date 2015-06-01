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
    var tree = new TreeView("mapping");
    tree.collapseAll();
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
    <td class="halfPane" style="vertical-align: top;">
      <form method="get" name="requestSearch" action="?m=search" class="watched prepared" onsubmit="return Search.requestCluster(this);">
        {{if $infos_index|@count !==0}}
          <table class="tbl" id="table-mapping">
            <tr>
              <th class="category">Visualisation du mapping</th>
            </tr>
            <tr>
              <td class="text compact" id="mapping">
                <ul style="font-family: monospace;">
                  {{foreach from=$infos_index.mapping item=_type key=_title}}
                    <li>
                      <span>{{$_title}}</span>
                      <ul>
                        {{foreach from=$_type item=_data key=_title}}
                          <li style="background-color:#FAF6D9;">
                            <span class="text empty">{{$_data|@mbTrace}}</span>
                          </li>
                        {{/foreach}}
                      </ul>
                    </li>
                  {{/foreach}}
                </ul>
              </td>
            </tr>
            <tr>
              <th class="category">Requête Cluster ES</th>
            </tr>
            <tr>
              <td>
                <label><input type="radio" name="type_request" value="get" checked/> GET</label>
                <label><input type="radio" name="type_request" value="post"/> POST</label>
                <label><input type="radio" name="type_request" value="put"/> PUT</label>
              </td>
            </tr>
            <tr>
              <td>
                <label><textarea name="request" id="request"></textarea></label>
              </td>
            </tr>
            <tr>
              <td class="button">
                <button class="new" type="submit">Effectuer la requête</button>
              </td>
            </tr>
          </table>
        {{/if}}
      </form>
    </td>
  </tr>
</table>
