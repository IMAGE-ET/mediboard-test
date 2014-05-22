{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    window.calendar_planning_deb = Calendar.regField(form.date_deb);
    window.calendar_planning_fin = Calendar.regField(form.date_fin);

    var element = form.elements.user_id,
      tokenField = new TokenField(element, {onChange: function(){}.bind(element)});

    var element_input = form.elements.user_view;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_input.name);

    url.autoComplete(element_input, null, {
      minChars: 2,
      method: "get",
      dropdown: true,
      updateElement: function(selected) {
        var guid = selected.get("id");
        var _name  = selected.down().down().getText();

        var to_insert = !tokenField.contains(guid);
        tokenField.add(guid);

        if (to_insert) {

          var btn = DOM.button({
            "type": "button",
            "className": "delete",
            "style": "display: inline-block !important",
            "onclick": "window.user_tag_token.remove($(this).up('li').get('tag_item_id')); this.up().remove();"
          });

          var li = DOM.li({
            "data-tag_item_id": guid,
            "id": "CTag-"+guid,
            "className": "tag"
          }, _name, btn);
          var br = DOM.br();
          $("user_tags").insert(br).insert(li);
        }

        var element_input = form.elements.user_view;
        $V(element_input, "");
      }
    });

    window.user_tag_token = tokenField;
  });

  function changePage(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <table class="main layout">
    <tbody>
      <tr>
        <td id="td_container_search">
          <input type="search" id="words" name="words" value="" placeholder="Saisissez les termes de votre recherche ici..." style="width:50em; height:2em; font-size:large;" onchange="$V(this.form.start, '0')" autofocus>
          {{mb_include module=search template=inc_tooltip_help}}
        </td>
      </tr>
    </tbody>
  </table>
  <table class="main layout" style="width: 80%">
    <tbody>
      <tr>
        <!-- Fieldset de tri par date -->
        <td style="width: 33%">
          <fieldset>
            <legend> Date</legend>
            <table style="width:100%">
              <tbody>
              <tr>
                <td>
                  <input type="radio" name="date_interval" id="dateUniqueDay" value="uniqueDay" checked="checked" onclick="$('span_date').hide(); $('span_date_deb').hide();">
                  <label for="dateUniqueDay">Jour seul</label>
                </td>
                <td>
                  <span id="span_date_deb" style="display: none;">
                    <label for="date_deb" style="display: block; width: 35px; float: left;">Début</label>
                  </span>
                  <input type="hidden" class="datetime" id="date_deb" name="date_deb" onchange="$V(this.form.start, '0')" >
                </td>
              </tr>
              <tr>
                <td>
                  <input type="radio" name="date_interval" id="dateSince" value="since" onclick="$('span_date').hide(); $('span_date_deb').hide();">
                  <label for="dateSince">Depuis</label>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="radio" name="date_interval" id="dateBetween" value="between" onclick="$('span_date').show(); $('span_date_deb').show();">
                  <label for="dateBetween">Comprise Entre</label>
                </td>
                <td>
                  <span id="span_date" style="display: none;">
                  <label for="date_fin" style="display: block; width: 35px; float: left;">Fin</label>
                  <input type="hidden" class="datetime" id="date_fin" name="date_fin" onchange="$V(this.form.start, '0')" >
                  </span>
                </td>
              </tr>
              </tbody>
            </table>
          </fieldset>
        </td>
        <!-- Fieldset de tri par Utilisateurs -->
        <td>
          <fieldset>
            <legend> Utilisateurs</legend>
            <table class="layout">
              <tr>
                <td>
                  <input type="text" name="user_view" class="autocomplete" value="" style="width: 15em;" placeholder="&mdash; Choisir un utilsateur"/>
                  <input type="hidden" name="user_id" value="" />
                </td>
              </tr>
              <tr>
                <td>
                  <ul id="user_tags" class="tags" style="float: none;">
                  </ul>
                </td>
              </tr>
            </table>
          </fieldset>
        </td>
        <!-- Fieldset de tri par Types -->
        <td>
          <fieldset>
            <legend>
              <input type="checkbox" name="searchAll" id="SearchAll" value="SearchAll" onclick="Search.checkAllCheckboxes(this, 'names_types[]')">
              <label for="SearchAll">Types</label>
            </legend>
            <table class="layout" id="first_indexing">
              <tr>
                <td>
                  <input type="checkbox" name="names_types[]" id="CCompteRendu" value="CCompteRendu"/>
                  <label for="CCompteRendu">Compte rendu</label>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="names_types[]" id="CTransmissionMedicale" value="CTransmissionMedicale">
                  <label for="CTransmissionMedicale"> Transmission Médicale</label>
                </td>
                </tr>
              <tr>
                <td>
                  <input type="checkbox" name="names_types[]" id="CObservationMedicale" value="CObservationMedicale">
                  <label for="CObservationMedicale"> Observation Médicale</label>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="names_types[]" id="CConsultation" value="CConsultation">
                  <label for="CConsultation"> Consultation de séjour</label>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="names_types[]" id="CConsultAnesth" value="CConsultAnesth">
                  <label for="CConsultAnesth"> Consultation anesthésique de séjour</label>
                </td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td></td>
        <td style="text-align: center">
          <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
        </td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>