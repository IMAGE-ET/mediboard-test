{{*
 * View exchange data format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="layout">
  <tr>
    <td>
      <div id="graph" style="width: 600px; height: 400px; margin-right: 0; margin-left: auto;"></div>
    </td>
    <td id="legend" style="width: 25%;"></td>
  </tr>
</table>

<script type="text/javascript">
  var options = {{$options|@json}};
  options.legend.container = $('legend');
  Flotr.draw($('graph'), {{$series|@json}}, options);
</script>