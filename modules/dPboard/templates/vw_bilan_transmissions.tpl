{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

viewTransmissions = function(sejour_id, praticien_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("sejour_id", sejour_id);
  url.addParam("praticien_id", document.selPraticien.praticien_id.value);
  url.requestUpdate("view_transmissions", { waitingText: null } );
}

tri_transmissions = function(order_col, order_way){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_transmissions");
  url.addParam("praticien_id", document.selPraticien.praticien_id.value);
  url.addParam("order_col", order_col);
  url.addParam("order_way", order_way);
  url.requestUpdate("view_transmissions", { waitingText: null } );
}

function markAsSelected(element) {
  $('list_patients').select('.selected').each(function (e) {e.removeClassName('selected')});
  $(element).up(1).addClassName('selected');
}

Main.add(function () {
  viewTransmissions();
  if($('last_trans')){
    $('last_trans').addClassName("selected");
  }
});

</script>

<table class="main">
  <tr>
    <td style="width: 150px;">
      <table class="form">
      	<tr>
			    <th class="category">Praticien</th>
			  </tr>
			  <tr>
			    <td>
			      <form name="selPraticien" action="?" method="get">
			        <input type="hidden" name="m" value="{{$m}}" />
			        <input type="hidden" name="tab" value="{{$tab}}" />
			        <select name="praticien_id" onchange="this.form.submit();">
						  {{foreach from=$praticiens item=_praticien}}
						    <option {{if $praticien_id == $_praticien->_id}}selected="selected"{{/if}} value="{{$_praticien->_id}}">{{$_praticien->_view}}</option>
						  {{/foreach}}
						  </select>
					  </form>
					</td>
			  </tr>
      </table>
			<table class="tbl" id="list_patients">
			  {{if $sejours|@count}}
			  <tr id="last_trans">
			    <td>
			      <a href="#" onclick="markAsSelected(this); viewTransmissions();">Transmissions des dernières 24h</a>
			    </td>
			  </tr>
			  <tr>
			    <th>Toutes les transmissions par patients</th>
			  </tr>
				{{foreach from=$sejours item=_sejour}}
				  <tr>
				    <td>
				      <a href="#" onclick="markAsSelected(this); viewTransmissions('{{$_sejour->_id}}');">{{$_sejour->_ref_patient->_view}}</a>
				    </td>
				  </tr>
				{{/foreach}}
				{{/if}}
			</table>
	  </td>
	  <td id="view_transmissions"></td>
	</tr>
</table>