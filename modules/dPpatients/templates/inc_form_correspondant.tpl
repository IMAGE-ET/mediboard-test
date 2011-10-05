<script type="text/javascript">
  InseeFields.initCPVille("editCorrespondant", "cp", "ville", "tel");

  toggleUrrsafParente = function(elt) {
    
    $("parente").toggle();
    if ($V(elt) == "employeur") {
      $("urssaf").setStyle({display: "table-row"});
      $("parente").setStyle({display: "none"});
      elt.form.parente.selectedIndex = 0;
    }
    else {
      $("parente").setStyle({display: "table-row"});
      $("urssaf").setStyle({display: "none"});
      $V(elt.form.urrsaf, "");
    }
  }
</script>

<form name="editCorrespondant" method="post" action="?">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_correspondant_patient_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$correspondant}}
  {{mb_field object=$correspondant field="patient_id" hidden=true}}
  
  <table class="tbl">
    <tr>
      <th colspan="2" class="title {{if $correspondant->_id}}modify{{/if}}">
        {{if $correspondant->_id}}
          {{tr}}CCorrespondantPatient-title-modify{{/tr}}
        {{else}}
          {{tr}}CCorrespondantPatient-title-create{{/tr}}
          <span style="float: right;">
            {{mb_field object=$correspondant field=relation onchange=toggleUrrsafParente(this)}}
          </span>
        {{/if}}
      </th>
    </tr>
    <tr>
      <th class="narrow">{{mb_label object=$correspondant field="nom"}}</th>
      <td>{{mb_field object=$correspondant field="nom"}}</td>
    </tr>
    
    {{if $correspondant->relation != "employeur"}}
      <tr>
        <th>{{mb_label object=$correspondant field="prenom"}}</th>
        <td>{{mb_field object=$correspondant field="prenom"}}</td>
      </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$correspondant field="adresse"}}</th>
      <td>{{mb_field object=$correspondant field="adresse"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="cp"}}</th>
      <td>{{mb_field object=$correspondant field="cp"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="ville"}}</th>
      <td>{{mb_field object=$correspondant field="ville"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="tel"}}</th>
      <td>{{mb_field object=$correspondant field="tel"}}</td>
    </tr>
    <tr {{if $correspondant->relation == "employeur"}}style="display: none;"{{/if}} id="parente">
      <th>{{mb_label object=$correspondant field="parente"}}</th>
      <td>{{mb_field object=$correspondant field="parente" emptyLabel="Choose"}}</td>
    </tr>
    <tr {{if $correspondant->relation != "employeur"}}style="display: none;"{{/if}} id="urssaf">
      <th>{{mb_label object=$correspondant field="urssaf"}}</th>
      <td>{{mb_field object=$correspondant field="urssaf"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$correspondant field="email"}}</th>
      <td>{{mb_field object=$correspondant field="email"}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="remarques"}}</th>
      <td>{{mb_field object=$correspondant field="remarques" onchange="this.form.onsubmit()"}}</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;">
        <button type="button" class="save" onclick="Correspondant.onSubmit(this.form);" style="margin: auto;">
          {{if !$correspondant->_id}}
            {{tr}}Create{{/tr}}
          {{else}}
            {{tr}}Save{{/tr}}
          {{/if}}
        </button>
        {{if $correspondant->_id}}
          <button type="button" onclick="Correspondant.confirmDeletion(this.form);" class="cancel">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>