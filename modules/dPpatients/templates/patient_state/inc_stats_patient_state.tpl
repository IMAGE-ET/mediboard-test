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
  };

  plotHover = function(event, pos, item) {
    if (item) {
      jQuery("#flot-tooltip").remove();

      content = item.series.label + "<br /><strong>" + item.series.data[0][1] + " " + item.series.pie.unit + "</strong>";

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
  Il y a <strong>{{$total_patient_state}}</strong> patients dont le statut de l'identité est renseigné sur les <strong>{{$total_patient}}</strong> patients de l'instance.
</div>

<table class="layout">
  <tr>
    <td>
      <p style="text-align: center">
        <strong>
          {{tr}}{{$graph.title}}{{/tr}} &bull; {{$graph.count}} {{$graph.unit}}
        </strong>
      </p>
      <div style="width: 600px; height: 600px;" id="graph_occupation"></div>
    </td>
  </tr>
</table>