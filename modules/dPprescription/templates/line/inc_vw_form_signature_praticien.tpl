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
    <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
	{{/if}}
	
  {{if $line->signee}}
	  <!-- Annulation de la signature -->
    <input type="hidden" name="signee" value="0" />
    <button type="button" class="cancel" onclick="onSubmitFormAjax(this.form, { onComplete: function() { Prescription.reloadLine('{{$line->_guid}}'); } });">Annuler la signature</button>
  {{else}}
	  <!-- signature --> 
    <input type="hidden" name="signee" value="1" />
    <button type="button" class="tick" id="signature_{{$line->_id}}" 
		        onclick="onSubmitFormAjax(this.form, { 
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