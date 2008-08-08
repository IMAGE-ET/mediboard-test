{{if $canFile->edit && !$accordDossier}}
<table>
  <tr>
    <td class="button">
     <button class="new" type="button" onclick="uploadFile('{{$selClass}}', '{{$selKey}}')">
       {{tr}}CFile-title-create{{/tr}}
     </button>
    </td>
  </tr>
</table>
{{/if}}
