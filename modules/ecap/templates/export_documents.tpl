<!-- Filter -->
<form name="Filter" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="do" value="0" />
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">Export de documents vers le e-Cap</th>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="tick" type="submit" onclick="this.form.do.value = 'export';">
        Exporter les documents
      </button>
      <button class="tick" type="submit" onclick="this.form.do.value = 'test';">
        Tester le service web
      </button>
    </td>
  </tr>
</table>

</form>

{{if $do == "export"}}
{{include file=inc_list_export_documents.tpl}}
{{elseif $do == "test"}}

{{else}}
<div class="big-info">
  Il est nécessaire de valider l'export pour le réaliser.
  Merci de cliquer sur <strong>Exporter les documents</strong> après avoir choisi :
  <dl>
    <dt>soit une <em>date</em></dt>
    <dd>Pour exporter les dpcuments de tous les séjours ayant une sortie réelle ce jour.</dd>
    <dt>soit un <em>numéro de dossier</em></dt>
    <dd>Pour exporter les documents spécifiques à un séjour en particulier.</dd>
  </dl>
</div>
{{/if}}


<table class="tbl">
  <tr>
    <th class="title" colspan="10">
    {{$files|@count}} Fichiers
    {{if $files|@count != $files_count}}
   	sur {{$files_count}} trouvés
    {{/if}}
    </th>
  </tr>

  <tr>
    <th>{{mb_title class=CFile field=file_name}}</th>
    <th>{{mb_title class=CFile field=object_id}}</th>
    <th>Identifiant e-Cap</th>
  </tr>
  
  {{foreach from=$files item=_file}}
  <tr>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}')">
	      {{mb_value object=$_file field=file_name}}
      </span>
    </td>

    <td>
      {{assign var=object value=$_file->_ref_object}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
	     {{$object}}
      </span>
    </td>

    <td>
      {{assign var=id_ecap value=$_file->_ref_id_ecap}}
      {{if $id_ecap->_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$id_ecap->_guid}}')">
	     {{$id_ecap}}
      </span>
      
			{{else}}
			<form name="Export-{{$_file->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
			
			<input type="hidden" name="m" value="{{$m}}" />
			<input type="hidden" name="dialog" value="1" />
			<input type="hidden" name="dosql" value="do_export_document" />
			<input type="hidden" name="docitem_guid" value="{{$_file->_guid}}" />
			
			<button class="change">{{tr}}Export{{/tr}}</button>

			</form>
			{{/if}}
    </td>
  </tr>
  {{/foreach}}

</table>

