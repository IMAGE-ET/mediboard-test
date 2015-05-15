{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_default var=display_date value=true}}
{{mb_default var=display_user value=true}}
{{mb_default var=display_types value=true}}
{{mb_default var=display_contextes value=false}}
{{mb_default var=query value=true}}
{{mb_default var=expand value=true}}
{{mb_default var=submit value=false}}

<script>
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
</script>

<table id="list_fields_search" class="main layout">
  <tr>
    <td class="separator expand" onclick="Search.toggleColumn(this, this.next())"></td>
    <td {{if $expand}}style="display:none{{/if}}">
      <!-- Champs de tri de la Recherche -->
      <table class="main layout">
        <tbody>
        <tr>
            <td style="width: 33%">
              {{if $display_date}}
                <!-- Fieldset de tri par date -->
                <fieldset>
                  <legend>Intervalle de date </legend>
                  <table>
                    <tr>
                      <td>
                        <input type="hidden" class="date" id="_min_date" name="_min_date" onchange="$V(this.form.start, '0'); {{if $submit}}this.form.onsubmit();{{/if}}"/>
                        <b>&raquo;</b>
                        <input type="hidden" class="date" id="_max_date" name="_max_date" onchange="$V(this.form.start, '0') ; {{if $submit}}this.form.onsubmit();{{/if}}"/>
                        <strong>{{tr}}or{{/tr}}</strong>
                        Jour seul : <input type="hidden" class="date" id="_date" name="_date" onchange="$V(this.form.start, '0') ; {{if $submit}}this.form.onsubmit();{{/if}}"/>
                      </td>
                    </tr>
                  </table>
                </fieldset>
              {{/if}}

              {{if $display_contextes}}
              <!-- Fieldset de tri par Contextes -->
                <fieldset>
                  <legend>
                    <input type="checkbox" name="SearchAllContextes" id="SearchAllContextes" value="SearchAllContextes" onclick="Search.checkAllCheckboxes(this, 'contextes[]')">
                    <label for="SearchAllContextes">Contextes</label>
                  </legend>
                  <table class="layout">
                    <tr>
                      <td class="columns-3">
                      {{foreach from=$contextes item=_contexte}}
                        <input type="checkbox" name="contextes[]" id="{{$_contexte}}" value="{{$_contexte}}"/>
                        <label for="{{$_contexte}}">{{tr}}CSearchThesaurusEntry.contextes.{{$_contexte}}{{/tr}}</label>
                        <br/>
                      {{/foreach}}
                      </td>
                    </tr>
                  </table>
                </fieldset>
              {{/if}}
            </td>

          <!-- Fieldset de tri par Types -->
          {{if $display_types}}
            <td>
              <fieldset>
                <legend>
                  <input type="checkbox" name="searchAll" id="SearchAll" value="SearchAll" onclick="Search.checkAllCheckboxes(this, 'names_types[]')">
                  <label for="SearchAll">Types</label>
                </legend>
                <table class="layout" id="first_indexing">
                  <tr>
                    <td class="columns-2">
                      {{foreach from=$types item=_type}}
                        {{if $_type != "CPrescriptionLineMedicament" && $_type != "CPrescriptionLineMix" &&  $_type != "CPrescriptionLineElement"}}
                          <input type="checkbox" name="names_types[]" id="{{$_type}}" value="{{$_type}}"/>
                          <label for="{{$_type}}">{{tr}}{{$_type}}{{/tr}}</label>
                          <br/>
                        {{/if}}
                      {{/foreach}}
                      {{if array_intersect(array("CPrescriptionLineMedicament", "CPrescriptionLineMix","CPrescriptionLineElement"), $types)}}
                        <input type="checkbox" name="names_types[]" id="CPrescriptionLineMedicament" value="CPrescriptionLineMedicament"/>
                        <label for="precription">Prescription</label>
                        <br/>
                      {{/if}}
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          {{/if}}

          <!-- Fieldset de tri par Intervenants -->
          {{if $display_user}}
            <td style="width: 20%">
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
          {{/if}}

          <!-- Fieldset de requêtes complexes -->
          {{if $query}}
            <td style="width: 15%">
              <fieldset>
                <legend>{{tr}}CSearchThesaurusEntry-Pattern{{/tr}}</legend>
                <table>
                  <tr>
                    <td>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title and{{/tr}}" onclick="Thesaurus.addPatternToEntry('add', this.form)">{{tr}}CSearchThesaurusEntry-Pattern and{{/tr}}</button>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title or{{/tr}}" onclick="Thesaurus.addPatternToEntry('or', this.form)">{{tr}}CSearchThesaurusEntry-Pattern or{{/tr}}</button>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title not{{/tr}}" onclick="Thesaurus.addPatternToEntry('not', this.form)">{{tr}}CSearchThesaurusEntry-Pattern not{{/tr}}</button>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title like{{/tr}}" onclick="Thesaurus.addPatternToEntry('like', this.form)">{{tr}}CSearchThesaurusEntry-Pattern like{{/tr}}</button>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title obligation{{/tr}}" onclick="Thesaurus.addPatternToEntry('obligation', this.form)">{{tr}}CSearchThesaurusEntry-Pattern obligation{{/tr}}</button>
                      <button type="button" title="{{tr}}CSearchThesaurusEntry-Pattern-title prohibition{{/tr}}" onclick="Thesaurus.addPatternToEntry('prohibition', this.form)">{{tr}}CSearchThesaurusEntry-Pattern prohibition{{/tr}}</button>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          {{/if}}
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
</table>


