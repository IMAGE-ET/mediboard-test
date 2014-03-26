<script type="text/javascript">
  function updateCountsPat(start, count) {
    var form = getForm("do-import-patient-pat");
    $V(form.elements.start, start);
    $V(form.elements.count, count);

    if ($V(form.elements.auto)) {
      form.onsubmit();
    }
  }

  function updateCountsSejour(start, count) {
    var form = getForm("do-import-patient-sejour");
    $V(form.elements.start, start);
    $V(form.elements.count, count);

    if ($V(form.elements.auto)) {
      form.onsubmit();
    }
  }
</script>


<table>
  <tr>
    <td>
      {{mb_include module=system template=inc_object_import_specs object=$patient_specs class=CPatient}}
    </td>
    <td style="width: 50%; vertical-align: top">
      {{assign var=class value=imports}}
      <form name="EditConfig-{{$class}}" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {document.location.reload();}})">
        <input type="hidden" name="m" value="system" />
        <input type="hidden" name="dosql" value="do_configure" />
        <table class="form">
          {{mb_include module=system template=inc_config_str var=pat_csv_path}}
          {{mb_include module=system template=inc_config_str var=pat_start}}
          {{mb_include module=system template=inc_config_str var=pat_count}}

          <tr>
            <td class="button" colspan="6">
              <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
            </td>
          </tr>
        </table>
      </form>

      {{if !$conf.dPpatients.imports.pat_csv_path}}
        <div class="small-error">
          <strong>
            Il faut définir le chemin du fichier CSV à importer dans l'onglet <a href="?m=patients&tab=configure">Configurer</a>
          </strong>
        </div>
      {{else}}
        <form name="do-import-patient-pat" method="post" onsubmit="return onSubmitFormAjax(this, null, 'do-import-patient-pat-log')">
          <input type="hidden" name="m" value="patients" />
          <input type="hidden" name="dosql" value="do_import_patient" />
          <input type="hidden" name="callback" value="updateCountsPat" />

          <table class="main form" style="table-layout: fixed;">
            <tr>
              <th colspan="2" class="title">
                Importation de patients
              </th>
            </tr>
            <tr>
              <td colspan="2">
                <div class="small-info">Import du fichier <code>{{$conf.dPpatients.imports.pat_csv_path}}</code></div>
                <div class="small-warning">Attention ào l'établissement selectionné !</div>
              </td>
            </tr>
            <tr>
              <th>
                <label for="start">{{tr}}config-dPpatients-imports-pat_start{{/tr}}</label>
              </th>
              <td>
                <input type="number" name="start" value="{{$conf.dPpatients.imports.pat_start}}" size="5" />
              </td>
            </tr>
            <tr>
              <th>
                <label for="count">{{tr}}config-dPpatients-imports-pat_count{{/tr}}</label>
              </th>
              <td>
                <input type="number" name="count" value="{{$conf.dPpatients.imports.pat_count}}" size="5" />
              </td>
            </tr>
            <tr>
              <th>
                <label for="auto">Automatique</label>
              </th>
              <td>
                <input type="checkbox" name="auto" />
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center;"><button class="change">{{tr}}Import{{/tr}}</button></td>
            </tr>
          </table>
        </form>
        <table class="main tbl">
          <tr>
            <th>Ligne</th><th>Résultat</th>
          </tr>
          <tbody id="do-import-patient-pat-log">
          </tbody>
        </table>
      {{/if}}
    </td>
  </tr>

</table>