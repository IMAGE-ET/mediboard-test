<table class="form">
  <tr>
    <th class="title" colspan="2">
      <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
      Validation du Document
    </th>
  </tr>
  <tr>
    <th>Procédure Associée</th>
    <td>{{$docGed->_reference_doc}}</td>
  </tr>
  <tr>
    <th>Proposé par</th>
    <td class="text">{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  <tr>
    <th>Le</th>
    <td>{{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}</td>
  </tr>
  <tr>
    <th>Remarques</th>
    <td class="text">{{$docGed->_lastentry->remarques|nl2br}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
        <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
      </a>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[titre]" title="Veuillez saisir un titre pour cette procédure">
        Titre
      </label>
    </th>
    <td>
      <input type="text" name="ged[titre]" value="{{$docGed->titre}}" title="{{$docGed->_props.titre}}|notNull" />
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_theme_id]" title="Veuillez Sélectionner un thème de classement">
        Thème
      </label>
    </th>
    <td>
      <select name="ged[doc_theme_id]" title="{{$docGed->_props.doc_theme_id}}|notNull">
        <option value="">&mdash; Veuillez sélectionner un Thème &mdash;</option>
        {{foreach from=$listThemes item=curr_theme}}
        <option value="{{$curr_theme->doc_theme_id}}" {{if $docGed->doc_theme_id == $curr_theme->doc_theme_id}} selected="selected" {{/if}} >
          {{$curr_theme->nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Vos Remarques</label></th>
    <td>
      <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
    </td>
  </tr>
  {{if $docGed->version}}
  <tr>
    <th><label for="ged[version]">Valider pour la version</label></th>
    <td>
      <select name="ged[version]" title="currency|notNull">
        {{foreach from=$versionDoc|smarty:nodefaults item=currVersion}}
        <option value="{{$currVersion}}">{{$currVersion}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{else}}
  <input type="hidden" name="ged[version]" value="1">
  {{/if}}
  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="validDoc(this.form);">
        Valider le document 
      </button>
      <button class="cancel" type="button" onclick="redactionDoc(this.form);">
        Renvoyer le document
      </button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'La procédure ',objName:'{{$docGed->_reference_doc|smarty:nodefaults|JSAttribute}}'})" title="Supprimer la Procédure">
        Supprimer
      </button>
    </td>
  </tr>
</table>