{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var graphs = {{$graphs|@json}};

function drawGraphs() {
  $A(graphs).each(function(g, key){
    Flotr.draw($('graph-'+key), g.series, g.options);
  });
}

Main.add(function () {
  Calendar.regField(getForm("typevue").date, null, {noView: true});
  drawGraphs();
});
</script>

<table class="main">

<tr>
  <th>
    <form action="?" name="typevue" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      
  	  Logs d'accès du  {{$date|date_format:"%A %d %b %Y"}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      
      <label for="interval" title="Echelle d'affichage">Intervalle</label>
      <select name="interval" onchange="this.form.submit()">
        <option value="day" {{if $interval == "day"}} selected="selected" {{/if}}>Journée</option>
        <option value="month" {{if $interval == "month"}} selected="selected" {{/if}}>Mois</option>
        <option value="hyear" {{if $interval == "hyear"}} selected="selected" {{/if}}>Semestre</option>
      </select>
      &mdash;
      <label for="numelem" title="Nombre maximum d'éléments à afficher">Eléments maximums</label>
      <input type="text" name="numelem" value="{{$numelem}}" size="2" />
      <br />
      <label for="element" title="Choix de la mesure">Type de mesure</label>
      <select name="element" onchange="this.form.submit()">
        <option value="duration"{{if $element == "duration"}}selected="selected"{{/if}}>Durée totale (php + DB)</option>
        <option value="request"{{if $element == "request"}}selected="selected"{{/if}}>Durée DB</option>
      </select>
      &mdash;
      <label for="groupres" title="Type de vue des graphiques">Type de vue</label>
      <select name="groupres" onchange="this.form.submit()">
        <option value="0"{{if $groupres == 0}}selected="selected"{{/if}}>Regrouper par module</option>
        <option value="1"{{if $groupres == 1}}selected="selected"{{/if}}>Regrouper tout</option>
      </select>
    </form>
  </th>
</tr>

<tr>
  <td>
    {{if $groupres == 1}}
    <div id="graph-0" style="float: left; width: 450px; height: 450px;"></div>
    <div id="graph-1" style="float: left; width: 450px; height: 450px;"></div>
    {{else}}
      {{foreach from=$graphs item=graph name=graphs}}
        <div id="graph-{{$smarty.foreach.graphs.index}}" style="float: left; width: 450px; height: 450px;"></div>
      {{/foreach}}
    {{/if}}
  </td>
</tr>
</table>