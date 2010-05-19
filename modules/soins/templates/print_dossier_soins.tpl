{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !@$offline}}
<!-- Fermeture du tableau pour faire fonctionner le page-break -->
    </td>
  </tr>
</table>
{{/if}}

{{assign var=object value=$sejour->_ref_patient}}
<table class="tbl">
	<tr>
		<th class="title">
			{{$object->_view}}
		</th>
	</tr>
</table>
{{mb_include module=dPpatients template=CPatient_complete no_header=true}}

<br style="page-break-after: always;" />

{{assign var=object value=$sejour}}
<table class="tbl">
  <tr>
    <th class="title">
      {{$object->_view}}
    </th>
  </tr>
</table>
{{mb_include module=dPplanningOp template=CSejour_complete no_header=true}}

{{if $dossier|@count}}
  {{mb_include module=dPprescription template=inc_vw_dossier_cloture}}
{{/if}}

{{include file="../../dPpatients/templates/print_constantes.tpl"}}

<br style="page-break-after: always;" />

<table class="tbl">
  <tr>
    <th class="title">
      Prescription
    </th>
  </tr>
	{{if $prescription->_ref_lines_med_comments.med|@count || $prescription->_ref_lines_med_comments.comment|@count}}
  <tr>
  	<th>
  		Médicaments
  	</th>
  </tr>
	{{/if}}
	{{foreach from=$prescription->_ref_lines_med_comments.med item=line_med}}
	  <tr>
	  	<td>
	  	  {{mb_include module="dPprescription" template="inc_print_medicament" med=$line_med nodebug=true}}
    	</td>
	  </tr>
	{{/foreach}}

  {{foreach from=$prescription->_ref_lines_med_comments.comment item=line_med_comment}}
		<tr>
			<td>
		    {{mb_include module="dPprescription"  template="inc_print_commentaire" comment=$line_med_comment nodebug=true}}
    	</td>
		</tr>
  {{/foreach}}

	
	{{if $prescription->_ref_prescription_line_mixes|@count}}
	<tr>
		<th>Perfusions</th>
	</tr>
	{{/if}}
	{{foreach from=$prescription->_ref_prescription_line_mixes item=_prescription_line_mix}}
	<tr>
		<td>
		  {{mb_include module="dPprescription" template="inc_print_prescription_line_mix" perf=$_prescription_line_mix nodebug=true}}
	  </td>
	</tr>
	{{/foreach}}
	
  {{foreach from=$prescription->_ref_lines_elements_comments key=_chap item=_lines_by_chap}}
    {{if $_lines_by_chap|@count}}
    <tr>
      <th>
        {{tr}}CCategoryPrescription.chapitre.{{$_chap}}{{/tr}}
      </th>
    </tr>
    {{/if}}
    {{foreach from=$_lines_by_chap item=_lines_by_cat}}
      {{if array_key_exists('element', $_lines_by_cat)}}
			  {{foreach from=$_lines_by_cat.element item=line_elt}}
				  <tr>
				  	<td>
				  	   {{mb_include module="dPprescription" template="inc_print_element" elt=$line_elt nodebug=true}}
          	</td>
				  </tr>
        {{/foreach}}
			{{/if}}
			{{if array_key_exists('comment', $_lines_by_cat)}}
        {{foreach from=$_lines_by_cat.comment item=line_elt_comment}}
          <tr>
          	<td>
							 <li>
							   ({{$line_elt_comment->_ref_praticien->_view}})
							   {{$line_elt_comment->commentaire|nl2br}}
							</li>
          	</td>
          </tr>
				{{/foreach}}
      {{/if}}
    {{/foreach}}
   {{/foreach}}
</table>

{{if !@$offline}}

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>
 {{/if}}