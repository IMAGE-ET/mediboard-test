{{*
  * List docitems by category with thumbnail
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id$
  * @link     http://www.mediboard.org
*}}

{{mb_script module=compteRendu script=modele_selector}}
{{if $object}}
  {{assign var=object_class value=$object->_class}}
  {{assign var=object_id value=$object->_id}}
{{else}}
  {{assign var=object_class value=""}}
  {{assign var=object_id value=""}}
{{/if}}

<script>
  Main.add(function () {
    {{if $accordDossier}}
    var tabs{{$object_id}}{{$object_class}} = Control.Tabs.create('tab-{{$object_class}}{{$object_id}}', false, {
      afterChange: function (container) {
        var div_button = $("button_toolbar");
        switch (container.id) {
          case "Category-dmp":
            div_button.hide();
            break;
          default:
            div_button.show();
        }
      }
    });
    {{else}}
    var tabs = Control.Tabs.create('tab-consult', true, {
      afterChange: function(container) {
        var div_button = $("button_toolbar");
        switch (container.id) {
          case "Category-dmp":
            div_button.hide();
            break;
          default:
            div_button.show();
        }
      }
    });
    {{/if}}

    if ($("docItem_{{$object->_guid}}")) {
      $("docItem_{{$object->_guid}}").update({{$nbItems}});
    }

    ObjectTooltip.modes.locker = {
      module: "compteRendu",
      action: "ajax_show_locker",
      sClass: "tooltip"
    };
  });
</script>

<ul id="tab-{{if $accordDossier}}{{$object_class}}{{$object_id}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=_cat key=_cat_id}}

  {{assign var=docCount value=0}}
  {{assign var=docCountCancelled value=0}}
  {{foreach from=$_cat.items item=_docitem}}
    {{if !$_docitem->annule}}
      {{math equation=x+1 x=$docCount assign=docCount}}
    {{else}}
      {{math equation=x+1 x=$docCountCancelled assign=docCountCancelled}}
    {{/if}}
  {{/foreach}}
  {{if $docCount || $docCountCancelled || $conf.dPfiles.CFilesCategory.show_empty}}
    <li>
      <a href="#Category-{{$_cat_id}}" {{if !$docCount}}class="empty"{{/if}} id="tab_category_{{$_cat_id}}">
        {{$_cat.name}}
        <small>({{$docCount}})</small>
      </a>
    </li>
  {{/if}}
{{/foreach}}
{{if "dmp"|module_active}}
  <li>
    <a href="#Category-dmp">
      DMP
    </a>
  </li>
{{/if}}
</ul>

{{mb_include module=files template=inc_files_add_toolbar mozaic=1}}

{{foreach from=$affichageFile item=_cat key=_cat_id}}
  {{assign var=docCount value=$_cat.items|@count}}
  {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
    <div id="Category-{{$_cat_id}}" style="display: none; clear: both;">
      {{mb_include module=files template=inc_list_files_colonne list=$_cat.items}}
    </div>
  {{/if}}
{{/foreach}}

{{if "dmp"|module_active}}
  {{if "mbHost"|module_active}}
    {{mb_script module="dmp" script="cdmp"}}
    <div id="Category-dmp" style="display: none; clear: both;">
      {{mb_include module="dmp" template="inc_default_action_document" consultation=true}}
      <div id="result_consultation">
        <script>
          Main.add(function(){
            Cdmp.actionConsultation();
          });
        </script>
      </div>
      <button id="activities_send" type="button" class="save" onclick="getForm('activity_choose').onsubmit()" style="display: none">
        {{tr}}Save{{/tr}}
      </button>
    </div>
  {{/if}}
{{/if}}
<hr style="clear: both;" />