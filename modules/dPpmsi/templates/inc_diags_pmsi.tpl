{{*
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<!--  Diagnostic Principal -->
<form name="editDP" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$sejour}}
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
  <script>
    Main.add(function() {
      var url = new Url("cim10", "ajax_code_cim10_autocomplete");
      url.autoComplete(getForm("editDP").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        select: "code",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editDP").DP, oHidden.value);
        }
      });
    });

    deleteDP = function() {
      var oForm = getForm("editDP");
      $V(oForm.keywords_code, "");
      $V(oForm.DP, "");
      oForm.onsubmit();
    };
  </script>

  {{mb_label object=$sejour field=DP}}
  <input type="text" name="keywords_code" class="autocomplete str  code cim10" value="{{$sejour->DP}}" size="10"/>
  <input type="hidden" name="DP" onchange="this.form.onsubmit();"/>
  <button class="search notext" type="button" onclick="CIM10Selector.initDP({{$sejour->_id}})">
    {{tr}}Search{{/tr}}
  </button>
  <button type="button" class="cancel notext" onclick="deleteDP();"></button>
</form>

{{if $sejour->_ext_diagnostic_principal}}
  <strong>{{$sejour->_ext_diagnostic_principal->libelle}}</strong>
{{/if}}

<hr />

<!--  Diagnostic Relié -->
<form name="editDR" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'))">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />

  {{mb_label object=$sejour field=DR}}
  <script>
    Main.add(function(){
      var url = new Url("cim10", "ajax_code_cim10_autocomplete");
      url.autoComplete(getForm("editDR").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        select: "code",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editDR").DR, oHidden.value);
        }
      });
    });
    deleteDR = function() {
      var oForm = getForm("editDR");
      $V(oForm.keywords_code, "");
      $V(oForm.DR, "");
      oForm.onsubmit();
    };
  </script>
  <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$sejour->DR}}" size="10"/>
  <input type="hidden" name="DR" onchange="this.form.onsubmit();"/>
  <button class="search notext" type="button" onclick="CIM10Selector.initDR({{$sejour->_id}})">
    {{tr}}Search{{/tr}}
  </button>
  <button type="button" class="cancel notext" onclick="deleteDR();"></button>
</form>

{{if $sejour->_ext_diagnostic_relie}}
  <strong>{{$sejour->_ext_diagnostic_relie->libelle}}</strong>
{{/if}}

<hr />

<!--  Diagnostics Associés -->
<form name="editDA" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_class" value="CSejour" />
  <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />

  <label for="_added_code_cim" title="Diagnostics associés significatifs">DAS</label>
  <script type="text/javascript">
    Main.add(function(){
      var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
      url.autoComplete(getForm("editDA").keywords_code, '', {
        minChars: 1,
        dropdown: true,
        width: "250px",
        select: "code",
        afterUpdateElement: function(oHidden) {
          $V(getForm("editDA")._added_code_cim, oHidden.value);
        }
      });
    });
  </script>
  <input type="text" name="keywords_code" class="autocomplete str" value="" size="10"/>
  <input type="hidden" name="_added_code_cim" onchange="this.form.onsubmit();"/>
  <button class="search notext" type="button" onclick="CIM10Selector.initDAS({{$sejour->_id}})">
    {{tr}}Search{{/tr}}
  </button>
</form>

<hr />

{{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item="curr_cim"}}
  <form name="delCodeAsso-{{$curr_cim->code}}" action="?m={{$m}}" method="post"
        onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
    <input type="hidden" name="m" value="patients" />
    <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="object_class" value="CSejour" />
    <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
    <input type="hidden" name="_deleted_code_cim" value="{{$curr_cim->code}}" />
    <button class="trash notext" type="submit">
      {{tr}}Delete{{/tr}}
    </button>
  </form>
  {{$curr_cim->code}} : {{$curr_cim->libelle}}
  <br />
{{/foreach}}