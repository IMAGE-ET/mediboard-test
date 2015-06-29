{{*
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  nextStepPatients = function() {
    var form = getForm("import-patients-form");
    $V(form.start, parseInt($V(form.start))+parseInt($V(form.step)));

    if ($V(form.auto)) {
      form.onsubmit();
    }
  };

  checkDirectory = function(input, message) {
    var url = new Url("patients", "ajax_check_import_dir");
    url.addParam("directory", $V(input));
    url.requestUpdate(message);
  }
</script>

<form name="import-patients-form" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'import-log-patients')">
  <input type="hidden" name="m" value="patients" />
  <input type="hidden" name="dosql" value="do_import_patients_xml" />

  <table class="main form">
    <tr>
      <th>
        <label for="directory">Répertoire source</label>
      </th>
      <td colspan="5">
        <input type="text" name="directory" value="{{$directory}}" size="60" onchange="checkDirectory(this, this.next())" />
        <div></div>
      </td>
    </tr>

    <tr>
      <th>
        <label for="files_directory">Répertoire des fichiers (si pas dans le répértoire source)</label>
      </th>
      <td colspan="5">
        <input type="text" name="files_directory" value="{{$files_directory}}" size="60" onchange="checkDirectory(this, this.next())" />
        <div></div>
      </td>
    </tr>

    <tr>
      <th class="narrow">
        <label for="start">Début</label>
      </th>
      <td class="narrow">
        <input type="text" name="start" value="{{$start}}" size="4" />
      </td>

      <th class="narrow">
        <label for="step">Pas</label>
      </th>
      <td class="narrow">
        <input type="text" name="step" value="{{$step}}" size="4" />
      </td>

      <th class="narrow">
        <label for="auto">Avance auto.</label>
      </th>
      <td>
        <input type="checkbox" name="auto" value="1" />
      </td>
    </tr>

    <tr>
      <td colspan="6">
        <button class="change">{{tr}}Import{{/tr}}</button>
      </td>
    </tr>
  </table>

</form>

<div id="import-log-patients"></div>