{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esLogSearch");
    window.calendar_planning_fin = Calendar.regField(form._min_date);
    window.calendar_planning_fin = Calendar.regField(form._max_date);
    window.calendar_planning_fin = Calendar.regField(form._date);
    getAutocomplete();
    window.user_tag_token.add('{{$app->user_id}}');
    insertTag('{{$app->_ref_user->_guid}}', '{{$app->_ref_user}}');
  });

  function changePage(start) {
    var form = getForm("esLogSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  }

  function insertTag(guid, name) {
    var tag = $("CTag-" + guid);

    if (!tag) {
      var btn = DOM.button({
        "type": "button",
        "className": "delete",
        "style": "display: inline-block !important",
        "onclick": "window.user_tag_token.remove($(this).up('li').get('user_id')); this.up().remove();"
      });

      var li = DOM.li({
        "data-tag_item_id": guid,
        "id": "CTag-"+guid,
        "className": "tag"
      }, name, btn);

      $("user_tags").insert(li);
    }
  }

  function getAutocomplete () {
    var form = getForm("esLogSearch");
    var element = form.elements.user_id,
      tokenField = new TokenField(element, {onChange: function(){}.bind(element)});

    var element_input = form.elements.user_view;
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_input.name);
    url.addParam("edit", "1");
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
          insertTag(guid, _name);
        }

        var element_input = form.elements.user_view;
        $V(element_input, "");
      }
    });

    window.user_tag_token = tokenField;

  }
</script>

<form method="get" name="esLogSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayLogResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="accept_utf8" value="1">
  <table class="main layout">
    <tbody>
    <tr>
      <td id="td_container_search">
        <input type="search" id="words" name="words" value="" placeholder="Saisissez les termes de votre recherche ici..." style="width:50em; height:1.5em; font-size:medium;" onchange="$V(this.form.start, '0')" autofocus>
        <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
      </td>
    </tr>
    <tr>
      <td>
        <input type="checkbox" name="aggregate" id="aggregate" value="1" checked>
        <label for="aggregate"> Agrégation des résultats</label>
      </td>
    </tr>
    </tbody>
  </table>
  <table class="main layout">
    <tbody>
    <tr>
      <!-- Fieldset de tri par date -->
      <td>
        <fieldset>
          <legend>Intervalle de date </legend>
          {{*{{mb_include module=search template=inc_tooltip_date}}*}}
          <table>
            <tr>
              <td>
                <input type="hidden" class="date" id="_min_date" name="_min_date" onchange="$V(this.form.start, '0')" value="{{$date}}">
                <b>&raquo;</b>
                <input type="hidden" class="date" id="_max_date" name="_max_date" onchange="$V(this.form.start, '0')" >
                <strong>{{tr}}or{{/tr}}</strong>
                Jour seul : <input type="hidden" class="date" id="_date" name="_date" onchange="$V(this.form.start, '0')" >
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
      <!-- Fieldset de tri par Intervenants -->
      <td  style="width: 33%">
        <fieldset>
          <legend> Intervenants</legend>
          <table class="layout">
            <tr>
              <td>
                <input type="text" name="user_view" class="autocomplete" value="" placeholder="&mdash; Choisir un intervenant"/>
                <input type="hidden" name="user_id" value=""/>

                <button type="button" class="user notext" title="Mon compte"
                        onclick="window.user_tag_token.add('{{$app->user_id}}'); insertTag('{{$app->_ref_user->_guid}}', '{{$app->_ref_user}}')">
                </button>
                <button type="button" title="Effacer le champ" class="erase notext" onclick="$V(this.form.elements.user_id, '');
                          $V(this.form.elements.user_view, ''); $$('li.tag').each(function(elt) { elt.remove(); });">
                </button>
              </td>
            </tr>
            <tr>
              <td>
                <ul id="user_tags" class="tags" style="float: none;">
                  {{if $app->_ref_user->_is_praticien}}
                    <li data-tag_item_id="{{$app->_ref_user->_id}}" id="CTag-{{$app->_ref_user->_id}}" class="tag">
                      {{$app->_ref_user->_view}}
                      <button type="button" class="delete"
                              onclick="window.user_tag_token.remove($(this).up('li').get('user_id')); this.up().remove(); $V(this.form.elements.user_id, '');"
                              style="display: inline-block !important;"></button>
                    </li>
                  {{/if}}
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
              <td>
                <input type="checkbox" name="names_types[]" id="CConsultation" value="CConsultation">
                <label for="CConsultation"> Consultation</label>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <input type="checkbox" name="names_types[]" id="CConsultAnesth" value="CConsultAnesth">
                <label for="CConsultAnesth"> Consultation anesthésique</label>
              </td>
            </tr>

            <tr>
              <td class="halfPane">
                <input type="checkbox" name="names_types[]" id="CFile" value="CFile">
                <label for="CFile"> Fichier</label>
              </td>
              <td>
                <input type="checkbox" name="names_types[]" id="CObservationMedicale" value="CObservationMedicale">
                <label for="CObservationMedicale"> Observation Médicale</label>
              </td>
            </tr>
            <tr>
              <td>
                <input type="checkbox" name="names_types[]" id="CPrescription" value="CPrescriptionLineMix">
                <label for="CPrescription"> Prescription</label>
              </td>
              <td>
                <input type="checkbox" name="names_types[]" id="CTransmissionMedicale" value="CTransmissionMedicale">
                <label for="CTransmissionMedicale"> Transmission Médicale</label>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
      <!-- Fieldset de tri par Contexte -->
      <td>
        <fieldset>
          <legend>
            <input type="checkbox" name="SearchAllContextes" id="SearchAllContextes" value="SearchAllContextes" onclick="Search.checkAllCheckboxes(this, 'contextes[]')">
            <label for="SearchAllContextes">Contextes</label>
          </legend>
          <table class="layout" id="first_indexing">
            <tr>
              <td colspan="2">
                <input type="checkbox" name="contextes[]" id="generique" value="generique"/>
                <label for="generique">Générique</label>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input type="checkbox" name="contextes[]" id="pharmacie" value="pharmacie">
                <label for="pharmacie"> Pharmacie</label>
              </td>
            </tr>

            <tr>
              <td>
                <input type="checkbox" name="contextes[]" id="pmsi" value="pmsi">
                <label for="pmsi"> PMSI</label>
              </td>
              <td>
                <input type="checkbox" name="contextes[]" id="bloc" value="bloc">
                <label for="bloc"> Bloc</label>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    </tbody>
  </table>
  <div id="list_log_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>