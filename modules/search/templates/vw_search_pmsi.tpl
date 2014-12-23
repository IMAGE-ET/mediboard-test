{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}

{{mb_script module="search" script="Search" ajax=true}}
{{mb_script module="search" script="Thesaurus" ajax=true}}
<script>
  Main.add(function () {
    var form = getForm("esSearch");
    Calendar.regField(form._min_date);
    Calendar.regField(form._max_date);
    Calendar.regField(form._date);

    Search.getAutocompleteUser(form);
    Thesaurus.getAutocompleteFavoris(form);

  });

  changePage = function(start) {
    var form = getForm("esSearch");
    $V(form.elements.start, start);
    form.onsubmit();
  };

  insertTag = function (guid, name) {
    var tag = $("CTag-" + guid);

    if (!tag) {
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
      }, name, btn);

      $("user_tags").insert(li);
    }
  }
</script>

<form method="get" name="esSearch" action="?m=search" class="watched prepared" onsubmit="return Search.displayResults(this);" onchange="onchange=$V(this.form, '0')">
  <input type="hidden" name="start" value="0">
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}"/>
  <input type="hidden" name="contexte" value="pmsi">
  <table class="main layout">
    <tbody>
    <tr>
      <td id="td_container_search" style="box-sizing : border-box; padding: 2em 6em 0.5em;">
        <input type="search" id="words" class="autocomplete" name="words" placeholder="Saisissez les termes de votre recherche ici..." style="width:50em; height:1.5em; font-size:medium;" onchange="$V(this.form.start, '0'); $V(this.form.words_favoris, this.value)" autofocus>
        <button class="favoris notext" type="button"
                onclick="
                    if($V(form.words)){
                    Thesaurus.addeditThesaurusEntry(form, null, function(){});
                    }
                    else {
                    Modal.alert('Pour ajouter un favoris il faut que votre recherche contienne au moins un mot.');
                    }
                    " title="{{tr}}CSearch-addToThesaurus{{/tr}}">

        </button>
        <input type="hidden" name="words_favoris"/>
        {{mb_include module=search template=inc_tooltip_help}}
    </tr>
    <tr>
      <td>
        <span class="circled">
          <input type="checkbox" name="aggregate" id="aggregate" value="1">
          <label for="aggregate"> Agrégation des résultats</label>
        </span>
        <span class="circled">
          <input type="checkbox" name="fuzzy" id="fuzzy" value="1">
          <label for="fuzzy">{{tr}}CSearch-Fuzzy Search{{/tr}}</label>
        </span>

      </td>
    </tr>
    </tbody>
  </table>
  <table class="main layout">
    <tbody>
    <tr>
      <!-- Fieldset de tri par date -->
      <td style="width:45%">
        <fieldset>
          <legend>Intervalle de date </legend>
          {{*{{mb_include module=search template=inc_tooltip_date}}*}}
          <table>
            <tr>
              <td>
                <input type="hidden" class="datetime" name="_min_date" onchange="$V(this.form.start, '0')" >
                <b>&raquo;</b>
                <input type="hidden" class="datetime" name="_max_date" onchange="$V(this.form.start, '0')" >
                <strong>{{tr}}or{{/tr}}</strong>
                Jour seul : <input type="hidden" class="datetime" name="_date" onchange="$V(this.form.start, '0')" >
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
      <!-- Fieldset de tri par Intervenants -->

      <td>
        <fieldset>
          <legend> Intervenants</legend>
          <table class="layout">
            <tr>
              <td>
                <input type="text" name="user_view" class="autocomplete" value="" placeholder="&mdash; Choisir un intervenant"/>
                <input type="hidden" name="user_id" value="" />

                <button type="button" class="user notext"
                        onclick="window.user_tag_token.add('{{$app->user_id}}'); insertTag('{{$app->_ref_user->_guid}}', '{{$app->_ref_user}}')">
                </button>
                <button type="button" class="erase notext" onclick="$V(this.form.elements.user_id, '');
                          $V(this.form.elements.user_view, ''); $$('li.tag').each(function(elt) { elt.remove(); });">
                </button>
                <ul id="user_tags" class="tags" style="display: inline-block;">
                </ul>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>

      <!-- Fieldset de tri par Types -->
      <td style="width:30%">
        <fieldset >
          <legend>
            <input type="checkbox" name="searchAll" id="SearchAll" value="SearchAll" onclick="Search.checkAllCheckboxes(this, 'names_types[]')">
            <label for="SearchAll">Types</label>
          </legend>
          <div class="columns-2">
            {{foreach from=$types item=_types}}
              <label>
                <input type="checkbox" name="names_types[]" id="{{$_types}}" value="{{$_types}}">
                {{tr}}{{$_types}}{{/tr}}
              </label>
              <br/>
            {{/foreach}}
          </div>
        </fieldset>
      </td>
    <tr>
      <td class="button" colspan="3">
        <button type="submit" id="button_search" class="button lookup">Démarrer la recherche</button>
      </td>
    </tr>
    </tbody>
  </table>
  <div id="list_result">
    <!-- Résultats de la Recherche -->
  </div>
</form>