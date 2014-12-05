<script type="text/javascript">
  Main.add(function() {
    Control.Tabs.create('tabs_stats', true);
  });
  checkMaxPeriod = function(elt) {
    var form = elt.form;
    var date_min_elt = form._date_min;
    var date_max_elt = form._date_max;
    var date_min = new Date($V(date_min_elt));
    var date_max = new Date($V(date_max_elt));
    
    if (date_min.format("yyyy-MM-dd") > date_max.format("yyyy-MM-dd")) {
      return;
    }
    
    if (elt.name == "_date_min" ) {
      if (date_max.format("yy-MM-dd") > date_min.addDays(31).format("yy-MM-dd")) {
        $V(date_max_elt, date_min.format("yyyy-MM-dd"), false);
        $V(form._date_max_da, date_min.format("dd/MM/yyyy"), false);
      }
    }
    else if (date_min.format("yy-MM-dd") < date_max.addDays(-31).format("yy-MM-dd")) {
      $V(date_min_elt, date_max.format("yyyy-MM-dd"), false);
      $V(form._date_min_da, date_max.format("dd/MM/yyyy"), false);
    }
  };

  toggleCabinetFields = function(elt) {
    var mode = $V(elt);
    var form = elt.form;

    var disable = mode != "adresse_par";

    [
      form._function_id, form._other_function_id, form._user_id, form._date_min, form._date_min_da, form._date_max, form._date_max_da
    ].invoke(disable ? "disable" : "enable");
  };

  getSpreadSheet = function (form) {
    var url = new Url();
    url.addFormData(form);
    url.addParam('suppressHeaders', 1);
    url.addParam('csv', 1);
    url.popup(550, 300, 'stats_correspondants');
  }
</script>

<ul id="tabs_stats" class="control_tabs">
  <li>
    <a href="#nb_consults">Nombre de consultations</a>
  </li>
  <li>
    <a href="#prise_rdv">Prises de RDV</a>
  </li>
  <li>
    <a href="#medecins_correspondants">Médecins correspondants / adressants</a>
  </li>
</ul>
  
<div id="nb_consults" style="display: none;">
  <form name="FilterNbConsults" action="?" method="get" onsubmit="if (checkForm(this)) { return onSubmitFormAjax(this, null, 'refresh_nb_consults') }">
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="a" value="ajax_stats_nb_consults" />
  
    <table class="form">
      <tr>
        <th>{{mb_label object=$filter field=_function_id}}</th>
        <td>
          <select name="_function_id" onchange="$V(this.form._user_id, '', false)">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filter->_function_id}}
          </select>
        </td>
    
        <th>{{mb_label object=$filter field=_date_min}}</th>
        <td>{{mb_field object=$filter field=_date_min form=FilterNbConsults register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
      </tr>
      
      <tr>
        <th>
          {{mb_label object=$filter field=_other_function_id}}
        </th>
        <td>
          <select name="_other_function_id" class="{{$filter->_props._other_function_id}}">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filter->_other_function_id}}
          </select>
        </td>
    
        <th>{{mb_label object=$filter field=_date_max}}</th>
        <td>{{mb_field object=$filter field=_date_max form=FilterNbConsults register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
      </tr>
      <tr>
        <th>
          {{mb_label object=$filter field=_user_id}}
        </th>
        <td colspan="3">
          <select name="_user_id" onchange="$V(this.form._function_id, '', false)">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$filter->_user_id}}
          </select>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="4">
          <button type="submit" class="change">
            {{tr}}Compute{{/tr}}
          </button>
        </td>
      </tr>
    </table>
  </form>
  <div id="refresh_nb_consults"></div>
</div>

<div id="prise_rdv" style="display: none;">
  <form name="FilterPriseRdv" action="?" method="get" onsubmit="if (checkForm(this)) { return onSubmitFormAjax(this, null, 'refresh_prise_rdv')}">
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="a" value="ajax_stats_prise_rdv" />

    <table class="form">
      <tr>
        <th>
          {{mb_label object=$filter field=_user_id}}
        </th>
        <td>
          <select name="_user_id">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$filter->_user_id}}
          </select>
        </td>
    
        <th>{{mb_label object=$filter field=_date_min}}</th>
        <td>{{mb_field object=$filter field=_date_min form=FilterPriseRdv register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
      </tr>
      
      <tr>
        <th colspan="2"></th>
        <th>{{mb_label object=$filter field=_date_max}}</th>
        <td>{{mb_field object=$filter field=_date_max form=FilterPriseRdv register=true canNull=false onchange="checkMaxPeriod(this)"}}</td>
      </tr>
  
      <tr>
        <td class="button" colspan="4">
          <button type="submit" class="change">
            {{tr}}Compute{{/tr}}
          </button>
        </td>
      </tr>
    </table>
  </form>
  <div id="refresh_prise_rdv"></div>
</div>

<div id="medecins_correspondants" style="display: none;">
  <table class="main layout">
    <tr>
      <td>
        <fieldset>
          <legend>Se baser sur les médecins adressant les patients dans les RDV</legend>

          <form name="FilterMedCorrespondantsAdressePar" action="?" method="get" onsubmit="return Url.update(this, 'refresh_medecins_correspondants')">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="a" value="ajax_stats_medecins_correspondants" />
            <input type="hidden" name="compute_mode" value="adresse_par" />
            <input type="hidden" name="csv" value="0" />

            <table class="form">
              <tr>
                <th>{{mb_label object=$filter field=_function_id}}</th>
                <td>
                  <select name="_function_id" onchange="$V(this.form._user_id, '', false)">
                    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                    {{mb_include module=mediusers template=inc_options_function list=$functions selected=$filter->_function_id}}
                  </select>
                </td>

                <th>{{mb_label object=$filter field=_date_min}}</th>
                <td>{{mb_field object=$filter field=_date_min form=FilterMedCorrespondantsAdressePar register=true canNull=false}}</td>
              </tr>

              <tr>
                <th>
                  {{mb_label object=$filter field=_user_id}}
                </th>
                <td>
                  <select name="_user_id" onchange="$V(this.form._function_id, '', false)">
                    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                    {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$filter->_user_id}}
                  </select>
                </td>

                <th>{{mb_label object=$filter field=_date_max}}</th>
                <td>{{mb_field object=$filter field=_date_max form=FilterMedCorrespondantsAdressePar register=true canNull=false}}</td>
              </tr>
              <tr>
                <td class="button" colspan="4">
                  <button type="submit" class="change">
                    {{tr}}Compute{{/tr}}
                  </button>
                  <button type="button" class="download" onclick="getSpreadSheet(this.form)">
                    {{tr}}Download{{/tr}} (CSV)
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </fieldset>
      </td>

      <td>
        <fieldset>
          <legend>Se baser sur les médecins correspondants des patients</legend>

          <form name="FilterMedCorrespondants" action="?" method="get" onsubmit="return Url.update(this, 'refresh_medecins_correspondants')">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="a" value="ajax_stats_medecins_correspondants" />
            <input type="hidden" name="compute_mode" value="correspondants" />

            <table class="form">
              <tr>
                <td class="button">
                  <button type="submit" class="change">
                    {{tr}}Compute{{/tr}}
                  </button>
                  <button type="button" class="download" onclick="getSpreadSheet(this.form)">
                    {{tr}}Download{{/tr}} (CSV)
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </fieldset>
      </td>
    </tr>
  </table>

  <div id="refresh_medecins_correspondants"></div>
</div>

