{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
  function reloadDiagnostic(sejour_id) {
	  var url = new Url("dPurgences", "ajax_diagnostic_principal");
	  url.addParam("sejour_id", sejour_id);
	  url.requestUpdate("dp_"+sejour_id);

	  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
	  url.addParam("sejour_id", sejour_id);
	  url.addParam("modeDAS", 0);
	  url.requestUpdate("cim");
  }
</script>

<th>{{mb_label object=$sejour field="DP"}}</th>
<td>
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}" />
  <script type="text/javascript">
      CIM10Selector.initDP = function() {
        this.sForm     = "editSejour";
        this.sView     = "DP";
        this.sChir     = "praticien_id";
        this.pop();
      }
    </script>
    <input type="text" name="DP" value="{{$sejour->DP}}" class="code cim10" size="10" onchange="submitSejour(); reloadDiagnostic('{{$sejour->_id}}'); " />
    <button type="button" class="search notext" onclick="CIM10Selector.initDP()">
      {{tr}}button-CCodeCIM10-choix{{/tr}}
    </button>
</td>