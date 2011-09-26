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

{{mb_default var=in_modal value=0}}
{{assign var=object value=$sejour->_ref_patient}}

<script type="text/javascript">
  function togglePrintZone(name) {
    $$("."+name).each(function(elt) { elt.toggleClassName("not-printable"); });

    // Si un seul bloc est à imprimer, il faut retirer le style page-break.
    var patient = $$(".print_patient")[1];
    var sejour = $$(".print_sejour")[0];
    var prescription = $$(".print_prescription")[0];
    var task = $$(".print_tasks")[0];

    if (!patient.hasClassName("not-printable") && sejour.hasClassName("not-printable")
      && prescription.hasClassName("not-printable") && task.hasClassName("not-printable")) {
      patient.setStyle({pageBreakAfter: "auto"});
    }
    else if (!sejour.hasClassName("not-printable") && patient.hasClassName("not-printable") &&
      prescription.hasClassName("not-printable") && task.hasClassName("not-printable")) {
      sejour.setStyle({pageBreakAfter: "auto"});
    }
    else if (!prescription.hasClassName("not-printable") && patient.hasClassName("not-printable") &&
      sejour.hasClassName("not-printable") && task.hasClassName("not-printable")) {
      prescription.setStyle({pageBreakAfter: "auto"});
    }
    else {
      patient.setStyle({pageBreakAfter: "always"});
      sejour.setStyle({pageBreakAfter: "always"});
      prescription.setStyle({pageBreakAfter: "always"});
    }
  }

  Main.add(function() {
    window.print();
  });
</script>

<table class="tbl print_patient">
  <tr class="not-printable">
    <td>
      <strong>Choix des blocs à imprimer : </strong>
      <label><input type="checkbox" checked="checked" onchange="togglePrintZone('print_patient')" /> Patient</label>
      <label><input type="checkbox" checked="checked" onchange="togglePrintZone('print_sejour')" /> Séjour</label>
      <label><input type="checkbox" checked="checked" onchange="togglePrintZone('print_prescription')"/> Prescription</label>
      <label><input type="checkbox" checked="checked" onchange="togglePrintZone('print_tasks')"/> Tâches</label>
      <button class="print" type="button" onclick="window.print()">Imprimer</button>
    </td>
  </tr>
	<tr>
		<th class="title">
      {{if $in_modal}}
        <button style="float: right;" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
      {{/if}}      
			{{$object->_view}}
      {{mb_include module=dPpatients template=inc_vw_ipp ipp=$object->_IPP}}
		</th>
	</tr>
</table>

{{mb_include module=dPpatients template=CPatient_complete no_header=true}}

{{assign var=object value=$sejour}}
<table class="tbl print_sejour" style="border: none !important; page-break-after: always;">
  <thead>
    <tr>
      <th class="title">
        {{$object->_view}}
        {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA}}
      </th>
    </tr>
  </thead>
  <tr>
    <td>
      {{mb_include module=dPplanningOp template=CSejour_complete no_header=true}}
    </td>
  </tr>
  {{if $dossier|@count}}
    <tr>
      <td>
        {{mb_include module=dPprescription template=inc_vw_dossier_cloture}}
      </td>
    </tr>
  {{/if}}
  <tr>
    <td>
      {{include file="../../dPpatients/templates/print_constantes.tpl"}}
    </td>
  </tr>
</table>

{{*
{{if "forms"|module_active}}
<div id="ex-objects-container">
  <table class="main tbl">
    <tr>
      <th class="title">Formulaires</th>
    </tr>
  </table>
  <div id="ex-objects"></div>
</div>
{{/if}}
*}}

<table class="tbl print_prescription" style="page-break-after: always;">
  <thead>
    <tr>
      <th class="title">
        {{$sejour->_view}}
        {{mb_include module=dPplanningOp template=inc_vw_numdos nda=$sejour->_NDA}}
      </th>
    </tr>
  </thead>
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
	  	<td class="text">
	  	  {{mb_include module="dPprescription" template="inc_print_medicament" med=$line_med nodebug=true print=false dci=0}}
    	</td>
	  </tr>
	{{/foreach}}

  {{foreach from=$prescription->_ref_lines_med_comments.comment item=line_med_comment}}
		<tr>
			<td class="text">
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
		<td class="text">
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
    {{if $conf.dPprescription.CPrescription.display_cat_for_elt}}
      {{foreach from=$_lines_by_chap item=_lines_by_cat}}
        {{assign var=cat_displayed value="0"}}
        {{if array_key_exists('element', $_lines_by_cat) || array_key_exists('comment', $_lines_by_cat)}}
          <tr>
            <td class="text">
            {{if array_key_exists('comment', $_lines_by_cat)}}
              {{foreach from=$_lines_by_cat.element item=line_elt name=foreach_lines}}
                {{if $smarty.foreach.foreach_lines.first}}
                  {{assign var=cat_displayed value="1"}}
                  <strong>{{$line_elt->_ref_element_prescription->_ref_category_prescription->nom}} :</strong>
                {{/if}}
                {{mb_include module="dPprescription" template="inc_print_element" elt=$line_elt nodebug=true}}
              {{/foreach}}
            {{/if}}
            {{if array_key_exists('comment', $_lines_by_cat)}}
              {{foreach from=$_lines_by_cat.comment item=line_elt_comment name=foreach_lines}}
                {{if $smarty.foreach.foreach_lines && !$cat_displayed}}
                  <strong>{{$line_elt_comment->_ref_category_prescription->nom}} :</strong>
                {{/if}}
                <li>
                   ({{$line_elt_comment->_ref_praticien->_view}})
                   {{$line_elt_comment->commentaire|nl2br}}
                </li>
              {{/foreach}}
            {{/if}}
            </td>
          </tr>
        {{/if}}
      {{/foreach}}
    {{else}}
      {{foreach from=$_lines_by_chap item=_lines_by_cat}}
        {{if array_key_exists('element', $_lines_by_cat)}}
          {{foreach from=$_lines_by_cat.element item=line_elt}}
            <tr>
              <td class="text">
                 {{mb_include module="dPprescription" template="inc_print_element" elt=$line_elt nodebug=true}}
              </td>
            </tr>
          {{/foreach}}
        {{/if}}
        {{if array_key_exists('comment', $_lines_by_cat)}}
          {{foreach from=$_lines_by_cat.comment item=line_elt_comment}}
            <tr>
              <td class="text">
                 <li>
                   ({{$line_elt_comment->_ref_praticien->_view}})
                   {{$line_elt_comment->commentaire|nl2br}}
                </li>
              </td>
            </tr>
          {{/foreach}}
        {{/if}}
      {{/foreach}}
    {{/if}}
  {{/foreach}}
</table>

{{mb_include module=soins template=inc_vw_tasks_sejour mode_realisation=0 readonly=1}}

{{if !@$offline}}

<!-- re-ouverture du tableau -->
<table>
  <tr>
    <td>
 {{/if}}