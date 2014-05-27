<h2>Nettoyage des correspondants patient</h2>

<table class="main layout" style="table-layout: fixed;">
  <tr>
    <td>
      <div class="small-info">
        Cet outil permet d'effectuer une épuration des doublons de correspondants patients dûs à des importations en supprimant les doublons.
      </div>

      <form name="cleanup-correspondant-patient" method="post" onsubmit="return onSubmitFormAjax(this, {}, 'cleanup-correspondant-patient-log')">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="dosql" value="do_cleanup_correspondant_patient" />

        <table class="main form">
          <tr>
            <th>
              <label for="count_min">
                Traiter les doublons qui sont plus de
              </label>
            </th>

            <td>
              <input type="number" name="count_min" value="50" size="5" />
            </td>

            <td rowspan="3">
              <button type="submit" class="tick">{{tr}}Clean up{{/tr}}</button>
            </td>
          </tr>

          <tr>
            <th>
              <label for="merge_dates">
                Regrouper aussi les correspondants ayant des dates de début différentes
              </label>
            </th>
            <td>
              <input type="checkbox" name="merge_dates" value="1" />
            </td>
          </tr>

          <tr>
            <th>
              <label for="dry_run">
                Dry run (n'effectue pas de suppression)
              </label>
            </th>
            <td>
              <input type="checkbox" name="dry_run" value="1" checked />
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td id="cleanup-correspondant-patient-log"></td>
  </tr>
</table>

<h2>Assignement de genre pour la base de correspondants</h2>
<table class="main form">
  <tr>
    <td style="width: 50%;">
      <div class="small-info">
        Cet outil permet de réassigner les sexes sur les correspondants pour lesquels ils ne sont pas assignés
      </div>
      <form name="guess-correspondant-patient" method="post" onsubmit="return onSubmitFormAjax(this, {}, 'sex-correspondant-patient-log')">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="dosql" value="do_guess_massive_sex" />
        <input type="hidden" name="target_class" value="CCorrespondantPatient"/>
        <label><input type="checkbox" name="callback" value="guess-correspondant-patient"/>Automatique</label>
        <label><input type="checkbox" name="reset" value="1"/>Recommencer de zéro</label>
        <button>GO</button>
      </form>
    </td>
    <td id="sex-correspondant-patient-log"></td>
  </tr>

</table>