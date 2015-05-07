{{*
 * $Id$
 *  
 * @category Modèles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  var classes = {{$classes|@json}};

  var aTraduction = {};
  {{foreach from=$listTraductions key=key item=currClass}}
  aTraduction["{{$key}}"] = "{{$currClass|smarty:nodefaults}}";
  {{/foreach}}

  loadClasses = function(value) {
    var form = getForm("editFrm");
    var select = form.elements['class'];
    var options = classes;

    // delete all former options except first
    while (select.length > 1) {
      select.options[1] = null;
    }

    // insert new ones
    for (var elm in options) {
      var option = elm;
      if (typeof(options[option]) != "function") { // to filter prototype functions
        select.options[select.length] = new Option(aTraduction[option], option);
      }
    }

    $V(select, value);
    loadFields();
  };

  loadFields = function(value) {
    var form = getForm("editFrm");
    var select = form.elements['field'];
    var className  = form.elements['class'].value;
    var options = classes[className];

    // delete all former options except first
    while (select.length > 1) {
      select.options[1] = null;
    }

    // insert new ones
    for (var elm in options) {
      var option = elm;
      if (typeof(options[option]) != "function") { // to filter prototype functions
        select.options[select.length] = new Option($T(className+"-"+option), option);
      }
    }

    $V(select, value);
    loadDependances();
  };

  loadDependances = function(depend_value_1, depend_value_2) {
    var form = document.editFrm;
    var select_depend_1 = form.elements['depend_value_1'];
    var select_depend_2 = form.elements['depend_value_2'];
    var className  = form.elements['class'].value;
    var fieldName  = form.elements['field'].value;
    var options = classes[className];

    // delete all former options except first
    {{if !$aide->_is_ref_dp_1}}
    while (select_depend_1.length > 1) {
      select_depend_1.options[1] = null;
    }
    {{/if}}
    {{if !$aide->_is_ref_dp_2}}
    while (select_depend_2.length > 1) {
      select_depend_2.options[1] = null;
    }
    {{/if}}

    if (!options || !classes[className][fieldName]) {
      return;
    }

    {{if !$aide->_is_ref_dp_1}}
    // Depend value 1
    options_depend_1 = classes[className][fieldName]['depend_value_1'];
    for (var elm in options_depend_1) {
      var option = options_depend_1[elm];
      if (typeof(option) != "function") { // to filter prototype functions
        select_depend_1.options[select_depend_1.length] = new Option(aTraduction[option], elm, elm == depend_value_1);
      }
    }
    $V(select_depend_1, '{{$aide->depend_value_1}}');
    {{/if}}

    {{if !$aide->_is_ref_dp_2}}
    // Depend value 2
    options_depend_2 = classes[className][fieldName]['depend_value_2'];
    for (var elm in options_depend_2) {
      var option = options_depend_2[elm];
      if (typeof(option) != "function") { // to filter prototype functions
        select_depend_2.options[select_depend_2.length] = new Option(aTraduction[option], elm, elm == depend_value_2);
      }
    }
    $V(select_depend_2, '{{$aide->depend_value_2}}');
    {{/if}}
  };

  Main.add(function() {
    loadClasses('{{$aide->class}}');
    loadFields('{{$aide->field}}');
    loadDependances('{{$aide->depend_value_1}}', '{{$aide->depend_value_2}}');
    HyperTextLink.getListFor('{{$aide->_id}}', '{{$aide->_class}}');

    var form = getForm("editFrm");

    var urlUsers = new Url("mediusers", "ajax_users_autocomplete");
    urlUsers.addParam("edit", "1");
    urlUsers.addParam("input_field", "user_id_view");
    urlUsers.autoComplete(form.user_id_view, null, {
      minChars: 0,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.getAttribute("id").split("-")[2];
        $V(form.user_id, id);
      }
    });

    {{if $access_function}}
      var urlFunctions = new Url("mediusers", "ajax_functions_autocomplete");
      urlFunctions.addParam("edit", "1");
      urlFunctions.addParam("input_field", "function_id_view");
      urlFunctions.addParam("view_field", "text");
      urlFunctions.autoComplete(form.function_id_view, null, {
        minChars: 0,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field, selected) {
          var id = selected.getAttribute("id").split("-")[2];
          $V(form.function_id, id);
        }
      });
    {{/if}}

    {{if $access_group}}
      var urlGroups = new Url("etablissement", "ajax_groups_autocomplete");
      urlGroups.addParam("edit", "1");
      urlGroups.addParam("input_field", "group_id_view");
      urlGroups.addParam("view_field", "text");
      urlGroups.autoComplete(form.group_id_view, null, {
        minChars: 0,
        method: "get",
        select: "view",
        dropdown: true,
        afterUpdateElement: function(field, selected) {
          var id = selected.getAttribute("id").split("-")[2];
          $V(form.group_id, id);
        }
      });
    {{/if}}
  });
</script>

<form name="editFrm" method="post" onsubmit="return onSubmitFormAjax(this, function() {
      Control.Modal.close(); Aide.loadTabsAides(getForm('filterFrm'));
      })">
  {{mb_class object=$aide}}
  {{mb_key   object=$aide}}
  <input type="hidden" name="del" value="0" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$aide}}

    <tr>
      <th>{{mb_label object=$aide field="user_id"}}</th>
      <td>
        {{mb_field object=$aide field=user_id hidden=1
        onchange="
             \$V(this.form.function_id, '', false);
             if (this.form.function_id_view) {
               \$V(this.form.function_id_view, '', false);
             }
             \$V(this.form.group_id, '', false);
             if (this.form.group_id_view) {
               \$V(this.form.group_id_view, '', false);
             }"}}
        <input type="text" name="user_id_view" value="{{$aide->_ref_user}}" />
      </td>
    </tr>

    {{if $access_function}}
      <tr>
        <th>{{mb_label object=$aide field="function_id"}}</th>
        <td>
          {{mb_field object=$aide field=function_id hidden=1
          onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.group_id, '', false);
             if (this.form.group_id_view) {
               \$V(this.form.group_id_view, '', false);
             }"}}
          <input type="text" name="function_id_view" value="{{$aide->_ref_function}}" />
        </td>
      </tr>
    {{/if}}

    {{if $access_group}}
      <tr>
        <th>{{mb_label object=$aide field="group_id"}}</th>
        <td>
          {{mb_field object=$aide field=group_id hidden=1
          onchange="
             \$V(this.form.user_id, '', false);
             \$V(this.form.user_id_view, '', false);
             \$V(this.form.function_id, '', false);
             if (this.form.function_id_view) {
               \$V(this.form.function_id_view, '', false);
             }"}}
          <input type="text" name="group_id_view" value="{{$aide->_ref_group}}" />
        </td>
      </tr>
    {{/if}}

    <tr>
      <th>{{mb_label object=$aide field="class"}}</th>
      <td>
        <select name="class" class="{{$aide->_props.class}}" onchange="loadFields()" style="width: 12em;">
          <option value="">&mdash; Choisir un type objet</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="field"}}</th>
      <td>
        <select name="field" class="{{$aide->_props.field}}" onchange="loadDependances()" style="width: 12em;">
          <option value="">&mdash; Choisir un champ</option>
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="depend_value_1"}}</th>
      <td>
        {{if $aide->_is_ref_dp_1}}
          {{mb_field object=$aide field="depend_value_1" hidden=true}}
          <input type="hidden" name="_ref_class_depend_value_1" value="{{$aide->_class_dp_1}}" />
          <input type="text" name="_depend_value_2_view" value="{{$aide->_vw_depend_field_1}}" />
          <script>
            Main.add(function(){
              var form = getForm("editFrm");

              var url = new Url("system", "ajax_seek_autocomplete");
              url.addParam("object_class", $V(form._ref_class_depend_value_1));
              url.addParam("field", "depend_value_1");
              url.addParam("input_field", "_depend_value_1_view");
              url.addParam("show_view", "true");
              url.autoComplete(form.elements._depend_value_1_view, null, {
                minChars: 3,
                method: "get",
                select: "view",
                dropdown: true,
                afterUpdateElement: function(field, selected){
                  $V(field.form.elements.depend_value_1, selected.get("id"));
                }
              });
            });
          </script>
        {{else}}
          <select name="depend_value_1" class="{{$aide->_props.depend_value_1}}">
            <option value="">&mdash; Tous</option>
          </select>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="depend_value_2"}}</th>
      <td>
        {{if $aide->_is_ref_dp_2}}
          {{mb_field object=$aide field="depend_value_2" hidden=true}}
          <input type="hidden" name="_ref_class_depend_value_2" value="{{$aide->_class_dp_2}}" />
          <input type="text" name="_depend_value_2_view" value="{{$aide->_vw_depend_field_2}}" />
          <script>
            Main.add(function(){
              var form = getForm("editFrm");

              var url = new Url("system", "ajax_seek_autocomplete");
              url.addParam("object_class", $V(form._ref_class_depend_value_2));
              url.addParam("field", "depend_value_2");
              url.addParam("input_field", "_depend_value_2_view");
              url.addParam("show_view", "true");
              url.autoComplete(form.elements._depend_value_2_view, null, {
                minChars: 3,
                method: "get",
                select: "view",
                dropdown: true,
                afterUpdateElement: function(field, selected){
                  $V(field.form.elements.depend_value_2, selected.get("id"));
                }
              });
            });
          </script>
        {{else}}
          <select name="depend_value_2" class="{{$aide->_props.depend_value_2}}">
            <option value="">&mdash; Tous</option>
          </select>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="name"}}</th>
      <td>{{mb_field object=$aide field="name"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$aide field="text"}}</th>
      <td>{{mb_field object=$aide field="text"}}</td>
    </tr>

    <tr>
      <th>{{tr}}CHyperTextLink{{/tr}}</th>
      <td id="list-hypertext_links"></td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $aide->aide_id}}
          <button class="modify" type="button" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button"
                  onclick="confirmDeletion(this.form,{typeName:'l\'aide',objName:'{{$aide->name|smarty:nodefaults|JSAttribute}}'}, function() {
                    Control.Modal.close(); Aide.loadTabsAides(getForm('filterFrm'));
                    })">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="button" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>