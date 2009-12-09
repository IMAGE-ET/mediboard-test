{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="radio">

<script type="text/javascript">
Horodatage = {
  onSubmit:  function(oForm) {
	  return onSubmitFormAjax(oForm, { 
	  	onComplete: function() { 
	  		Horodatage.reload(oForm) 
	  	} 
	  } ); 
	},
	reload: function(oForm) {
	  var url = new Url("dPurgences", "httpreq_vw_radio");
	  url.addParam("rpu_id", oForm.rpu_id.value);
	  url.requestUpdate('radio');
	}
}

</script>

<form name="Horodatage-{{$rpu->_guid}}" action="" method="post" onsubmit="return Horodatage.onSubmit(this)">
  <input type="hidden" name="dosql" value="do_rpu_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPurgences" />

  {{mb_key object=$rpu}}
  {{mb_include template=inc_horodatage_field object=$rpu field=radio_debut}}
  {{mb_include template=inc_horodatage_field object=$rpu field=radio_fin}}
  {{mb_include template=inc_horodatage_field object=$rpu field=bio_depart}}
  {{mb_include template=inc_horodatage_field object=$rpu field=bio_retour}}

</form>

</div>