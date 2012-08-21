<script type="text/javascript">
togglePatientAddresse = function(input) {
  var checked = ($V(input) == 1);
  var radios = $(input).up('form').select('.adresse_par');
  
  radios.invoke('setVisibility', checked);
  
  if (!checked) {
    input.form.onsubmit();
  }
}

addOtherCorrespondant = function(medecin_id) {
  var form = getForm("addCorrespondant");
  $V(form.medecin_id, medecin_id);
  onSubmitFormAjax(form, {onComplete: reloadCorrespondants.curry('{{$consult->_id}}')});
}

reloadCorrespondants = function(consultation_id) {
  var url = new Url("cabinet", "ajax_reload_correspondants");
  url.addParam("consultation_id", consultation_id);
  url.requestUpdate("adresseParPrat");
}

Medecin = {
  form: null,
  edit : function() {
    this.form = getForm("editAdresseParPrat");
    var url = new Url("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
    var radios = this.form.adresse_par_prat_id;
    var lastRadio = radios;
    
    if (!Object.isElement(radios)) {
      lastRadio = radios[radios.length-1];
    }
    
    var viewElement = lastRadio.next('span');
    viewElement.update(view);
    viewElement.next('button').show();
    lastRadio.checked = true;
    lastRadio.value = id;
    lastRadio.form.onsubmit();
  }
};
</script>

<form name="addCorrespondant" method="post">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_correspondant_aed"/>
  <input type="hidden" name="correspondant_id" />
  <input type="hidden" name="patient_id" value="{{$consult->patient_id}}" />
  <input type="hidden" name="medecin_id" />
</form>

<div id="adresseParPrat">
  {{mb_include module=cabinet template=inc_list_patient_medecins}}
</div>