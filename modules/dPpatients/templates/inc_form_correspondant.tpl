{{mb_script module="patients" script="autocomplete" ajax=true}}

<script type="text/javascript">
  InseeFields.initCPVille("editCorrespondant", "cp", "ville", "tel");

  toggleUrrsafParente = function(elt) {
    
    $("parente").toggle();
    if ($V(elt) == "employeur") {
      $("urssaf").setStyle({display: "table-row"});
      $("parente").setStyle({display: "none"});
      $("parente_autre").setStyle({display: "none"});
      var form = getForm("editCorrespondant");
      $V(form.parente_autre, "");
      $V(form.relation_autre, "");
      elt.form.parente.selectedIndex = 0;
    }
    else {
      $("parente").setStyle({display: "table-row"});
      $("urssaf").setStyle({display: "none"});
      $V(elt.form.urrsaf, "");
    }
  }
  
  toggleRelationAutre = function(elt) {
    if ($V(elt) == "autre") {
      $('relation_autre').setStyle({display: "inline"});
    }
    else {
      $('relation_autre').setStyle({display: "none"});
    }
  }
  
  toggleParenteAutre = function(elt) {
    if ($V(elt) == "autre") {
      $('parente_autre').setStyle({display: "table-row"});
    }
    else {
      $("parente_autre").setStyle({display: "none"});
      $V(getForm("editCorrespondant").parente_autre, '');
    }
  }
</script>

<form name="editCorrespondant" method="post" action="?">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_correspondant_patient_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$correspondant}}
  {{mb_field object=$correspondant field="patient_id" hidden=true}}
  
  <table class="form">
    <tr>
      <th colspan="2" class="title {{if $correspondant->_id}}modify{{/if}}">
        {{if $correspondant->_id}}
          {{tr}}CCorrespondantPatient-title-modify{{/tr}}
        {{else}}
          {{tr}}CCorrespondantPatient-title-create{{/tr}}
          <span style="float: right; {{if $correspondant->relation != "autre"}}display: none;{{/if}}" id="relation_autre">
            {{mb_label object=$correspondant field=relation_autre}} :
              <input type="text" name="relation_autre" value="{{$correspondant->relation_autre}}" />
          </span>
          <span style="float: left;">
            {{mb_field object=$correspondant field=relation onchange="toggleRelationAutre(this); toggleUrrsafParente(this)" alphabet=true}}
          </span>
        {{/if}}
      </th>
    </tr>
    <tr>
      <th style="width: 30%;">{{mb_label object=$correspondant field="nom"}}</th>
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
    <tr>
      <th>{{mb_label object=$correspondant field="mob"}}</th>
      <td>{{mb_field object=$correspondant field="mob"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="fax"}}</th>
      <td>{{mb_field object=$correspondant field="fax"}}</td>
    </tr>
    <tr {{if $correspondant->relation == "employeur"}}style="display: none;"{{/if}} id="parente">
      <th>{{mb_label object=$correspondant field="parente"}}</th>
      <td>{{mb_field object=$correspondant field="parente" emptyLabel="Choose" onchange="toggleParenteAutre(this);"}}</td>
    </tr>
    <tr {{if $correspondant->parente != "autre"}} style="display: none;"{{/if}} id="parente_autre">
      <th>{{mb_label object=$correspondant field="parente_autre"}}</th>
      <td>{{mb_field object=$correspondant field="parente_autre"}}</td>
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