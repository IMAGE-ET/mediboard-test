{{* $Id$ *}}

<script type="text/javascript">
Medecin = {
  form: null,
  edit : function(form) {
    this.form = form;
    var url = new Url();
    url.setModuleAction("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  del: function(form) {
    if (confirm("Voulez vous vraiment supprimer ce médecin ?")) {
      if (form.medecin_traitant)
        $V(form.medecin_traitant, '');
      else
        $V(form.del, 1);
    }
  },
  
  set: function(id) {
    $V(this.form.medecin_traitant ? this.form.medecin_traitant : this.form.medecin_id, id);
  }
};

submitMedecin = function(form) {
  return onSubmitFormAjax(form, {
    onComplete: function() {$('{{$widget_id}}').widget.refresh()} 
  });
}

{{if $patient && $patient->_id}}
Main.add(function () {
  var formCorresp = getForm("correspondant-new-{{$patient->_id}}");
  urlCorresp = new Url();
  urlCorresp.setModuleAction("dPpatients", "httpreq_do_medecins_autocomplete");
  urlCorresp.autoComplete(formCorresp._view, formCorresp._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formCorresp.medecin_id, element.id.split('-')[1]);
    }
  });
  
  var formTraitant = getForm("traitant-edit-{{$patient->_id}}");
  urlTraitant = new Url();
  urlTraitant.setModuleAction("dPpatients", "httpreq_do_medecins_autocomplete");
  urlTraitant.autoComplete(formTraitant._view, formTraitant._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formTraitant.medecin_traitant, element.id.split('-')[1]);
    }
  });
});
{{/if}}
</script>

{{if $patient && $patient->_id}}
<table class="form">
  <tr>
    <th style="width: 30%;">
      {{mb_label object=$patient field="medecin_traitant"}}
      {{mb_field object=$patient field="medecin_traitant" hidden=1}}
    </th>
    <td>
      <form name="traitant-edit-{{$patient->_id}}" action="?" method="post" onsubmit="return submitMedecin(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="medecin_traitant" value="{{$patient->medecin_traitant}}" onchange="this.form.onsubmit()" />
        Dr <input type="text" name="_view" size="30" value="{{$patient->_ref_medecin_traitant->_view}}" ondblclick="Medecin.edit(this.form)" />
        <div id="traitant-edit-{{$patient->_id}}__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
        <button class="search" type="button" onclick="Medecin.edit(this.form)">{{tr}}Choose{{/tr}}</button>
        <button class="cancel notext" type="button" onclick="Medecin.del(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>
  {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp name=corresp}}
  <tr>
    {{if $smarty.foreach.corresp.first}}
      <th rowspan="{{$patient->_ref_medecins_correspondants|@count}}">{{tr}}CPatient-back-medecins_correspondants{{/tr}}</th>
    {{/if}}
    <td class="readonly">
      <form name="correspondant-edit-{{$patient->_id}}" action="?" method="post" onsubmit="return submitMedecin(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_correspondant_aed" />
        <input type="hidden" name="del" value="" onchange="this.form.onsubmit()" />
        <input type="hidden" name="correspondant_id" value="{{$curr_corresp->_id}}" />
        <input type="hidden" name="patient_id" value="{{$curr_corresp->_ref_patient->_id}}" />
        <input type="hidden" name="medecin_id" value="{{$curr_corresp->_ref_medecin->_id}}" onchange="this.form.onsubmit()" />
        Dr <input type="text" name="_view" size="30" value="{{$curr_corresp->_ref_medecin->_view}}" ondblclick="Medecin.edit(this.form)" readonly="readonly" />
        <button class="search" type="button" onclick="Medecin.edit(this.form)">{{tr}}Change{{/tr}}</button>
        <button class="cancel notext" type="button" onclick="Medecin.del(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>
  {{/foreach}}
  <tr>
    <th>{{tr}}CCorrespondant-title-create{{/tr}}</th>
    <td>
      <form name="correspondant-new-{{$patient->_id}}" action="?" method="post" onsubmit="return submitMedecin(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_correspondant_aed" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="medecin_id" value="" onchange="this.form.onsubmit()" />
        Dr <input type="text" name="_view" size="30" value="" ondblclick="Medecin.edit(this.form)" />
        <div id="correspondant-new-{{$patient->_id}}__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
        <button class="search" type="button" onclick="Medecin.edit(this.form)">{{tr}}Choose{{/tr}}</button>
      </form>
    </td>
  </tr>
</table>
{{else}}
  <div class="big-info">
    Veuillez créer la fiche patient avant de pouvoir modifier ses médecins correspondants
  </div>
{{/if}}