{{* $Id$ *}}

<script type="text/javascript">
Medecin = {
  form: null,
  widgetId: null,
  edit : function(form, widget_id) {
    this.form = form;
    this.widgetId = widget_id;
    var url = new Url();
    url.setModuleAction("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  del: function(form, widget_id) {
    var widget = $(widget_id).widget;
    if (confirm("Voulez vous vraiment supprimer ce médecin ?")) {
      if (form.medecin_traitant) {
        $V(form.medecin_traitant, '');
      }
      else {
        $V(form.del, 1);
      }
      onSubmitFormAjax(form, {onComplete: function() {widget.refresh()} });
    }
  },
  
  set: function(id) {
    var widget = $(this.widgetId).widget;
    if (this.form.medecin_traitant) {
      $V(this.form.medecin_traitant, id);
    } 
    else {
      $V(this.form.medecin_id, id);
    }
    onSubmitFormAjax(this.form, {onComplete: widget.refresh.bind(widget)});
  }
};
</script>

{{if $patient && $patient->_id}}
<table class="form">
  <tr>
    <th style="width: 30%;">
      {{mb_label object=$patient field="medecin_traitant"}}
      {{mb_field object=$patient field="medecin_traitant" hidden=1}}
    </th>
    <td class="readonly">
      <form name="traitant-edit-{{$patient->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="medecin_traitant" value="" />
        <input type="text" name="_view" size="30" value="Dr {{$patient->_ref_medecin_traitant->_view}}" ondblclick="Medecin.edit(this.form, '{{$widget_id}}')" readonly="readonly" />
        <button class="search" type="button" onclick="Medecin.edit(this.form, '{{$widget_id}}')">Choisir</button>
        <button class="cancel notext" type="button" onclick="Medecin.del(this.form, '{{$widget_id}}')">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>
  {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp name=corresp}}
  <tr>
    <th>{{tr}}CCorrespondant{{/tr}} {{$smarty.foreach.corresp.iteration}}</th>
    <td class="readonly">
      <form name="correspondant-edit-{{$patient->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_correspondant_aed" />
        <input type="hidden" name="del" value="" />
        <input type="hidden" name="correspondant_id" value="{{$curr_corresp->_id}}" />
        <input type="hidden" name="patient_id" value="{{$curr_corresp->_ref_patient->_id}}" />
        <input type="hidden" name="medecin_id" value="{{$curr_corresp->_ref_medecin->_id}}" />
        <input type="text" name="_view" size="30" value="Dr {{$curr_corresp->_ref_medecin->_view}}" ondblclick="Medecin.edit(this.form, '{{$widget_id}}')" readonly="readonly" />
        <button class="search" type="button" onclick="Medecin.edit(this.form, '{{$widget_id}}')">Choisir</button>
        <button class="cancel notext" type="button" onclick="Medecin.del(this.form, '{{$widget_id}}')">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>
  {{/foreach}}
  <tr>
    <th>{{tr}}CCorrespondant-title-create{{/tr}}</th>
    <td class="readonly">
      <form name="correspondant-new-{{$patient->_id}}" action="?" method="post">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_correspondant_aed" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="medecin_id" value="" />
        <input type="text" name="_view" size="30" value="" ondblclick="Medecin.edit(this.form, '{{$widget_id}}')" readonly="readonly" />
        <button class="search" type="button" onclick="Medecin.edit(this.form, '{{$widget_id}}')">Choisir</button>
      </form>
    </td>
  </tr>
</table>
{{else}}
  <div class="big-info">
    Veuillez enregistrer le patient avant de pouvoir modifier ses médecins correspondants
  </div>
{{/if}}