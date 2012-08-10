<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.setTabCount("correspondance", "{{$nb_correspondants}}");
  });
</script>
<table style="width: 100%;" class="tbl">
  <thead>
    <tr>
      <th class="title">{{mb_label class=CCorrespondantPatient field=nom}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=prenom}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=naissance}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=adresse}}</th>
      <th class="title">
        {{mb_label class=CCorrespondantPatient field=cp}} / {{mb_label class=CCorrespondantPatient field=ville}}
      </th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=tel}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=mob}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=fax}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=parente}}</th>
      {{if $conf.ref_pays == 1}}
        <th class="title">{{mb_label class=CCorrespondantPatient field=urssaf}}</th>
      {{/if}}
      <th class="title">{{mb_label class=CCorrespondantPatient field=email}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=remarques}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=date_debut}}</th>
      <th class="title">{{mb_label class=CCorrespondantPatient field=date_fin}}</th>
      <th class="title" style="width: 1%"></th>
    </tr>
  </thead>
  {{if $nb_correspondants > 0}}
    {{foreach from=$correspondants_by_relation item=_correspondants key=relation}}
      {{if $_correspondants|@count}}
        <tr>
          <th colspan="{{if $conf.ref_pays == 1}}15{{else}}14{{/if}}">
            {{tr}}CCorrespondantPatient.relation.{{$relation}}{{/tr}}
          </th>
        </tr>
        {{foreach from=$_correspondants item=_correspondant}}
          <tr>
            <td>{{mb_value object=$_correspondant field=nom}}</td>
            <td>{{mb_value object=$_correspondant field=prenom}}</td>
            <td>{{mb_value object=$_correspondant field=naissance}}</td>
            <td>{{mb_value object=$_correspondant field=adresse}}</td>
            <td>
              {{mb_value object=$_correspondant field=cp}}
              {{mb_value object=$_correspondant field=ville}}
            </td>
            <td>{{mb_value object=$_correspondant field=tel}}</td>
            <td>{{mb_value object=$_correspondant field=mob}}</td>
            <td>{{mb_value object=$_correspondant field=fax}}</td>
            <td>
              {{if $_correspondant->relation != "employeur"}}
                {{if $_correspondant->parente == "autre"}}
                  {{mb_value object=$_correspondant field=parente_autre}}
                {{else}}
                  {{mb_value object=$_correspondant field=parente}}
                {{/if}}
              {{/if}}
            </td>
            {{if $conf.ref_pays == 1}}
              <td>
                {{if $_correspondant->relation == "employeur"}}
                  {{mb_value object=$_correspondant field=urssaf}}
                {{/if}}
              </td>
            {{/if}}
            <td>{{mb_value object=$_correspondant field=email}}</td>
            <td>
              {{mb_value object=$_correspondant field=remarques}}
              {{mb_value object=$_correspondant field=ean}}
            </td>            
            <td>{{mb_value object=$_correspondant field=date_debut}}</td>
            <td>{{mb_value object=$_correspondant field=date_fin}}</td>
            <td>
              <button type="button" class="edit notext" onclick="Correspondant.edit('{{$_correspondant->_id}}', null, Correspondant.refreshList.curry('{{$patient_id}}'))"></button>
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="13" class="empty">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      {{/if}}
    {{/foreach}}
  {{else}}
    <tr>
      <td colspan="13" class="empty">{{tr}}CCorrespondantPatient.none{{/tr}}</td>
    </tr>
  {{/if}}
</table>