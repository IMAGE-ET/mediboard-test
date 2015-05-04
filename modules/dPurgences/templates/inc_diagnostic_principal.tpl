{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=diagCanNull value=false}}

<th>{{mb_label object=$sejour field="DP"}}</th>
<td>
{{mb_script module="dPplanningOp" script="cim10_selector"}}

<script type="text/javascript">
  reloadDiagnostic = function(sejour_id, modeDAS) {
    if ($("dp_"+sejour_id)) {
      var url = new Url("dPurgences", "ajax_diagnostic_principal");
      url.addParam("sejour_id", sejour_id);
      url.requestUpdate("dp_"+sejour_id, function(){
        /* FIXME: VIRER CE CODE */
        var formName = "editSejour";
        var form = getForm(formName);
        var label = form.down("label[for=DP]");
        label.className = form.DP.className;
        label.id = "labelFor_"+formName+"_DP";
        label.htmlFor = formName+"_DP";

        var oElement = form.DP;
        oElement.id = formName+"_DP";
        oElement.observe("change", notNullOK)
                .observe("ui:change", notNullOK);
        oElement.fire("ui:change");

        // Ne donne pas la main sur les select des autres formulaires si on ne fait pas ça
        if (Prototype.Browser.IE) {
          form.keywords_code.select();
          form.keywords_code.blur();
        }
      });
    }
    if ($("cim")) {
      var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
      url.addParam("sejour_id", sejour_id);
      url.addParam("modeDAS", modeDAS);
      url.requestUpdate("cim");
    }
  };
  deleteCodeCim10 = function() {
    var oForm = getForm("editSejour");
    $V(oForm.keywords_code, '');
    $V(oForm.DP, '');
    submitSejour('{{$sejour->_id}}');
  };
  Main.add(function() {
    var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
    url.addParam("sejour_id", '{{$sejour->_id}}');
    url.autoComplete(getForm("editSejour").keywords_code, '', {
      minChars: 1,
      dropdown: true,
      select: "code",
      width: "250px",
      afterUpdateElement: function(oHidden) {
        $V(getForm('editSejour').DP, oHidden.value);
      }
    });
  });
</script>
 
  <input type="hidden" name="praticien_id" value="{{$sejour->praticien_id}}"/>

  {{assign var=notnull value=""}}
  {{if "CAppUI::conf"|static_call:"dPurgences Display check_dp":"CGroups-$g" == "2"}}
    {{if $diagCanNull}}
      {{assign var=notnull value="canNull"}} {{* canNull pour eviter d'avoir l'alert "notNull" sans arret *}}
    {{else}}
      {{assign var=notnull value="notNull"}}
    {{/if}}
  {{/if}}

  <input type="text" name="keywords_code" id="editSejour_keywords_code" class="autocomplete str" value="{{$sejour->DP}}" size="10"/>
  <input type="hidden" name="DP" class="{{$notnull}}" value="{{$sejour->DP}}" onchange="$V(this.form.keywords_code, this.value); submitSejour('{{$sejour->_id}}');"/>
  <script type="text/javascript">
    CIM10Selector.initDPUrgences = function() {
      this.sForm     = "editSejour";
      this.sView     = "DP";
      this.sChir     = "praticien_id";
      this.pop();
    }
  </script>
  <button type="button" class="search notext" onclick="CIM10Selector.initDPUrgences()">
    {{tr}}button-CCodeCIM10-choix{{/tr}}
  </button>
  <button type="button" class="cancel notext" onclick="deleteCodeCim10();">
    {{tr}}Delete{{/tr}}
  </button>
</td>

