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