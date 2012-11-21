{{mb_default var=readonly value=false}}

{{if !$readonly}}
<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.setTabCount("correspondance", "{{$nb_correspondants}}");
  });
</script>
{{/if}}

<table style="width: 100%;" class="tbl">
  <thead>
    <tr>
      <th class="category">{{mb_title class=CCorrespondantPatient field=nom}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=prenom}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=naissance}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=adresse}}</th>
      <th class="category">
        {{mb_title class=CCorrespondantPatient field=cp}} / {{mb_title class=CCorrespondantPatient field=ville}}
      </th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=tel}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=mob}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=fax}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=parente}}</th>
      {{if $conf.ref_pays == 1}}
        <th class="category">{{mb_title class=CCorrespondantPatient field=urssaf}}</th>
      {{/if}}
      <th class="category">{{mb_title class=CCorrespondantPatient field=email}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=remarques}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=date_debut}}</th>
      <th class="category">{{mb_title class=CCorrespondantPatient field=date_fin}}</th>

      {{if !$readonly}}
        <th class="category" style="width: 1%"></th>
      {{/if}}
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
             Rqes:{{mb_value object=$_correspondant field=remarques}}<br/>
             EAN : {{mb_value object=$_correspondant field=ean}}<br/>
             Assure_id : {{mb_value object=$_correspondant field=assure_id}}<br/>
              {{if $_correspondant->ean_id}}({{mb_value object=$_correspondant field=ean_id}}){{/if}}
            </td>
            <td>{{mb_value object=$_correspondant field=date_debut}}</td>
            <td>{{mb_value object=$_correspondant field=date_fin}}</td>

            {{if !$readonly}}
            <td>
              <button type="button" class="edit notext" onclick="Correspondant.edit('{{$_correspondant->_id}}', null, Correspondant.refreshList.curry('{{$patient_id}}'))"></button>
            </td>
            {{/if}}
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