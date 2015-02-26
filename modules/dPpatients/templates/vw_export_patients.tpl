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
  }
</script>

<table class="main layout">
  <tr>
    <th class="narrow">
      <form name="export-patients" method="post" onsubmit="return onSubmitFormAjax(this, null, 'export-log')">
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
            <th>
              <label for="praticien_id">Praticien</label>
            </th>
            <td>
              <select name="praticien_id">
                <option value="">&ndash;</option>
                {{foreach from=$praticiens item=_prat}}
                  <option value="{{$_prat->_id}}" {{if $_prat->_id == $praticien->_id}}selected{{/if}}>{{$_prat}}</option>
                {{/foreach}}
              </select>
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

            <th>
              <label for="start">Pas</label>
            </th>
            <td>
              <input type="text" name="step" value="{{$step}}" size="4" />
            </td>
          </tr>
        </table>

        <button class="change">{{tr}}Export{{/tr}}</button>
      </form>
    </th>
    <td id="export-log"></td>
  </tr>
</table>