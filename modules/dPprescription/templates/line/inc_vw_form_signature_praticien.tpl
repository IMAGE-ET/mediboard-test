{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->_class_name == "CPrescriptionLineElement" || $line->_class_name == "CPrescriptionLineComment"}}
  <!-- Signature d'un element -->
  <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
    <input type="hidden" name="dosql" value="{{$dosql}}" />
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
    {{if $line->signee}}
      <input type="hidden" name="signee" value="0" />
      <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription_reelle->_id}}','','{{$div_refresh}}') } }  )">Annuler la signature</button>
    {{else}}
      <input type="hidden" name="signee" value="1" />
      <button type="button" class="tick" id="signature_{{$line->_id}}" onclick="submitFormAjax(this.form,'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription_reelle->_id}}','','{{$div_refresh}}') } }  )">Signer</button>  
    {{/if}}
  </form>
{{else}}
  <form name="validation-{{$line->_class_name}}-{{$line->_id}}" action="" method="post">
    <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="prescription_line_guid" value="{{$line->_guid}}" />
    <input type="hidden" name="prescription_reelle_id" value="{{$prescription_reelle->_id}}" />
    <input type="hidden" name="mode_pharma" value="0" />
		{{if $line->signee}}
    <input type="hidden" name="annulation" value="1" />
    {{/if}}
    <button type="button" class="{{if $line->signee}}cancel{{else}}tick{{/if}}" id="signature_{{$line->_id}}" onclick="submitFormAjax(this.form,'systemMsg');">{{if $line->signee}}Annuler signature{{else}}Signer{{/if}}</button>
  </form>
{{/if}}