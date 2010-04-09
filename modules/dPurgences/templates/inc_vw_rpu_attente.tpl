{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<div id="attente">
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
			  var url = new Url("dPurgences", "ajax_vw_attente");
			  url.addParam("rpu_id", oForm.rpu_id.value);
			  url.requestUpdate('attente');
			}
		}
	</script>
	
	<form name="Horodatage-{{$rpu->_guid}}" action="" method="post" onsubmit="return Horodatage.onSubmit(this)">
	  <input type="hidden" name="dosql" value="do_rpu_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPurgences" />
	
	  {{mb_key object=$rpu}}
		{{if !$rpu->radio_fin}}
	    {{mb_include template=inc_horodatage_field object=$rpu field=radio_debut}}
	  {{/if}}
		{{if $rpu->radio_debut}}
	    {{mb_include template=inc_horodatage_field object=$rpu field=radio_fin}}
		{{/if}}
		
		{{if !$rpu->bio_retour}}
	    {{mb_include template=inc_horodatage_field object=$rpu field=bio_depart}}
		{{/if}}
	  {{if $rpu->bio_depart}}	
	    {{mb_include template=inc_horodatage_field object=$rpu field=bio_retour}}
	  {{/if}}
		
	  {{if !$rpu->specia_arr}}
		  {{mb_include template=inc_horodatage_field object=$rpu field=specia_att}}
		{{/if}}
	  {{if $rpu->specia_att}}  
	    {{mb_include template=inc_horodatage_field object=$rpu field=specia_arr}}
	  {{/if}}
	</form>
</div>