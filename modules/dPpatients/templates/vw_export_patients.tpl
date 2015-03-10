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
  nextStep = function() {
    var form = getForm("export-patients");
    $V(form.start, parseInt($V(form.start))+parseInt($V(form.step)));

    if ($V(form.auto)) {
      form.onsubmit();
    }
  };

  updatePraticienCount = function(){
    var form = getForm("export-patients");
    $('praticien-count').update($V(form.elements["praticien_id[]"]).length);
  };

  Main.add(function(){
    updatePraticienCount();
  })
</script>

<table class="main layout">
  <tr>
    <td class="narrow">
      <form name="export-patients" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'export-log')">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="dosql" value="do_export_patients" />

        <table class="main form">
          <tr>
            <th>
              <label for="directory">Répertoire cible</label>
            </th>
            <td colspan="3">
              <input type="text" name="directory" value="{{$directory}}" size="60" />
            </td>
          </tr>

          <tr>
            <th rowspan="3">
              <label for="praticien_id">Praticiens</label>
            </th>
            <td rowspan="3">
              <select name="praticien_id[]" multiple size="10" onclick="updatePraticienCount()">
                {{foreach from=$praticiens item=_prat}}
                  <option value="{{$_prat->_id}}" {{if in_array($_prat->_id,$praticien_id)}}selected{{/if}}>{{$_prat}}</option>
                {{/foreach}}
              </select>
              <br />
              <span id="praticien-count">0</span> praticiens sélectionnés
            </td>

            <th>
              <label for="auto">Avance auto.</label>
            </th>
            <td>
              <input type="checkbox" name="auto" value="1" />
            </td>
          </tr>

          <tr>
            <th>
              <label for="start">Début</label>
            </th>
            <td>
              <input type="text" name="start" value="{{$start}}" size="4" />
            </td>
          </tr>

          <tr>
            <th>
              <label for="start">Pas</label>
            </th>
            <td>
              <input type="text" name="step" value="{{$step}}" size="4" />
            </td>
          </tr>

          <tr>
            <td colspan="2"></td>
            <td colspan="2">
              <button class="change">{{tr}}Export{{/tr}}</button>
            </td>
          </tr>
        </table>

      </form>
    </td>
    <td id="export-log"></td>
  </tr>
</table>