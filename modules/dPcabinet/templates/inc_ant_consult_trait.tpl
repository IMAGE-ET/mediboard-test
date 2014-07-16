{{mb_default var=addform value=""}}
{{mb_default var=type_see value=""}}

<script>
  updateFieldsCDS = function(selected) {
    var cds_text = selected.down('.view').getText();
    var cds_type = selected.get("type");
    var cds_code = selected.get("code");

    var oFormAllergie = getForm("editAntFrm{{$addform}}");
    if (cds_type == "CHA") {
      $V(oFormAllergie.type, "alle");
    }
    else {
      $V(oFormAllergie.type, "");
    }

    $V(oFormAllergie.appareil, "");
    $V(oFormAllergie.rques, cds_text);
    $V(oFormAllergie._idex_code, cds_code);
    $V(oFormAllergie._idex_tag, "COMPENDIUM_CDS");

    return onSubmitAnt(oFormAllergie);
  }
</script>

<tr>
  <td>
    {{mb_include module=cabinet template=inc_ant_allergie}}

    {{if "dPprescription"|module_active || $conf.dPpatients.CTraitement.enabled}}
      {{mb_include module=cabinet template=inc_traitement}}
    {{/if}}

    {{if $conf.ref_pays == 1}}
      <fieldset>
        <legend>Base de données CIM</legend>
        {{main}}
          var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
          url.autoComplete(getForm("addDiagFrm").keywords_code, '', {
            minChars: 1,
            dropdown: true,
            width: "250px",
            select: "code",
            afterUpdateElement: function(oHidden) {
              oForm = getForm("addDiagFrm");
              $V(oForm.code_diag, oHidden.value);
              reloadCim10($V(oForm.code_diag));
            }
          });
        {{/main}}
        <form name="addDiagFrm" action="?m=dPcabinet" method="post" onsubmit="return false;">
          <strong>Ajouter un diagnostic</strong>
          <input type="hidden" name="chir" value="{{$userSel->_id}}" />
          <input type="text" name="keywords_code" class="autocomplete str code cim10" value="" size="10"/>
          <input type="hidden" name="code_diag" onchange="$V(this.form.keywords_code, this.value)"/>
          <button class="search" type="button" onclick="CIM10Selector.init()">{{tr}}Search{{/tr}}</button>
          <button class="tick notext" type="button" onclick="reloadCim10(this.form.code_diag.value)">{{tr}}Validate{{/tr}}</button>
          <script>
            CIM10Selector.init = function(){
              this.sForm = "addDiagFrm";
              this.sView = "code_diag";
              this.sChir = "chir";
              this.options.mode = "favoris";
              this.pop();
            }
          </script>
        </form>
      </fieldset>
    {{/if}}
  </td>
</tr>