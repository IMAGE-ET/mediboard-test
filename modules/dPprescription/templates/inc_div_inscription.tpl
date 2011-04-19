{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
// refresh du nombre d'elements d'incriptions dans le volet	
Main.add( function(){
  Prescription.refreshTabHeader('div_inscription','{{$prescription->_counts_by_chapitre.inscription}}','{{$prescription->_counts_by_chapitre_non_signee.inscription}}');
	
	var oForm = getForm("addLine");
	oForm.prescription_line_medicament_id.value = "";
	oForm.del.value = "0";

});	
	
</script>

<!-- Affichage des medicaments -->
{{if $prescription->_ref_lines_inscriptions.med|@count}}
  <table class="tbl">
    <tr>
      <th colspan="6" class="title">Médicaments</th>
    </tr>
   <tr>
      <th style="width: 5%;" class="narrow">&nbsp;</th> 
      <th style="width: 25%">Produit</th>
      <th style="width: 40%;">Posologie</th>
      <th style="width: 10%">Début</th>
      <th style="width: 10%">Durée</th>
      <th style="width: 10%">Praticien</th>
    </tr>  
	</table>
  {{foreach from=$prescription->_ref_lines_inscriptions.med item=_line_med}}
    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" curr_line=$_line_med}}
  {{/foreach}}
{{/if}}

<!-- Affichage des elements -->
{{if $prescription->_ref_lines_inscriptions.elt|@count}}
  <table class="tbl">
	  <tr>
      <th colspan="6" class="title">Elements</th>
    </tr>
    <th style="width:25%;">Libellé</th>
    <th style="width:35%;">Prises</th>
    <th style="width:10%;">Début</th>
    <th style="width:10%;">Durée</th>
    <th style="width:10%;">Exécutant</th>
    <th style="width:10%;">Prat.</th>
  </table>
  {{foreach from=$prescription->_ref_lines_inscriptions.elt item=_line_elt}}
    {{include file="../../dPprescription/templates/inc_vw_line_element_lite.tpl" _line_element=$_line_elt element="inscription"}}
  {{/foreach}}
{{/if}}