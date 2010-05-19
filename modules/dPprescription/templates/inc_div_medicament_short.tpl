{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
// On met à jour les valeurs de praticien_id
Main.add( function(){
  Prescription.refreshTabHeader("div_medicament","{{$prescription->_counts_by_chapitre.med}}","{{if $prescription->object_id}}{{$prescription->_counts_by_chapitre_non_signee.med}}{{else}}0{{/if}}");
} );


</script>
{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
<table class="tbl">
  <!-- Affichage des lignes de medicaments -->
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    {{include file="../../dPprescription/templates/inc_vw_line_pack.tpl" line=$curr_line}}
  {{/foreach}}
  
  <!-- Affichage des lignes de prescription_line_mixes -->
  {{foreach from=$prescription->_ref_prescription_line_mixes item=_prescription_line_mix}}
    {{include file="inc_vw_line_perf_pack.tpl"}}
  {{/foreach}}
  
  
  {{if $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
    <th colspan="8">Commentaires</th>
  </tr>
  {{/if}}
  <!-- Parcours des commentaires --> 
  {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
    {{include file="../../dPprescription/templates/inc_vw_line_pack.tpl" line=$_line_comment}}
  {{/foreach}}
</table> 
{{else}}
  <div class="small-info"> 
     Il n'y a aucun médicament dans cette prescription.
  </div>
{{/if}}
