{{mb_script module=etablissement script=Group}}

<table class="main layout">
  <tr>
    <td style="width: 50%">
      <fieldset>
        <legend>1. Téléversement</legend>
        <iframe name="upload-import-file" id="upload-import-file" style="width: 1px; height: 1px;"></iframe>

        <form method="post" name="upload-import-file-form" enctype="multipart/form-data" target="upload-import-file">
          <input type="hidden" name="m" value="etablissement" />
          <input type="hidden" name="dosql" value="do_upload_import_group" />

          <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
          <input type="file" name="import" style="width: 400px;" onchange="Group.uploadReset()" />

          <button type="submit" class="submit">{{tr}}Upload{{/tr}}</button>
          <span class="upload-ok" style="display: none;">
            <img src="./images/icons/tick.png" />
            Le fichier est prêt à être importé
          </span>
          <span class="upload-error" style="display: none;">
            <img src="./images/icons/cancel.png" />
            Le fichier n'est pas valide, ce doit être un fichier XML exporté depuis Mediboard
          <span>
        </form>
      </fieldset>

      <div id="import-steps"></div>
    </td>
    <td>
      <fieldset>
        <legend>Rapport d'importation</legend>
        <div id="group-import-report"><span class="empty">Aucune importation réalisée</span></div>
      </fieldset>
    </td>
  </tr>
</table>
