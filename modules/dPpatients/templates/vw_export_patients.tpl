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
    var form = getForm("export-patients-form");
    $V(form.start, parseInt($V(form.start))+parseInt($V(form.step)));

    if ($V(form.auto)) {
      form.onsubmit();
    }
  };

  nextStepSejours = function() {
    var form = getForm("export-sejours-form");
    $V(form.start, parseInt($V(form.start))+parseInt($V(form.step)));

    if ($V(form.auto)) {
      form.onsubmit();
    }
  };

  updatePraticienCount = function(){
    var list = $V($("praticien_ids"));
    $('praticien-count').update(list.length);

    var formSejour = getForm("export-sejours-form");
    $V(formSejour["praticien_id[]"], list);

    var formPatients = getForm("export-patients-form");
    $V(formPatients["praticien_id[]"], list);
  };

  Main.add(function(){
    updatePraticienCount();
    Control.Tabs.create("export-tabs", true);

    var sejourForm = getForm("export-sejours-form");
    Calendar.regField(sejourForm.date_min);
    Calendar.regField(sejourForm.date_max);
  })
</script>

<table class="main layout">
  <tr>
    <td class="narrow" style="vertical-align: bottom;">Praticiens (<span id="praticien-count">0</span> sélectionnés)</td>
    <td style="width: 500px;">
      <ul id="export-tabs" class="control_tabs">
        {{if "dPplanningOp"|module_active}}
          <li><a href="#export-sejours">Séjours</a></li>
        {{/if}}

        <li><a href="#export-patients">Patients</a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td rowspan="2">
      <select id="praticien_ids" multiple size="40" onclick="updatePraticienCount()">
        {{foreach from=$praticiens item=_prat}}
          <option value="{{$_prat->_id}}" {{if in_array($_prat->_id,$praticien_id)}}selected{{/if}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </td>

    <td>
      <div id="export-sejours" style="display: none;">
        <form name="export-sejours-form" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'export-log')">
          <input type="hidden" name="m" value="patients" />
          <input type="hidden" name="dosql" value="do_make_sejour_archives" />

          <select name="praticien_id[]" multiple style="display: none;">
            {{foreach from=$praticiens item=_prat}}
              <option value="{{$_prat->_id}}">{{$_prat}}</option>
            {{/foreach}}
          </select>

          <table class="main form">
            <tr>
              <th class="narrow">Date début</th>
              <td class="narrow"><input type="hidden" name="date_min" class="dateTime" /></td>
              <th class="narrow">Date fin</th>
              <td class="narrow"><input type="hidden" name="date_max" class="dateTime" /></td>
              <td class="narrow"></td>
              <td></td>
            </tr>

            <tr>
              <th>
                <label for="start">Début</label>
              </th>
              <td>
                <input type="text" name="start" value="{{$start}}" size="4" />
              </td>

              <th>
                <label for="step">Pas</label>
              </th>
              <td>
                <input type="text" name="step" value="{{$step}}" size="4" />
              </td>

              <th>
                <label for="auto">Avance auto.</label>
              </th>
              <td>
                <input type="checkbox" name="auto" value="1" />
              </td>
            </tr>

            <tr>
              <td colspan="6">
                <button class="change">{{tr}}Export{{/tr}}</button>
              </td>
            </tr>
          </table>

        </form>
      </div>

      <div id="export-patients" style="display: none;">
        <form name="export-patients-form" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'export-log')">
          <input type="hidden" name="m" value="patients" />
          <input type="hidden" name="dosql" value="do_export_patients" />

          <select name="praticien_id[]" multiple style="display: none;">
            {{foreach from=$praticiens item=_prat}}
              <option value="{{$_prat->_id}}">{{$_prat}}</option>
            {{/foreach}}
          </select>

          <table class="main form">
            <tr>
              <th>
                <label for="directory">Répertoire cible</label>
              </th>
              <td colspan="5">
                <input type="text" name="directory" value="{{$directory}}" size="60" />
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
                <button class="change">{{tr}}Export{{/tr}}</button>
              </td>
            </tr>
          </table>

        </form>
      </div>

      <div id="export-log"></div>
    </td>
  </tr>
</table>