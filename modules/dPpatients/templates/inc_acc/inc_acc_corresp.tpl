{{if !$patient->_id}}
  <div class="small-info">
    Veuillez créer la fiche patient avant de pouvoir ajouter ses correspondants.
  </div>
{{else}}
  <table style="width: 100%;">
    <tr>
      {{foreach from=`$patient->_ref_cp_by_relation` item=_correspondants}}
        {{assign var=_correspondant value=$_correspondants|@reset}}
        <td style="width: 33%;">
          <form name="editCorrespondant_{{$_correspondant->relation}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="dosql" value="do_correspondant_patient_aed" />
            <input type="hidden" name="callback" value="mapIdCorres" />
            {{mb_key object=$_correspondant}}
            <input type="hidden" name="patient_id" value="{{$_correspondant->patient_id}}" />
            <input type="hidden" name="relation" value="{{$_correspondant->relation}}" />
            <table class="form">
              <tr>
                <th class="category" colspan="2">{{mb_value object=$_correspondant field=relation}}</th>
              </tr>
              <tr>
                <th>{{mb_label object=$_correspondant field="nom"}}</th>
                <td>{{mb_field object=$_correspondant field="nom" onchange="this.form.onsubmit()"}}</td>
              </tr>
              
              {{if $_correspondant->relation != "employeur"}}
                <tr>
                  <th>{{mb_label object=$_correspondant field="prenom"}}</th>
                  <td>{{mb_field object=$_correspondant field="prenom" onchange="this.form.onsubmit()"}}</td>
                </tr>
              {{/if}}
              
              <tr>
                <th>{{mb_label object=$_correspondant field="adresse"}}</th>
                <td>{{mb_field object=$_correspondant field="adresse" onchange="this.form.onsubmit()"}}</td>
              </tr>
              <tr>
                <th>{{mb_label object=$_correspondant field="cp"}}</th>
                <td>{{mb_field object=$_correspondant field="cp" onchange="this.form.onsubmit()"}}</td>
              </tr>
              <tr>
                <th>{{mb_label object=$_correspondant field="ville"}}</th>
                <td>{{mb_field object=$_correspondant field="ville" onchange="this.form.onsubmit()"}}</td>
              </tr>
              <tr>
                <th>{{mb_label object=$_correspondant field="tel"}}</th>
                <td>{{mb_field object=$_correspondant field="tel" onchange="this.form.onsubmit()"}}</td>
              </tr>
              
              {{if $_correspondant->relation == "employeur"}}
                <tr>
                  <th>{{mb_label object=$_correspondant field="urssaf"}}</th>
                  <td>{{mb_field object=$_correspondant field="urssaf" onblur="this.form.onsubmit(); tabs.changeTabAndFocus('assure', this.form.assure_nom);"}}</td>
                </tr>
              {{else}}
                <tr>
                  <th>{{mb_label object=$_correspondant field="parente"}}</th>
                  <td>{{mb_field object=$_correspondant field="parente" emptyLabel="Choose" onchange="this.form.onsubmit()"}}</td>
                </tr>
              {{/if}}
              <tr>
                <th>{{mb_label object=$_correspondant field="email"}}</th>
                <td>{{mb_field object=$_correspondant field="email" onchange="this.form.onsubmit()"}}</th>
              </tr>
              <tr>
                <th>{{mb_label object=$_correspondant field="remarques"}}</th>
                <td>{{mb_field object=$_correspondant field="remarques" onchange="this.form.onsubmit()"}}</td>
              </tr>
            </table>
          </form>
        </td>
      {{/foreach}}
    </tr>
  </table>
{{/if}}