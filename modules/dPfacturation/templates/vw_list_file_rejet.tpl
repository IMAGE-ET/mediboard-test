{{if !$chir_id}}{{mb_return}}{{/if}}

{{if !$fs_source_reception->_id || !$fs_source_reception->active}}
  <div class="small-warning">
    <strong>Le dossier de dépôt n'est pas paramétré ou n'est pas actif pour ce praticien</strong>
  </div>
  {{mb_return}}
{{/if}}
{{if $erreur}}
  <div class="small-error">{{$erreur}}</div>
  {{mb_return}}
{{/if}}

<table class="tbl">
  {{if $count_files >= 1000}}
    <tr>
      <td>
        <div class="small-warning">
          <strong>Le dossier {{$fs_source_reception->host}} contient trop de fichiers ({{$count_files}}) pour être listé</strong>
        </div>
      </td>
    </tr>
  {{else}}
    <tr>
      <th colspan="2">
        Liste des fichiers du dossier ({{$count_files}})
        {{if $count_files && $count_files < 100}}
          <button type="button" class="copy" onclick="Rejet.traitementXML('{{$chir_id}}');" style="float: right;">Traiter</button>
        {{/if}}
      </th>
    </tr>
    {{foreach from=$files item=_file}}
      <tr>
        <td class="text">{{$_file}} </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty">Pas de fichiers dans le dossier paramétré</td>
      </tr>
    {{/foreach}}
  {{/if}}
</table>
