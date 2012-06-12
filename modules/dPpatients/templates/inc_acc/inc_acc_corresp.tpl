{{if !$patient->_id}}
  <div class="small-info">
    Veuillez créer la fiche patient avant de pouvoir ajouter ses correspondants.
  </div>
{{else}}
  <button type="button" class="new"
    onclick="Correspondant.edit(0, '{{$patient->_id}}', Correspondant.refreshList.curry('{{$patient->_id}}'))">
      {{tr}}CCorrespondantPatient-title-create{{/tr}}
  </button>
  <div id="list-correspondants">
    {{mb_include module=patients template=inc_list_correspondants
       correspondants_by_relation=`$patient->_ref_cp_by_relation`
       nb_correspondants=$patient->_ref_correspondants_patient|@count}}
  </div>
{{/if}}