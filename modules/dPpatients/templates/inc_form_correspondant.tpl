{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module="patients" script="autocomplete" ajax=true}}
{{mb_default var=mode_modele value=0}}

<script type="text/javascript">
  InseeFields.initCPVille("editCorrespondant", "cp", "ville", "tel");

  Main.add(function() {
    var form = getForm("editCorrespondant");
    Calendar.regField(form.date_debut, null, { noView: false } );
    Calendar.regField(form.date_fin  , null, { noView: false } );

    {{if !$correspondant->_id}}
      toggleUrrsafParente(form.relation);
    {{/if}}

    {{if !$mode_modele}}
      // Autocomplete sur le nom du correspondant
      var url = new Url("system", "ajax_seek_autocomplete");
      url.addParam("object_class", "CCorrespondantPatient");
      url.addParam("whereComplex[patient_id]", "IS NULL");
      url.addParam("input_field", "nom");
      url.addParam("view_field", "nom");
      url.autoComplete(form.nom, null, {
        minChars: 2,
        method: "get",
        select: "view",
        callback: function(input, queryString){
          var form = getForm("editCorrespondant");
          return queryString+"&where[relation]="+$V(form.relation);
        },
        updateElement: function(selectedElement) {
          this.afterUpdateElement(form.nom, selectedElement)
        },
        afterUpdateElement: function(field, selected){
          var form = field.form;
          var selected = selected.select(".view")[0];
          if (selected.innerHTML) {
            $V(form.nom, selected.get("nom"));
            $V(form.nom_jeune_fille, selected.get("nom_jeune_fille"));
            $V(form.prenom, selected.get("prenom"));
            $V(form.adresse, selected.get("adresse"));
            $V(form.cp, selected.get("cp"));
            $V(form.ville, selected.get("ville"));
            $V(form.tel, selected.get("tel"));
            $V(form.mob, selected.get("mob"));
            $V(form.fax, selected.get("fax"));
            $V(form.urssaf, selected.get("urssaf"));
            $V(form.ean, selected.get("ean"));
            $V(form.parente, selected.get("parente"));
            $V(form.email, selected.get("email"));
            $V(form.remarques, selected.get("remarques"));
          }
        }
      });
    {{/if}}

  } );

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
    else if ($V(elt) == "assurance") {
      $("urssaf").setStyle({display: "none"});
      $("parente").setStyle({display: "none"});
      $("parente_autre").setStyle({display: "none"});
    }
    else {
      $("parente").setStyle({display: "table-row"});
      $("urssaf").setStyle({display: "none"});
      console.log(elt);
      $V(elt.form.urrsaf, "");
    }
  }

  toggleRelationAutre = function(elt) {
    if ($V(elt) == "autre") {
      $("relation_autre").setStyle({display: "inline"});
    }
    else {
      $("relation_autre").setStyle({display: "none"});
    }
  }

  toggleParenteAutre = function(elt) {
    if ($V(elt) == "autre") {
      $("parente_autre").setStyle({display: "table-row"});
    }
    else {
      $("parente_autre").setStyle({display: "none"});
      $V(getForm("editCorrespondant").parente_autre, '');
    }
  }

  toggleConfiancePrevenir = function(elt) {
    if ($V(elt) == "confiance") {
      $("nom_jeune_fille").setStyle({display: "table-row"});
      $("prenom").setStyle({display: "table-row"});
      $("naissance").setStyle({display: "table-row"});
    }
    else if ($V(elt) == "prevenir") {
      $("nom_jeune_fille").setStyle({display: "none"});
      $("prenom").setStyle({display: "table-row"});
      $("naissance").setStyle({display: "table-row"});
    }
    else {
      $("nom_jeune_fille").setStyle({display: "none"});
      $("prenom").setStyle({display: "none"});
      $("naissance").setStyle({display: "none"});
    }
  }

  toggleAssurance = function(elt) {
      if ($V(elt) == "assurance") {
         {{if $conf.ref_pays == 2}}
          $("ean").setStyle({display: "table-row"});
          $("employeur").setStyle({display: "table-row"});
          $("num_assure").setStyle({display: "none"});
          $("ean_id").setStyle({display: "table-row"});
          $("assure_id").setStyle({display: "table-row"});
        {{/if}}
        $("date_debut").setStyle({display: "table-row"});
        $("date_fin").setStyle({display: "table-row"});
        $("prenom").setStyle({display: "none"});
      }
      else if ($V(elt) == "employeur") {
        {{if $conf.ref_pays == 2}}
          $("ean").setStyle({display: "none"});
          $("num_assure").setStyle({display: "table-row"});
          $("employeur").setStyle({display: "none"});
          $("ean_id").setStyle({display: "none"});
          $("assure_id").setStyle({display: "none"});
        {{/if}}
        $("prenom").setStyle({display: "none"});
        $("date_debut").setStyle({display: "table-row"});
        $("date_fin").setStyle({display: "table-row"});
      }
      else {
        {{if $conf.ref_pays == 2}}
          $("ean").setStyle({display: "none"});
          $("num_assure").setStyle({display: "none"});
          $("employeur").setStyle({display: "none"});
          $("ean_id").setStyle({display: "none"});
          $("assure_id").setStyle({display: "none"});
        {{/if}}
        $("prenom").setStyle({display: "table-row"});
        $("date_debut").setStyle({display: "none"});
        $("date_fin").setStyle({display: "none"});

      }
  }
</script>

<form name="editCorrespondant" method="post" action="?">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_correspondant_patient_aed" />
  {{if $mode_modele}}
    <input type="hidden" name="callback" value="CorrespondantModele.afterSave" />
    <input type="hidden" name="group_id" value="{{$g}}" />
  {{else}}
    {{mb_field object=$correspondant field="patient_id" hidden=true}}
  {{/if}}

  <input type="hidden" name="del" value="0" />
  {{mb_key object=$correspondant}}


  <table class="form">
    <tr>
      <th colspan="2" class="title {{if $correspondant->_id}}modify{{/if}}">
        {{if $correspondant->_id}}
          {{tr}}CCorrespondantPatient-title-modify{{/tr}}
        {{else}}
          <span style="float: right; {{if $correspondant->relation != "autre"}}display: none;{{/if}}" id="relation_autre">
            {{mb_label object=$correspondant field=relation_autre}} :
              <input type="text" name="relation_autre" value="{{$correspondant->relation_autre}}" />
          </span>
          <span style="float: left;">
            {{mb_field object=$correspondant field=relation onchange="toggleRelationAutre(this); toggleUrrsafParente(this); toggleConfiancePrevenir(this); toggleAssurance(this);" alphabet=true}}
          </span>
          </th>
        <tr>
          <th class="title" colspan="2">
            {{tr}}CCorrespondantPatient-title-create{{/tr}}
        {{/if}}
      </th>
    </tr>
    <tr>
      <th style="width: 30%;">{{mb_label object=$correspondant field="nom"}}</th>
      <td>
        {{if $mode_modele}}
          {{mb_field object=$correspondant field="nom"}}
        {{else}}
          <input type="text" name="nom" class="autocomplete" value="{{$correspondant->nom}}"/>
        {{/if}}
      </td>
    </tr>
    <tr {{if $correspondant->relation != "confiance"}}style="display: none;"{{/if}} id="nom_jeune_fille">
      <th>{{mb_label object=$correspondant field="nom_jeune_fille"}}</th>
      <td>{{mb_field object=$correspondant field="nom_jeune_fille"}}</td>
    </tr>

    <tr id="prenom" {{if !$correspondant->_id || ($correspondant->relation == "employeur" || $correspondant->relation == "assurance")}}style="display: none;"{{/if}}>
      <th>{{mb_label object=$correspondant field="prenom"}}</th>
      <td>{{mb_field object=$correspondant field="prenom"}}</td>
    </tr>

    <tr id="num_assure" {{if $correspondant->relation != "employeur" || !$correspondant->_id || $conf.ref_pays == 1}}style="display: none;"{{/if}}>
      <th>{{mb_label object=$correspondant field="num_assure"}}</th>
      <td>{{mb_field object=$correspondant field="num_assure"}}</td>
    </tr>

    {{if !$mode_modele}}
      <tr id="employeur" {{if ($correspondant->relation != "assurance" && $correspondant->_id) || $conf.ref_pays == 1}} style="display: none;"{{/if}}>
        <th>{{mb_label object=$correspondant field="employeur"}}</th>
        <td>
          <select name="employeur">
            <option value="">-- Choisir</option>
            {{foreach from=$patient->_ref_correspondants_patient item=_correspondant}}
              {{if $_correspondant->relation == "employeur"}}
                <option value="{{$_correspondant->_id}}" {{if $correspondant->employeur == $_correspondant->_id}}selected="selected"{{/if}}>{{$_correspondant->nom}}</option>
              {{/if}}
            {{/foreach}}
          </select>
        </td>
      </tr>
    {{/if}}

    <tr {{if $correspondant->relation != "confiance"}}style="display: none;"{{/if}} id="naissance">
      <th>{{mb_label object=$correspondant field="naissance"}}</th>
      <td>{{mb_field object=$correspondant field="naissance" form="editCorrespondant" register=true}}</td>
    </tr>

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

    <tr {{if $correspondant->relation != "employeur" || $conf.ref_pays == 2}}style="display: none;"{{/if}} id="urssaf">
      <th>{{mb_label object=$correspondant field="urssaf"}}</th>
      <td>{{mb_field object=$correspondant field="urssaf"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$correspondant field="email"}}</th>
      <td>{{mb_field object=$correspondant field="email"}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$correspondant field="remarques"}}</th>
      <td>{{mb_field object=$correspondant field="remarques"}}</td>
    </tr>


    {{* Switzerland *}}
    {{if $conf.ref_pays == 2}}
      <tr id="ean">
        <th>{{mb_label object=$correspondant field="ean"}}</th>
        <td>{{mb_field object=$correspondant field="ean"}}</td>
      </tr>
      <tr id="ean_id" {{if $correspondant->relation != "assurance" && $correspondant->_id}} style="display: none;"{{/if}}>
        <th>{{mb_label object=$correspondant field="ean_id"}}</th>
        <td>{{mb_field object=$correspondant field="ean_id"}}</td>
      </tr>

      <tr id="assure_id" {{if $correspondant->relation != "assurance" && $correspondant->_id}} style="display: none;"{{/if}}>
        <th>{{mb_label object=$correspondant field="assure_id"}}</th>
        <td>{{mb_field object=$correspondant field="assure_id"}}</td>
      </tr>
    {{/if}}

    {{if !$mode_modele}}
      <tr id="date_debut" {{if $correspondant->relation != "assurance" && $correspondant->relation != "employeur" && $correspondant->_id}} style="display: none;"{{/if}}>
        <th>{{mb_label object=$correspondant field="date_debut"}}</th>
        <td>{{mb_field object=$correspondant field="date_debut"}}</td>
      </tr>

      <tr id="date_fin" {{if $correspondant->relation != "assurance" && $correspondant->relation != "employeur" && $correspondant->_id}} style="display: none;"{{/if}}>
        <th>{{mb_label object=$correspondant field="date_fin"}}</th>
        <td>{{mb_field object=$correspondant field="date_fin"}}</td>
      </tr>
    {{/if}}

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