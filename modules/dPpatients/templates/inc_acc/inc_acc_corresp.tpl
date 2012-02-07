{{if !$patient->_id}}
  <div class="small-info">
    Veuillez créer la fiche patient avant de pouvoir ajouter ses correspondants.
  </div>
{{else}}
  <button type="button" class="new" onclick="Correspondant.edit(0, '{{$patient->_id}}')">{{tr}}CCorrespondantPatient-title-create{{/tr}}</button>
  <table style="width: 100%;" class="tbl">
    <thead>
      <tr>
        <th class="title">{{mb_label class=CCorrespondantPatient field=nom}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=prenom}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=adresse}}</th>
        <th class="title">
          {{mb_label class=CCorrespondantPatient field=cp}} / {{mb_label class=CCorrespondantPatient field=ville}}
        </th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=tel}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=mob}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=fax}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=parente}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=urssaf}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=email}}</th>
        <th class="title">{{mb_label class=CCorrespondantPatient field=remarques}}</th>
        <th class="title" style="width: 1%"></th>
      </tr>
    </thead>
    <tbody id="list-correspondants">
      {{mb_include module=dPpatients template=inc_list_correspondants
         correspondants_by_relation=`$patient->_ref_cp_by_relation`
         nb_correspondants=$patient->_ref_correspondants_patient|@count}}
    </tbody>
  </table>
{{/if}}