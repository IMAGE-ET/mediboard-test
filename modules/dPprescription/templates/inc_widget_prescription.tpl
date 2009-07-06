{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !@$suffixe}}{{assign var=suffixe value="std"}}{{/if}}

<script type="text/javascript">
Prescription.suffixes.push("{{$suffixe}}");
Prescription.suffixes = Prescription.suffixes.uniq();

openPrescription = function(prescription_id){
  PrescriptionEditor.popup(prescription_id,'{{$object_id}}','{{$object_class}}','{{$type}}');
}
</script>

{{if is_array($prescriptions)}}
  <!-- Pas de prescription -->
  {{if !$prescriptions|@count}}
  <form name="addPrescriptionSejour{{$suffixe}}" action="?">
    <input type="hidden" name="m" value="dPprescription" />
    <input type="hidden" name="dosql" value="do_prescription_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="callback" value="openPrescription" />
    <input type="hidden" name="object_id" value="{{$object_id}}" />
    <input type="hidden" name="object_class" value="{{$object_class}}" />
    <input type="hidden" name="type" value="{{$type}}" />

    <!-- Creation d'une prescription externe ou de type sejour (pre_admission/sejour/sortie) -->
    <button type="button" class="new" onclick="submitFormAjax(this.form, 'systemMsg');">
      Créer une prescription de {{if $object_class == "CSejour"}}séjour{{else}}consultation{{/if}}
    </button>
  </form>
  {{else}}
  
  <table class="tbl">
    <tr>
      <th>Type</th>
      <th>Praticien</th>
      {{foreach from=$totals_by_chapitre item=total key=chapitre}}
        {{if $total}}
          <th>{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}</th>
  			{{/if}}
			{{/foreach}}
			<th></th>
    </tr>
    {{foreach from=$prescriptions item=_prescription}}
      <tr>
			  <td>
  			   <a href="#{{$_prescription->_id}}" onclick="PrescriptionEditor.popup('{{$_prescription->_id}}');">
  			     {{tr}}CPrescription.type.{{$_prescription->type}}{{/tr}}
  			   </a>
			  </td>
			  <td>{{$_prescription->_ref_praticien->_view}}</td>
			  {{foreach from=$_prescription->_counts_by_chapitre key=chapitre item=count}}
  			  {{if $totals_by_chapitre.$chapitre}}
    			  <td style="text-align: center;">
    			    {{if $count}}{{$count}}{{else}}-{{/if}}
    			  </td>
  			  {{/if}}
			  {{/foreach}}
			  <td style="width: 1%;">
			  {{if $_prescription->type == "externe"}}
			    <form name="addPrescriptionSejour{{$suffixe}}" action="?">
				    <input type="hidden" name="m" value="dPprescription" />
				    <input type="hidden" name="dosql" value="do_prescription_aed" />
				    <input type="hidden" name="del" value="1" />
				    <input type="hidden" name="prescription_id" value="{{$_prescription->_id}}" />
				    <!-- Creation d'une prescription externe ou de type sejour (pre_admission/sejour/sortie) -->
				    <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ PrescriptionEditor.refresh('{{$_prescription->object_id}}','{{$_prescription->object_class}}'); } } );">
				    Suppression de la prescription
				    </button>
				    <button type="button" class="print notext" onclick="Prescription.printPrescription('{{$_prescription->_id}}');">Imprimer</button>
				  </form>
			  {{/if}}
			  </td>
			</tr>
    {{/foreach}}
    </table>
  {{/if}}
{{/if}}