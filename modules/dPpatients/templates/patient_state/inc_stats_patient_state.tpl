{{*
 * $Id$
 *  
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  drawLoadGraph = function() {
    var oDatum   = {{$graph.datum|@json}};
    var oOptions = {{$graph.options|@json}};

    var oPh = jQuery("#graph_occupation");
    oPh.bind('plothover', plotHover);
    var plot = jQuery.plot(oPh, oDatum, oOptions);

    var oDatum2   = {{$graph2.datum|@json}};
    var oOptions2 = {{$graph2.options|@json}};

    var oPh2 = jQuery("#graph_occupation2");
    oPh2.bind('plothover', plotHover);
    var plot2 = jQuery.plot(oPh2, oDatum2, oOptions2);
  };

  plotHover = function(event, pos, item) {
    if (item) {
      jQuery("#flot-tooltip").remove();
      var abscisse = parseInt(pos.x1)|0;

      content = item.series.label + "<br /><strong>" + item.series.data[abscisse][1] + " " + item.series.unit + "</strong>";

      $$("body")[0].insert(DOM.div({className: "tooltip", id: "flot-tooltip"}, content).setStyle({
        position: 'absolute',
        top:  pos.pageY + "px",
        left: parseInt(pos.pageX) + "px",
        opacity: 0.8,
        backgroundColor: '#000000',
        color: '#FFFFFF',
        borderRadius: '4px',
        textAlign: 'center',
        maxWidth: '300px',
        whiteSpace: 'normal'
      }));
    }
    else {
      jQuery("#flot-tooltip").remove();
    }
  };

  Main.add(function () {
    drawLoadGraph();
  });
</script>

<div class="small-info">
  Il y a <strong>{{$graph.count}}</strong> patients dont le statut de l'identité est renseigné sur les <strong>{{$total_patient}}</strong> patients de l'instance.
</div>

<table class="layout">
  <tr>
    <td>
      <p style="text-align: center">
        <strong>
          {{tr}}{{$graph.title}}{{/tr}} &bull; {{$graph.count}} {{$graph.unit}}
        </strong>
      </p>
      <div style="width: 500px; height: 500px;" id="graph_occupation"></div>
    </td>
    <td>
      <p style="text-align: center">
        <strong>
          {{tr}}{{$graph2.title}}{{/tr}}
        </strong>
      </p>
      <div style="width: 500px; height: 500px;" id="graph_occupation2"></div>
      <form name="filter_graph_bar_patient_state" method="post" onsubmit="return PatientState.stats_filter(this)">
        <table class="form">
          <tr>
            <th>{{mb_label class=CPatientState field=_date_end}}</th>
            <td>
              {{mb_field class=CpatientState field=_date_end register=true form=filter_graph_bar_patient_state value=$_date_end}}
            </td>
          </tr>
          <tr>
            <th>{{mb_label class=CPatientState field=_number_day}}</th>
            <td>{{mb_field class=CpatientState field=_number_day value=$_number_day}}</td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button class="search" type="submit">{{tr}}Filter{{/tr}}</button>
              <button class="download" type="button" onclick="PatientState.downloadCSV()">{{tr}}Download{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>