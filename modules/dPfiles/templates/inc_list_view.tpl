{{mb_script module=compteRendu script=document}}
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
  var tabs{{$object_class}}{{$object_id}} = Control.Tabs.create('tab-{{$object_class}}{{$object_id}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}
});
</script>

<ul id="tab-{{if $accordDossier}}{{$object_class}}{{$object_id}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=_cat key=_cat_id}}
  {{assign var=docCount value=$_cat.items|@count}}
  {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
    <li>
      <a href="#Category-{{$_cat_id}}" {{if !$docCount}}class="empty"{{/if}}>
        {{$_cat.name}} 
        <small>({{$docCount}})</small>
      </a>
    </li>
  {{/if}}
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$affichageFile item=_cat key=_cat_id}}
{{assign var=docCount value=$_cat.items|@count}}

{{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
<table class="tbl" id="Category-{{$_cat_id}}" style="display: none;">
  {{if $canFile->edit && !$accordDossier}}
  <tr>
    <td colspan="2" class="text">
      <button class="new" onclick="uploadFile('{{$object->_guid}}', '{{$_cat_id}}')">
        Ajouter un fichier
      </button>
    </td>
  </tr>
  {{/if}}
  <tbody id="Category-{{$_cat_id}}">
    {{mb_include module=files template=inc_list_files list=$_cat.items}}
  </tbody>
</table>
{{/if}}
{{/foreach}}