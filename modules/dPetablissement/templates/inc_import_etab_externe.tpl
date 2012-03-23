<table class="main form">
  <tr>
    <td>
      <div class="big-info">
        Zip contenant des fichiers CSV avec première ligne correspondant aux titres. <br />
        Valeurs des colonnes:
        <ul>
          <li>FINESS</li>
          <li>SIRET</li>
          <li>APE</li>
          <li>Nom / Raison sociale</li>
          <li>Adresse</li>
          <li>Complément adresse 1</li>
          <li>Complément adresse 2</li>
          <li>Code postal</li>
          <li>Ville</li>
          <li>Tel</li>
          <li>Fax</li>
        </ul>
      </div>
      
      <form name="upload_form" action="?" enctype="multipart/form-data" method="post" target="upload_iframe" onsubmit="if (checkForm(this)) this.submit()">
        <input type="hidden" name="m" value="dPetablissement" />
        <input type="hidden" name="dosql" value="do_import_etab_externe" />
        <input type="hidden" name="MAX_FILE_SIZE"  value="67108864" /><!-- 64MB -->
        
        <input type="file" name="import" class="notNull" size="60" />
        <button type="button" class="tick" onclick="this.form.onsubmit()">Importer le fichier CSV</button>
      </form>
    </td>
    <td>
      <iframe id="upload_iframe" name="upload_iframe" src="about:blank" style="display: none;"></iframe>
    </td>
  </tr>
</table>
