{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

  function doReaffectation(mode_real) {
    var url = new Url;
    url.setModuleAction("dPbloc", "httpreq_reaffect_plagesop");
    url.addParam("mode_real", mode_real);
    url.requestUpdate("resultReaffectation");
  }

</script>

<table class="tbl">
  <tr>
    <td class="button narrow">
      <button class="modify" onclick="doReaffectation(1)">Réatribuer</button>
      <br />
      <button class="modify" onclick="doReaffectation(0)">Tester</button>
    </td>
    <td>
      <div id="resultReaffectation"></div>
    </td>
  </tr>
</table>