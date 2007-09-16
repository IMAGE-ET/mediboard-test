<script type="text/javascript">

var CMU = {
  del: function() {
    oForm = document.editFrm;
    oForm.cmu.value = "";
    oDateDiv = $("editFrm_cmu_da");
    oDateDiv.innerHTML = "";
  },
  set: function(sDate) {
    oForm = document.editFrm;
    oForm.cmu.value = sDate;
    oDateDiv = $("editFrm_cmu_da");
    oDateDiv.innerHTML = Date.fromDATE(sDate).toLocaleDate();
  }
}

var Medecin = {
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
    <th>{{mb_label object=$patient field="regime_sante"}}</th>
    <td>{{mb_field object=$patient field="regime_sante" tabindex="201" size="40" maxlength="40"}}</td>
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
    <th>{{mb_label object=$patient field="cmu"}}</th>
    <td class="date">
      <div id="editFrm_cmu_da">{{$patient->cmu|date_format:"%d/%m/%Y"}}</div>
      {{mb_field object=$patient field="cmu" hidden=1 prop="date"}}
      <img id="editFrm_cmu_trigger" src="./images/icons/calendar.gif" alt="calendar"/>
      <button class="cancel notext" type="button" onclick="CMU.del()">{{tr}}Delete{{/tr}}</button>
      <button class="tick notext" type="button" onclick="CMU.set('{{$dateCMU}}')">{{tr}}Create{{/tr}}</button>
    </td>
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
    <th rowspan="2">{{mb_label object=$patient field="ald"}}</th>
    <td rowspan="2">
      {{mb_field object=$patient field="ald" tabindex="203"}}
    </td>
    <th>
      {{mb_label object=$patient field="medecin2"}}
      {{mb_field object=$patient field="medecin2" hidden=1}}
    </th>
    <td class="readonly">
      <input type="text" name="_medecin2_name" size="30" value="{{if ($patient->_ref_medecin2)}}Dr. {{$patient->_ref_medecin2->_view}}{{/if}}" ondblclick="Medecin.pop('2')" readonly="readonly" />
      <button class="cancel notext" type="button" onclick="Medecin.del('2')">{{tr}}Delete{{/tr}}</button>
    </td>
    <td class="button">
      <button class="search" tabindex="253" type="button" onclick="Medecin.pop('2')">Choisir</button></td>
  </tr>
  
  <tr>
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
    <th>{{mb_label object=$patient field="incapable_majeur"}}</th>
    <td>{{mb_field object=$patient field="incapable_majeur" tabindex="204"}}</td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$patient field="ATNC"}}</th>
    <td>{{mb_field object=$patient field="ATNC" tabindex="206" onblur="oAccord.changeTabAndFocus(2, this.form.prevenir_nom)"}}</td>

<!-- 
    <th>{{mb_label object=$patient field="SHS"}}</th>
    <td colspan="2">
      {{mb_field object=$patient field="SHS" tabindex="256" size="8" maxlength="8";"}}
    </td>
 -->
   </tr>

</table>