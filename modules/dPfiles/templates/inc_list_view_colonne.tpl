{{*
  * List docitems by category with thumbnail
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
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

<script type="text/javascript">
Main.add(function () {
  {{if $accordDossier}}
  var tabs{{$object_id}}{{$object_class}} = Control.Tabs.create('tab-{{$object_class}}{{$object_id}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}

  if ($("docItem_{{$object->_guid}}")) {
    $("docItem_{{$object->_guid}}").update({{$nbItems}});
  }
});

</script>

<ul id="tab-{{if $accordDossier}}{{$object_class}}{{$object_id}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=_cat key=_cat_id}}
  {{assign var=docCount value=$_cat.items|@count}}
  {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
    <li>
      <a href="#Category-{{$_cat_id}}" {{if !$docCount}}class="empty"{{/if}} id="tab_category_{{$_cat_id}}">
        {{$_cat.name}}
        <small>({{$docCount}})</small>
      </a>
    </li>
  {{/if}}
{{/foreach}}
</ul>

<hr class="control_tabs" />

{{mb_include module=files template=inc_files_add_toolbar}}

{{foreach from=$affichageFile item=_cat key=_cat_id}}
  {{assign var=docCount value=$_cat.items|@count}}
  {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
    <div id="Category-{{$_cat_id}}" style="display: none; clear: both;">
      {{mb_include module=files template=inc_list_files_colonne list=$_cat.items}}
    </div>
  {{/if}}
{{/foreach}}
<hr style="clear: both;" />