{{* $Id: view_access_logs.tpl 12965 2011-08-25 14:31:18Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 12965 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function yAxisTickFormatter(val) {
  return Flotr.engineeringNotation(val, 2, 1000);
}

function drawGraphs() {
  var size = {
    width: '700px', 
    height: '500px'
  };

  var container = $('graph');
  container.setStyle(size);

  var graph = {{$graph|@json}};
  graph.options.yaxis.noTicks = 10;
  graph.options.yaxis.tickFormatter = yAxisTickFormatter;
  Flotr.draw(container, graph.series, graph.options);
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
      <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
      
      Journaux utilisateurs du {{$date|date_format:$conf.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      
      <label for="user_id" title="Filtre possible sur un utilisateur">Utilisateur</label>
      <select name="user_id" onchange="this.form.submit();">
        <option value="">&mdash; {{tr}}All{{/tr}}</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$user_id}}
      </select>
      
      <label for="interval" title="Echelle d'affichage">Intervalle</label>
      <select name="interval" onchange="this.form.submit();">
        <option value="one-day"      {{if $interval == "one-day"     }} selected="selected" {{/if}}>1 jour     (par 10mn)    </option>
        <option value="one-week"     {{if $interval == "one-week"    }} selected="selected" {{/if}}>1 semaine  (par heure)   </option>
        <option value="height-weeks" {{if $interval == "height-weeks"}} selected="selected" {{/if}}>8 semaines (par jour)    </option>
        <option value="one-year"     {{if $interval == "one-year"    }} selected="selected" {{/if}}>1 an       (par semaine) </option>
        <option value="four-years"   {{if $interval == "four-years"  }} selected="selected" {{/if}}>4 ans      (par mois)    </option>
      </select>
      
    </form>
  </th>
</tr>

<tr>
  <td>
    <div id="graph"></div>
  </td>
</tr>

</table>

