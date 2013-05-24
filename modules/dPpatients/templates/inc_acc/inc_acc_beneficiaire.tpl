<table style="width: 100%">
  <tr>
    <td style="width: 50%">

<script type="text/javascript">
checkFinAmo = function(){
  var form = getForm("editFrm");
  var fin_amo = $V(form.fin_amo);
  var warning = $("fin_amo_warning");
  var tab = $$("#tab-patient a[href='#beneficiaire']")[0];
  
  if (fin_amo && fin_amo < (new Date()).toDATE()) {
    warning.show();
    tab.addClassName("wrong");
  }
  else {
    warning.hide();
    tab.removeClassName("wrong");
  }
};

Main.add(checkFinAmo);

</script>

<table class="form">
  <col style="width: 50%;" />
  
  <tr>
    <th>{{mb_label object=$patient field="code_regime"}}</th>
    <td>{{mb_field object=$patient field="code_regime"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="caisse_gest"}}</th>
    <td>{{mb_field object=$patient field="caisse_gest"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="centre_gest"}}</th>
    <td>{{mb_field object=$patient field="centre_gest"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_gestion"}}</th>
    <td>{{mb_field object=$patient field="code_gestion"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="centre_carte"}}</th>
    <td>{{mb_field object=$patient field="centre_carte"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_field object=$patient field="regime_sante"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="deb_amo"}}</th>
    <td>{{mb_field object=$patient field="deb_amo" form="editFrm" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="fin_amo"}}</th>
    <td>
      {{mb_field object=$patient field="fin_amo" form="editFrm" register=true onchange="checkFinAmo()"}} {{* event observer doesn't work :( *}}
      <div class="small-warning" id="fin_amo_warning" style="display: none;">
        Période de droits terminée
      </div>
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_exo"}}</th>
    <td>{{mb_field object=$patient field="code_exo"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="code_sit"}}</th>
    <td>{{mb_field object=$patient field="code_sit"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="regime_am"}}</th>
    <td>{{mb_field object=$patient field="regime_am"}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field="medecin_traitant_declare"}}</th>
  	<td>{{mb_field object=$patient field="medecin_traitant_declare"}}</td>
  </tr>
  {{if $patient->INSC}}
    <tr>
      <th>{{mb_label object=$patient field="INSC"}}</th>
      <td>{{mb_value object=$patient field="INSC"}} ({{$patient->INSC_date|date_format:$conf.date}})</td>
    </tr>
  {{/if}}
</table>

    </td>
    <td style="width: 50%">

<table class="form">
  <tr>
    <th>{{mb_label object=$patient field="ald"}}</th>
    <td>{{mb_field object=$patient field="ald"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_field object=$patient field="incapable_majeur"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td>{{mb_field object=$patient field="cmu" onchange="calculFinAmo();"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_field object=$patient field="ATNC"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="is_smg"}}</th>
    <td>{{mb_field object=$patient field="is_smg"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="fin_validite_vitale"}}</th>
    <td>{{mb_field object=$patient field="fin_validite_vitale" form="editFrm" register=true}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="mutuelle_types_contrat"}}</th>
    <td>
      {{* 
      <script type="text/javascript">
        Main.add(function(){
          window.mutuelleToken = new TokenField(getForm("editFrm").mutuelle_types_contrat);
        });
      </script>
      {{mb_field object=$patient field="mutuelle_types_contrat" hidden=true}}
      
      <div id="mutuelle-types-contrats">
        {{foreach from=$patient->_mutuelle_types_contrat item=_type_contrat}}
          <div>
            <button type="button" class="remove notext" onclick="mutuelleToken.remove(this.innerHTML); $(this).up().remove()">{{$_type_contrat}}</button> {{$_type_contrat}}
          </div>
        {{/foreach}}
        <button type="button" class="add notext" onclick="var n=$(this).next(); mutuelleToken.add(n.value); n.value=''"></button>
        <input type="text" />
      </div>
       *}}
      {{mb_field object=$patient field="mutuelle_types_contrat"}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="notes_amo"}}</th>
    <td>{{mb_field object=$patient field="notes_amo"}}</td>
  </tr>
  <tr>
  	<th>{{mb_label object=$patient field="libelle_exo"}}</th>
  	<td>{{mb_field object=$patient field="libelle_exo" onblur="tabs.changeTabAndFocus('correspondance', getForm('editCorrespondant_prevenir').nom)"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$patient field="notes_amc"}}</th>
    <td>{{mb_field object=$patient field="notes_amc"}}</td>
  </tr>
</table>

    </td>
  </tr>
</table>