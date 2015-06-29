<!-- $Id$ -->

<script>
  function checkFormPrint() {
    var form = document.paramFrm;
    if (!(checkForm(form))) {
      return false;
    }
    popPlages();
  }

  function popPlages() {
    var form = document.paramFrm;
    var url = new Url('dPcabinet', 'print_plages');

    url.addElement(form._date_min);
    url.addElement(form._date_max);
    url.addElement(form.chir);
    url.addParam("_coordonnees", $V(form._coordonnees));
    url.addParam("_plages_vides", $V(form._plages_vides));
    url.addParam("_non_pourvues", $V(form._non_pourvues));
    url.addParam("_telephone", $V(form._telephone));
    url.addParam("_print_ipp", $V(form._print_ipp));

    {{if 'dPcabinet Planning show_print_order_mode'|conf:"CGroups-$g"}}
      var sorting_mode = $V(form.sorting_mode);
      if (sorting_mode != 'chrono') {
        url.addParam('a', 'print_listing_consults');
        url.addParam('sorting_mode', sorting_mode);
      }
    {{/if}}

    url.popup(700, 550, "Planning");
  }

  function changeDate(sDebut, sFin) {
    var oForm = document.paramFrm;
    oForm._date_min.value = sDebut;
    oForm._date_max.value = sFin;
    oForm._date_min_da.value = Date.fromDATE(sDebut).toLocaleDate();
    oForm._date_max_da.value = Date.fromDATE(sFin).toLocaleDate();
  }

  function changeDateCal() {
    var oForm = document.paramFrm;
    oForm.select_days[0].checked = false;
    oForm.select_days[1].checked = false;
    oForm.select_days[2].checked = false;
    oForm.select_days[3].checked = false;
  }
</script>

<form name="paramFrm" action="?m=dPcabinet" method="post" onsubmit="return checkFormPrint()">

  <table class="main layout">
    <tr>
      <td>
        <table class="form">
          <tr>
            <th class="category" colspan="3">Choix de la période</th>
          </tr>

          <tr>
            <td>{{mb_label object=$filter field="_date_min"}}</td>
            <td>{{mb_field object=$filter field="_date_min" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
            <td rowspan="2">
              <input type="radio" name="select_days" onclick="changeDate('{{$now}}','{{$now}}');" value="day" checked="checked" />
              <label for="select_days_day">Jour courant</label>
              <br /><input type="radio" name="select_days" onclick="changeDate('{{$tomorrow}}','{{$tomorrow}}');" value="tomorrow" />
              <label for="select_days_tomorrow">Lendemain</label>
              <br /><input type="radio" name="select_days" onclick="changeDate('{{$week_deb}}','{{$week_fin}}');" value="week" />
              <label for="select_days_week">Semaine courante</label>
              <br /><input type="radio" name="select_days" onclick="changeDate('{{$month_deb}}','{{$month_fin}}');" value="month" />
              <label for="select_days_month">Mois courant</label>
            </td>
          </tr>

          <tr>
            <td>{{mb_label object=$filter field="_date_max"}}</td>
            <td>{{mb_field object=$filter field="_date_max" form="paramFrm" canNull="false" onchange="changeDateCal()" register=true}} </td>
          </tr>
        </table>
      </td>

      <td>
        <table class="main form">
          <tr>
            <th class="category" colspan="2">{{tr}}common-Filter settings{{/tr}}</th>
          </tr>

          <tr>
            <th><label for="chir" title="Praticien">Praticien</label></th>

            <td>
              <select name="chir">
                <option value="0">&mdash; Tous</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$listChir}}
              </select>
            </td>
          </tr>

          {{if 'dPcabinet Planning show_print_order_mode'|conf:"CGroups-$g"}}
            <tr>
              <th class="category" colspan="2">{{tr}}common-Sort settings{{/tr}}</th>
            </tr>

            <tr>
              <th>{{tr}}common-Sorting mode{{/tr}}</th>

              <td>
                <select name="sorting_mode">
                  <option value="chrono">{{tr}}common-Chronological order{{/tr}}</option>
                  <option value="day">{{tr}}common-Birth day{{/tr}}</option>
                  <option value="month">{{tr}}common-Birth month{{/tr}}</option>
                  <option value="year">{{tr}}common-Birth year{{/tr}}</option>
                </select>
              </td>
            </tr>
          {{/if}}
        </table>
      </td>

      <td>
        <table class="main form">
          <tr>
            <th class="category" colspan="2">{{tr}}common-Display settings{{/tr}}</th>
          </tr>
          {{assign var="class" value="CConsultation"}}

          <tr class="not-full">
            <th>
              <label for="_print_ipp_1" title="Afficher ou cacher l'IPP">Afficher l'IPP</label>
            </th>

            <td>
              <label>
                <input type="radio" name="_print_ipp" value="1" {{if $filter->_print_ipp == "1"}}checked="checked"{{/if}} />
                {{tr}}common-Yes{{/tr}}
              </label>

              <label>
                <input type="radio" name="_print_ipp" value="0" {{if $filter->_print_ipp == "0"}}checked="checked"{{/if}} />
                {{tr}}common-No{{/tr}}
              </label>
            </td>
          </tr>

          <tr>
            <th>{{mb_label object=$filter field="_telephone"}}</th>
            <td>{{mb_field object=$filter field="_telephone"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$filter field="_coordonnees"}}</th>
            <td>{{mb_field object=$filter field="_coordonnees"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$filter field="_plages_vides"}}</th>
            <td>{{mb_field object=$filter field="_plages_vides"}}</td>
          </tr>

          <tr>
            <th>{{mb_label object=$filter field="_non_pourvues"}}</th>
            <td>{{mb_field object=$filter field="_non_pourvues"}}</td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="3">
        <button type="button" class="print" onclick="checkFormPrint()">Afficher</button>
      </td>
    </tr>
  </table>
</form>