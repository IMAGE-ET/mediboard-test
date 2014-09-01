{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  var AccessLog = {
    crazyLogs: function () {
      Control.Modal.close();
      var url = new Url('system', 'crazy_logs');

      url.requestModal(800, 300);
    },

    purgeCrazyAccessLogs : function() {
      var url = new Url('system', 'crazy_access_logs');
      url.addParam("mode", "purge");

      url.requestUpdate("crazy_al");
    },

    purgeCrazyDataSourceLogs : function() {
      var url = new Url('system', 'crazy_datasource_logs');
      url.addParam("mode", "purge");

      url.requestUpdate("crazy_dsl");
    },

    viewAggregateLogs : function () {
      var url = new Url('system', 'view_aggregate_access_logs');
      url.requestModal(400);
    },

    viewAggregationBoard : function () {
      var url = new Url('system', 'ajax_vw_aggregation_board');
      url.requestModal();
    },

    aggregate : function (dry_run) {
      var url = new Url('system', 'aggregate_access_logs');

      if (dry_run) {
        url.addParam('dry_run', 1);
        url.requestUpdate("dry_run");
      }
      else {
        Modal.confirm("Agréger ?", {onValidate: function (v) {
          if (v) {
            url.requestUpdate("aggregate")
          }
        } });
      }
    }
  };

  Main.add(function () {
    Control.Tabs.create('logs_tab', false, {
      afterChange: function (container) {
        switch (container.id) {
          case "access_logs_tab":
            form = getForm("typevue");
            if ($V(form.elements.to_update) === "1") {
              form.onsubmit();
              $V(form.elements.to_update, "0");
            }
            break;

          case "datasource_logs_tab":
            form = getForm("datasource-typevue");
            if ($V(form.elements.to_update) === "1") {
              form.onsubmit();
              $V(form.elements.to_update, "0");
            }
            break;
        }
      }
    });
    Calendar.regField(getForm("typevue").date, null, {noView: true});
  });
</script>

<fieldset>
  <button class="stats" type="button" onclick="AccessLog.viewAggregationBoard()">{{tr}}Aggregation-board{{/tr}}</button>
  <button class="search" type="button" onclick="AccessLog.viewAggregateLogs()">{{tr}}Aggregate{{/tr}}</button>
  <button class="search" type="button" onclick="AccessLog.crazyLogs()">Journaux douteux</button>
</fieldset>

<ul class="control_tabs" id="logs_tab">
  <li>
    <a href="#access_logs_tab">{{tr}}CAccessLog{{/tr}}</a>
  </li>

  <li>
    <a href="#datasource_logs_tab">{{tr}}CDataSourceLog{{/tr}}</a>
  </li>
</ul>

<div id="access_logs_tab" style="display: none;">
  <table class="main">
    <tr>
      <th>
        <form action="" name="typevue" method="get" onsubmit="return onSubmitFormAjax(this, null, 'access_logs_graphs');">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="a" value="vw_graph_access_logs" />
          <input type="hidden" name="to_update" value="1" />

          Journaux d'accès du {{$date|date_format:$conf.longdate}}
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.onsubmit()" />

          <br />
          <label for="interval" title="Echelle d'affichage">Intervalle</label>
          <select name="interval" onchange="this.form.onsubmit();">
            <option value="one-day"      {{if $interval == "one-day"     }} selected="selected" {{/if}}>1 jour (par 10mn)</option>
            <option value="one-week"     {{if $interval == "one-week"    }} selected="selected" {{/if}}>1 semaine (par heure)</option>
            <option value="height-weeks" {{if $interval == "height-weeks"}} selected="selected" {{/if}}>8 semaines (par jour)</option>
            <option value="one-year"     {{if $interval == "one-year"    }} selected="selected" {{/if}}>1 an (par semaine)</option>
            <option value="four-years"   {{if $interval == "four-years"  }} selected="selected" {{/if}}>4 ans (par mois)</option>
            <option value="twenty-years" {{if $interval == "twenty-years"}} selected="selected" {{/if}}>20 ans (par an)</option>
          </select>

          {{if $interval == "one-day"}}
            <label for="hour_min" title="Heure minimale">{{tr}}From{{/tr}}</label>
            <select name="hour_min" onchange="this.form.onsubmit()">
              {{foreach from=$hours item=_hour}}
                <option value="{{$_hour}}" {{if $hour_min == $_hour}} selected="selected" {{/if}}>
                  {{$_hour|pad:2:0}}h
                </option>
              {{/foreach}}
            </select>
            <label for="hour_max" title="Heure maximale">{{tr}}To{{/tr}}</label>
            <select name="hour_max" onchange="this.form.onsubmit()">
              {{foreach from=$hours item=_hour}}
                <option value="{{$_hour}}" {{if $hour_max == $_hour}} selected="selected" {{/if}}>
                  {{$_hour|pad:2:0}}h
                </option>
              {{/foreach}}
            </select>
          {{/if}}

          <label for="bigsize" title="Afficher en plus grande taille">Grande taille</label>
          <input type="checkbox" name="bigsize" onclick="drawGraphs(graphSizes[this.checked ? 1 : 0])"
                 {{if $groupmod == 2}}checked="checked"{{/if}} />
          <br />

          <label for="groupmod" title="Type de vue des graphiques">Type de vue</label>
          <select name="groupmod" onchange="this.form.onsubmit()">
            <option value="1" {{if $groupmod == 1}}selected="selected"{{/if}}>Regrouper par module</option>
            <option value="2" {{if $groupmod == 2}}selected="selected"{{/if}}>Regrouper tout</option>
            <optgroup label="Détail du module">
              {{foreach from=$listModules item=curr_module}}
                <option value="{{$curr_module->mod_name}}" {{if $curr_module->mod_name == $module}} selected="selected" {{/if}}>
                  {{tr}}module-{{$curr_module->mod_name}}-court{{/tr}}
                </option>
              {{/foreach}}
            </optgroup>
          </select>

          <label for="human_bot" title="Filtrage en fonction du type d'utilisateur">Visualiser</label>
          <select name="human_bot" onchange="this.form.onsubmit()">
            <option value="0" {{if $human_bot === '0'}}selected="selected"{{/if}}>Humains</option>
            <option value="1" {{if $human_bot === '1'}}selected="selected"{{/if}}>Robots</option>
            <option value="2" {{if $human_bot === '2'}}selected="selected"{{/if}}>Les deux</option>
          </select>
          <br />

          <div>
            Gauche :
            <select name="left_mode" onchange="this.form.onsubmit()">
              <option value="request_time" {{if $left_mode == 'request_time'}}selected="selected"{{/if}}>Temps de réponse</option>
              <option value="cpu_time"     {{if $left_mode == 'cpu_time'}}    selected="selected"{{/if}}>Temps CPU</option>
              <option value="errors"       {{if $left_mode == 'errors'}}      selected="selected"{{/if}}>Erreurs</option>
              <option value="memory_peak"  {{if $left_mode == 'memory_peak'}} selected="selected"{{/if}}>Pics de mémoire</option>
              <option value="nb_requests"  {{if $left_mode == 'nb_requests'}} selected="selected"{{/if}}>Requêtes SQL</option>
            </select>
            <select name="left_sampling" onchange="this.form.onsubmit()">
              <option value="total" {{if $left_sampling == 'total'}}selected="selected"{{/if}}>Total</option>
              <option value="mean"  {{if $left_sampling == 'mean'}} selected="selected"{{/if}}>par hit</option>
            </select>

            Droite :
            <select name="right_mode" onchange="this.form.onsubmit()">
              <option value="hits" {{if $right_mode == 'hits'}}selected="selected"{{/if}}>Hits</option>
              <option value="size" {{if $right_mode == 'size'}}selected="selected"{{/if}}>Bande passante</option>
            </select>
            <select name="right_sampling" onchange="this.form.onsubmit()">
              <option value="total" {{if $right_sampling == 'total'}}selected="selected"{{/if}}>Total</option>
              <option value="mean"  {{if $right_sampling == 'mean'}} selected="selected"{{/if}}>par unité de temps</option>
            </select>
          </div>

          <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
        </form>
      </th>
    </tr>

    <tr>
      <td colspan="2">
        <div id="access_logs_graphs"></div>
      </td>
    </tr>
  </table>
</div>

<div id="datasource_logs_tab" style="display: none;">
  {{mb_include module=system template=vw_datasource_logs}}
</div>