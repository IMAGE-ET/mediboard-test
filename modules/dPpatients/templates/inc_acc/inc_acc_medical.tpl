<script type="text/javascript">

calculFinAmo = function(){  
  var oForm = document.editFrm;
  var sDate = oForm.fin_amo.value;  
      
  if((getCheckedValue(oForm.cmu) == 1 && sDate == "")){
    date = new Date;
    date.addDays(365);
    oForm.fin_amo.value = date.toDATE();
    sDate = date.toLocaleDate();
    var oDiv = $('editFrm_fin_amo_da');
    oDiv.innerHTML = sDate;
  }  
}


Medecin = {
  pop : function(type) {
    var url = new Url();
    url.setModuleAction("dPpatients", "vw_medecins");
    url.addParam("type", type);
    url.popup(700, 450, "Medecin");
  },
  
  del: function(sElementName) {
    oForm = document.editFrm;
    oFieldMedecin = eval("oForm.medecin" + sElementName);
    oFieldMedecinName = eval("oForm._medecin" + sElementName + "_name");
    oFieldMedecin.value = "";
    oFieldMedecinName.value = "";
  },
  
  set: function( key, nom, prenom, sElementName) {
    oForm = document.editFrm;
    oFieldMedecin = eval("oForm.medecin" + sElementName);
    oFieldMedecinName = eval("oForm._medecin" + sElementName + "_name");
    oFieldMedecin.value = key;
    oFieldMedecinName.value = "Dr. " + nom + " " + prenom;
  }
}

</script>

<table class="form">

  <tr>
    <th>{{mb_label object=$patient field="code_regime"}}</th>
    <td>{{mb_field object=$patient field="code_regime" tabindex="201"}}</td>
    <th>
      {{mb_label object=$patient field="medecin_traitant"}}
      {{mb_field object=$patient field="medecin_traitant" hidden=1}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin_traitant_name" size="30" value="Dr. {{$patient->_ref_medecin_traitant->_view}}" ondblclick="Medecin.pop('_traitant')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="Medecin.del('_traitant')">{{tr}}Delete{{/tr}}</button>
    </td>
    <td class="button"><button class="search" tabindex="251" type="button" onclick="Medecin.pop('_traitant')">Choisir</button></td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="caisse_gest"}}</th>
    <td>{{mb_field object=$patient field="caisse_gest" tabindex="202"}}</td>
    <th>
      {{mb_label object=$patient field="medecin1"}}
      {{mb_field object=$patient field="medecin1" hidden=1}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin1_name" size="30" value="Dr. {{$patient->_ref_medecin1->_view}}" ondblclick="Medecin.pop('1')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="Medecin.del('1')">{{tr}}Delete{{/tr}}</button>
    </td>
    <td class="button"><button class="search" tabindex="252" type="button" onclick="Medecin.pop('1')">Choisir</button></td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="centre_gest"}}</th>
    <td>{{mb_field object=$patient field="centre_gest" tabindex="203"}}</td>
    <th>
      {{mb_label object=$patient field="medecin2"}}
      {{mb_field object=$patient field="medecin2" hidden=1}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin2_name" size="30" value="{{if ($patient->_ref_medecin2)}}Dr. {{$patient->_ref_medecin2->_view}}{{/if}}" ondblclick="Medecin.pop('2')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="Medecin.del('2')">{{tr}}Delete{{/tr}}</button>
    </td>
    <td class="button">
      <button class="search" tabindex="253" type="button" onclick="Medecin.pop('2')">Choisir</button>
    </td>
  </tr>

  <tr>
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_field object=$patient field="regime_sante" tabindex="204"}}</td>
    <th>
      {{mb_label object=$patient field="medecin3"}}
      {{mb_field object=$patient field="medecin3" hidden=1}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin3_name" size="30" value="{{if ($patient->_ref_medecin3)}}Dr. {{$patient->_ref_medecin3->_view}}{{/if}}" ondblclick="Medecin.pop('3')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="Medecin.del('3')">{{tr}}Delete{{/tr}}</button>
    </td>
    <td class="button"><button class="search" tabindex="254" type="button" onclick="Medecin.pop('3')">Choisir</button></td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="ald"}}</th>
    <td>{{mb_field object=$patient field="ald" tabindex="205"}}</td>
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_field object=$patient field="incapable_majeur" tabindex="204"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td>{{mb_field object=$patient field="cmu" onchange="calculFinAmo();"}}</td>
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_field object=$patient field="ATNC" tabindex="206" onblur="tabs.changeTabAndFocus('correspondance', this.form.prevenir_nom)"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="deb_amo"}}</th>
    <td class="date">{{mb_field object=$patient field="deb_amo" tabindex="203" form="editFrm"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="fin_amo"}}</th>
    <td class="date">{{mb_field object=$patient field="fin_amo" tabindex="203" form="editFrm"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="code_exo"}}</th>
    <td>{{mb_field object=$patient field="code_exo" tabindex="203"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="notes_amo"}}</th>
    <td>{{mb_field object=$patient field="notes_amo" tabindex="203"}}</td>
  </tr>
  
</table>