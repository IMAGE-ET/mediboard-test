<table class="form">
  <tr>
    <th class="title" colspan="2">              
      <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
      Procédure Terminée
    </th>
  </tr>
  <tr>
    <th>Procédure Associée</th>
    <td>
      {{if $docGed->_lastactif->doc_ged_suivi_id}}
      {{$docGed->_reference_doc}}<br />
      Version : {{$docGed->version}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>Theme</th>
    <td class="text">
      {{$docGed->_ref_theme->nom}}
    </td>
  </tr>
  <tr>
    <th>Etablissement</th>
    <td class="text">
      {{$docGed->_ref_group->text}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <a href="javascript:popFile({{$docGed->_lastactif->file_id}})" title="Voir le Fichier">
        <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastactif->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      {{if $docGed->annule}}
      <button class="change" type="button" onclick="annuleDoc(this.form,0);">
        Rétablir
      </button>
      {{else}}
        <button class="cancel" type="button" onclick="annuleDoc(this.form,1);">
          Annuler
        </button>
      {{/if}}
    </td>
  </tr>
</table>