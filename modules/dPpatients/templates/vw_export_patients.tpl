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

    $V($("praticien_ids_view"), list.join(","));
  };

  checkDirectory = function(input) {
    var url = new Url("patients", "ajax_check_export_dir");
    url.addParam("directory", $V(input));
    url.requestUpdate("directory-check");
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
          <option value="{{$_prat->_id}}" {{if in_array($_prat->_id,$praticien_id)}}selected{{/if}}>
            #{{$_prat->_id|pad:5:0}} -
            {{$_prat}}
          </option>
        {{/foreach}}
      </select>
      <input type="text" id="praticien_ids_view" size="30" onfocus="this.select()" />
      <button class="up notext" onclick="$V('praticien_ids', $V('praticien_ids_view').split(/,/))"></button>
    </td>

    <td>
      <div id="export-sejours" style="display: none;">
        <form name="export-sejours-form" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'export-log-sejours')">
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

          <div id="export-log-sejours"></div>
        </form>
      </div>

      <div id="export-patients" style="display: none;">
        <form name="export-patients-form" method="post" onsubmit="return onSubmitFormAjax(this, {useDollarV: true}, 'export-log-patients')">
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
                <input type="text" name="directory" value="{{$directory}}" size="60" onchange="checkDirectory(this)" />
                <div id="directory-check"></div>
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
              <th>
                <label for="all_prats">Tous les praticiens</label>
              </th>
              <td>
                <input type="checkbox" name="all_prats" value="1" {{if $all_prats}}checked{{/if}} />
              </td>

              <th>
                <label for="ignore_files">Ne pas copier les fichiers utilisateur</label>
              </th>
              <td colspan="3">
                <input type="checkbox" name="ignore_files" value="1" {{if $ignore_files}}checked{{/if}} />
              </td>
            </tr>

            <tr>
              <td colspan="6">
                <button class="change">{{tr}}Export{{/tr}}</button>
              </td>
            </tr>
          </table>

        </form>

        <div id="export-log-patients"></div>
      </div>
    </td>
  </tr>
</table>