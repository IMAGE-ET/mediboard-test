{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Formulaire de signature d'une ligne -->
<form name="validation-{{$line->_class}}-{{$line->_id}}" action="" method="post">
  <input type="hidden" name="dosql" value="{{$dosql}}" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	
	{{if $line->inscription}}
	  <input type="hidden" name="inscription" value="0" />
    <input type="hidden" name="recusee" value="0" />
	{{/if}}
	
	
	<!-- Si la ligne est signée et qu'on est le prescripteur, on peut supprimer la signature de la ligne -->
  {{if $line->signee && !$line->recusee && ($app->user_id == $line->praticien_id || $line->inscription)}}
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
  {{if $line->inscription}}
      {{if !$line->recusee}}
        <button type="button" id="recusation_{{$line->_id}}" class="tick"
          onclick="if ($V(getForm('editCommentaire-{{$line->_guid}}').commentaire) == '') {
                     alert('{{tr}}CPrescription.comment_for_recusion{{/tr}}'); 
                     return;
                   }
                   $V(this.form.inscription, 1);
                   $V(this.form.recusee, 1);
                   onSubmitFormAjax(this.form, {
                     onComplete: function() {
                       modalPrescription.close();
                       {{if $line->inscription}}
                         Prescription.reloadPrescSejour('{{$prescription->_id}}'); 
                       {{else}}
                         Prescription.reload.defer('{{$prescription->_id}}','','{{$div_refresh}}'); 
                       {{/if}}
                   } });">
        {{tr}}CPrescriptionLine.recuser_line{{/tr}}</button>
      {{elseif $line->recusee && $line->praticien_id && $line->praticien_id == $app->user_id}}
        <input type="hidden" name="signee" value="0" />
        <button type="button" class="cancel"
          onclick="$V(this.form.inscription, 1);
                   $V(this.form.signee, 0);
                   onSubmitFormAjax(this.form, {
                     onComplete: function() {
                     modalPrescription.close();
                     {{if $line->inscription}}
                       Prescription.reloadPrescSejour('{{$prescription->_id}}'); 
                     {{else}}
                       Prescription.reload.defer('{{$prescription->_id}}','','{{$div_refresh}}'); 
                     {{/if}}
                 }  });">{{tr}}CPrescriptionLine.cancel_recuser_line{{/tr}}</button>
      {{/if}}
    {{/if}}
</form>