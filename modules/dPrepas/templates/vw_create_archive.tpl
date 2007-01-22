<form name="FrmSelectArchive" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="dPrepas" />
<input type="hidden" name="dosql" value="repas_offline" />
<table class="form">
  <tr>
    <th>
      <label for="indexFile_1">Fichier index.html</label>
    </th>
    <td>
      <input type="radio" name="indexFile" value="1" checked="checked" /> Oui
      <input type="radio" name="indexFile" value="0" /> Non
    </td>
    <th>
      <label for="style_1">Fichier Style</label>
    </th>
    <td>
      <input type="radio" name="style" value="1" checked="checked" /> Oui
      <input type="radio" name="style" value="0" /> Non
    </td>
  </tr>
  <tr>
    <th>
      <label for="image_1">Dossier images</label>
    </th>
    <td>
      <input type="radio" name="image" value="1" checked="checked" /> Oui
      <input type="radio" name="image" value="0" /> Non
    </td>  
    <th>
      <label for="javascript_1">Fichiers Javascripts</label>
    </th>
    <td>
      <input type="radio" name="javascript" value="1" checked="checked" /> Oui
      <input type="radio" name="javascript" value="0" /> Non
    </td>
  </tr>
  <tr>
    <th>
      <label for="lib_1">Libairies</label>
    </th>
    <td>
      <input type="radio" name="lib" value="1" checked="checked" /> Oui
      <input type="radio" name="lib" value="0" /> Non
    </td> 
    <td class="button" colspan="2">
      <button class="submit" type="button" onclick="submitFormAjax(this.form, 'createArchive');">Créer</button>
    </td>
  </tr>
</table>
<div id="createArchive"></div>
</form>