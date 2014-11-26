{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    window.calendar_planning_fin = Calendar.regField(form._min_date);
    window.calendar_planning_fin = Calendar.regField(form._max_date);
    window.calendar_planning_fin = Calendar.regField(form._date);
    getAutocomplete();
    getAutocompleteFavoris();
  });

  function changePage(start) {
    var form = getForm("esSearch");
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
    var form = getForm("esSearch");
    var element = form.elements.user_id,
      tokenField = new TokenField(element, {onChange: function(){}.bind(element)});

    var element_input = form.elements.user_view;
    var url = new Url("mediusers", "ajax_users_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element_input.name);
    url.addParam("edit", "1");
    url.addParam("praticiens", "1");
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

  function getAutocompleteFavoris() {
    var form = getForm("esSearch");
    var element = form.elements.words_favoris,
      tokenWords = new TokenField(element, {
        onChange: function () {
        }.bind(element)
      });

    var element_input = form.elements.words;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CSearchThesaurusEntry");
    url.addParam("input_field", element_input.name);
    url.addParam("where[user_id]", '{{$app->_ref_user->_id}}');
    url.addParam("where[contextes]", $V(form.elements.contexte));
    url.autoComplete(element_input, null, {
      minChars: 2,
      method: "get",
      dropdown: true,
      updateElement: function (selected) {
        var _name = selected.down("span", "1").getText();
        var element_input = form.elements.words;
        $V(element_input, _name);
        $V(form.elements.aggregate, selected.down().get("aggregation"));
        var types = selected.down().get("types").split("|");
        $V(form.elements["names_types[]"], types);
      }
    });

    window.words_tag_token = tokenWords;
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="accept_utf8" value="1">
  <input type="hidden" name="contexte" value="generique">
  <table class="main layout">
    <tbody>
      <tr>
        <td id="td_container_search">
          <input type="search" id="words" name="words" value=""  class="autocomplete" placeholder="Saisissez les termes de votre recherche ici..." style="width:50em; font-size:medium;" onchange="$V(this.form.start, '0'); $V(this.form.words_favoris, this.value)" autofocus>
          <input type="hidden" name="words_favoris"/>

          {{mb_include module=search template=inc_tooltip_help}}
          <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
          <button class="add" type="button"
                  onclick="
                    if($V(form.words)){
                    Search.addeditThesaurusEntry(
                    this.form.aggregate.value,
                    $V(form.words),
                    '{{$app->user_id}}',
                    $V(form.elements['names_types[]']),
                    $V(form.contexte),
                    null);
                    }
                    else {
                    Modal.alert('Pour ajouter un favoris il faut que votre recherche contienne au moins un mot.');
                    }
                    ">{{tr}}CSearch-addToThesaurus{{/tr}}</button>
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
                  <input type="hidden" name="user_id" {{if $app->_ref_user->isPraticien()}}value="{{$app->user_id}}"{{/if}} />

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
              {{foreach from=$types item=_types}}
                <tr>
                  <td>
                    <input type="checkbox" name="names_types[]" id="{{$_types}}" value="{{$_types}}">
                    <label for="{{$_types}}">{{tr}}{{$_types}}{{/tr}}</label>
                  </td>
                </tr>
              {{/foreach}}
            </table>
          </fieldset>
        </td>
      </tr>
    </tbody>
  </table>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>