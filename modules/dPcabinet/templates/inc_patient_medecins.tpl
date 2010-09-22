<script type="text/javascript">
togglePatientAddresse = function(input) {
  var checked = ($V(input) == 1);
  var radios = $(input).up('form').select('.adresse_par');
  
  radios.invoke('setVisibility', checked);
  
  if (!checked) {
    input.form.onsubmit();
  }
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
    
    lastRadio.checked = true;
    lastRadio.value = id;
    lastRadio.form.onsubmit();
  }
};
</script>

{{assign var=medecin value=$patient->_ref_medecin_traitant}}

<form name="editAdresseParPrat" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  {{mb_key object=$consult}}
  
  <label>
    {{mb_field object=$consult field=adresse typeEnum=checkbox 
               onchange="togglePatientAddresse(this)"}}
    {{tr}}CConsultation-adresse{{/tr}}
  </label>
  <br />
  
  {{assign var=medecin_found value=false}}
  
  {{if $medecin->_id}}
    <label onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
      <input type="radio" name="adresse_par_prat_id" value="{{$medecin->_id}}" class="adresse_par"
             {{if !$consult->adresse}}style="visibility:hidden"{{/if}}
             {{if $consult->adresse_par_prat_id == $medecin->_id}}
               {{assign var=medecin_found value=true}}
               checked="checked"
             {{/if}}
             onclick="this.form.onsubmit()" /> 
      <strong>{{$medecin}}</strong>
    </label>
    <br />
  {{/if}}
  
  {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
    {{assign var=medecin value=$curr_corresp->_ref_medecin}}
    <label onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
      <input type="radio" name="adresse_par_prat_id" value="{{$medecin->_id}}" class="adresse_par"
             {{if !$consult->adresse}}style="visibility:hidden"{{/if}}
             {{if $consult->adresse_par_prat_id == $medecin->_id}}
               {{assign var=medecin_found value=true}}
               checked="checked"
             {{/if}}
             onclick="this.form.onsubmit()" /> 
      {{$medecin}}
    </label>
    <br />
  {{/foreach}}
  
  <div class="adresse_par" {{if !$consult->adresse}}style="visibility:hidden"{{/if}}>
    <input type="radio" name="adresse_par_prat_id" value="{{if !$medecin_found}}{{$consult->adresse_par_prat_id}}{{/if}}" class="adresse_par"
           {{if !$medecin_found && $consult->adresse_par_prat_id}}checked="checked"{{/if}}
           onclick="Medecin.edit()" />
    <button type="button" class="search" onclick="$(this).previous('input').checked=true;Medecin.edit()">{{tr}}Other{{/tr}}</button> 
    <span>
      {{if !$medecin_found && $consult->adresse_par_prat_id}}
        {{$consult->_ref_adresse_par_prat}}
      {{/if}}
    </span>
  </div>
</form>