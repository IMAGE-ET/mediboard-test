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
      $V(form._view, '');
      if (form.medecin_traitant)
        $V(form.medecin_traitant, '');
      else
        $V(form.del, 1);
    }
  },
  
  set: function(id, view) {
    $V(this.form.medecin_traitant ? this.form.medecin_traitant : this.form.medecin_id, id);
    $V(this.form._view, view);
  }
};

submitMedecin = function(form) {
	// Update main form for unexisting patient 
	if (!$V(form.patient_id)) {
		$V(document.editFrm.medecin_traitant, $V(form.medecin_traitant));
		return;
	}

	// Submit for existing patient
  return onSubmitFormAjax(form, {
    onComplete: function() {$('{{$widget_id}}').widget.refresh()} 
  });
}

Main.add(function () {
  var formTraitant = getForm("traitant-edit-{{$patient->_id}}");
  urlTraitant = new Url();
  urlTraitant.setModuleAction("dPpatients", "httpreq_do_medecins_autocomplete");
  urlTraitant.autoComplete(formTraitant._view, formTraitant._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formTraitant.medecin_traitant, element.id.split('-')[1]);
      $V(formTraitant._view, element.select(".view")[0].innerHTML);
    }
  });
	
	{{if $patient && $patient->_id}}
  var formCorresp = getForm("correspondant-new-{{$patient->_id}}");
  urlCorresp = new Url();
  urlCorresp.setModuleAction("dPpatients", "httpreq_do_medecins_autocomplete");
  urlCorresp.autoComplete(formCorresp._view, formCorresp._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formCorresp.medecin_id, element.id.split('-')[1]);
      $V(formCorresp._view, element.select(".view")[0].innerHTML.stripTags());
    }
  });
	{{/if}}

});
</script>

<table class="form">
  <tr>
    <th style="width: 30%;">
      {{mb_label object=$patient field="medecin_traitant"}}
      {{mb_field object=$patient field="medecin_traitant" hidden=1}}
    </th>
    <td>
    	{{mb_ternary var=medecin_traitant_id   test=$patient->_id value=$patient->medecin_traitant other=""}}

			{{* mb_ternary won't work : will throw a warning *}}    	
    	{{assign var=medecin_traitant_view value=""}}
    	{{if $patient->_id}}
    	{{assign var=medecin_traitant_view value=$patient->_ref_medecin_traitant->_view}}
			{{/if}}

      <form name="traitant-edit-{{$patient->_id}}" action="?" method="post" onsubmit="return submitMedecin(this)">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
        <input type="hidden" name="medecin_traitant" value="{{$medecin_traitant_id}}" onchange="this.form.onsubmit()""/>
        <input type="text" name="_view" size="50" value="{{$medecin_traitant_view}}" ondblclick="Medecin.edit(this.form)" />
        <div id="traitant-edit-{{$patient->_id}}__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
        <button class="search" type="button" onclick="Medecin.edit(this.form)">{{tr}}Choose{{/tr}}</button>
        <button class="cancel notext" type="button" onclick="Medecin.del(this.form)">{{tr}}Delete{{/tr}}</button>
      </form>
    </td>
  </tr>
  
	{{if $patient && $patient->_id}}
	
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
        <input type="text" name="_view" size="50" value="{{$curr_corresp->_ref_medecin->_view}}" ondblclick="Medecin.edit(this.form)" readonly="readonly" />
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
        <input type="text" name="_view" size="50" value="" ondblclick="Medecin.edit(this.form)" />
        <div id="correspondant-new-{{$patient->_id}}__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
        <button class="search" type="button" onclick="Medecin.edit(this.form)">{{tr}}Choose{{/tr}}</button>
      </form>
    </td>
  </tr>
 
 	{{else}}
 	<tr>
 	  <td colspan="2" class="text">
		  <div class="big-info">
		    Veuillez créer la fiche patient avant de pouvoir ajoute ou supprimer ses médecins correspondants.
		  </div>
 	  </td>
 	</tr>
	{{/if}}
</table>
