{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Formulaire de signature d'une ligne -->
<form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	
	{{if $line->inscription}}
	  <input type="hidden" name="inscription" value="0" />
	{{/if}}
	
	
	<!-- Si la ligne est signée et qu'on est le prescripteur, on peut supprimer la signature de la ligne -->
  {{if $line->signee && ($app->user_id == $line->praticien_id || $line->inscription)}}
	  <!-- Annulation de la signature -->
    <input type="hidden" name="signee" value="0" />
    <button type="button" class="cancel" onclick="onSubmitFormAjax(this.form, { onComplete: function() { Prescription.reloadLine('{{$line->_guid}}'); } });">Annuler la signature</button>
  
	<!-- Sinon, si elle n'est pas signee -->
	{{elseif !$line->signee}}
	  <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
    <input type="hidden" name="signee" value="1" />
    <button type="button" class="tick" id="signature_{{$line->_id}}" 
		        onclick="{{if $line->praticien_id && ($app->user_id != $line->praticien_id)}}
						         if(!confirm('Attention, vous etes sur le point de signer une ligne créée par un autre praticien, êtes vous sur de vouloir continuer ?')){
										   return;
										 }
										 {{/if}}
						         onSubmitFormAjax(this.form, { 
	                      onComplete: function(){ 
												  modalPrescription.close(); 
												  {{if $line->inscription}}
													  Prescription.reloadPrescSejour('{{$prescription->_id}}'); 
	                        {{else}}
													  Prescription.reload.defer('{{$prescription->_id}}','','{{$div_refresh}}'); 
												  {{/if}}
												} });">
			{{if $line->inscription}}
			  Transformer l'inscription en prescription (signer)
			{{else}}
			  Signer
			{{/if}}
		</button>  
  {{/if}}
</form>