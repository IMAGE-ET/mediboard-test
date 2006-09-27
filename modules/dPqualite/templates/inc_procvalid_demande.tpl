{{assign var="info_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y à %Hh%M"}}
<table class="form">
  <tr>          
    <th class="title" colspan="2" style="color: #f00;">              
      <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_VALID}}" />
      Procédure Demandée
    </th>
  </tr>
  <tr>
    <th>Date</th>
    <td>{{$docGed->_lastentry->date|date_format:"%A %d %B %Y à %Hh%M"}}</td>
  </tr>
  <tr>
    <th>Procédure Associée</th>
    <td>
      {{if $docGed->doc_ged_id && $docGed->_lastactif->doc_ged_suivi_id}}
      Révision de la procédure {{$docGed->_reference_doc}}<br />
      Thème : {{$docGed->_ref_theme->nom}}
      {{else}}
      Nouvelle Procédure
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>Etablissement</th>
    <td class="text">
      {{$docGed->_ref_group->text}}
    </td>
  </tr>
  <tr>
    <th>Formulée par</th>
    <td class="text">{{$docGed->_lastentry->_ref_user->_view}}</td>
  </tr>
  <tr>
    <th>Remarques</th>
    <td class="text">
      {{$docGed->_lastentry->remarques|nl2br}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="cancel" type="button" onclick="refuseDoc(this.form);">
        Refuser la demande 
      </button>
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
  {{if !$docGed->_lastactif->doc_ged_suivi_id}}            
  <tr>
    <th>
      <label for="ged[doc_chapitre_id]" title="Veuillez Sélectionner un chapitre de classement">
        Chapitre
      </label>
    </th>
    <td>
      <select name="ged[doc_chapitre_id]" title="{{$docGed->_props.doc_chapitre_id}}|notNull">
        <option value="">&mdash; Veuillez sélectionner un Chapitre &mdash;</option>
        {{foreach from=$listChapitres item=curr_chapitre}}
        <option value="{{$curr_chapitre->doc_chapitre_id}}" {{if $docGed->doc_chapitre_id == $curr_chapitre->doc_chapitre_id}} selected="selected" {{/if}} >
          {{$curr_chapitre->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  <tr>
    <th>
      <label for="ged[doc_categorie_id]" title="Veuillez Sélectionner une catégorie de classement">
        Catégorie
      </label>
    </th>
    <td>
      <select name="ged[doc_categorie_id]" title="{{$docGed->_props.doc_categorie_id}}|notNull">
        <option value="">&mdash; Veuillez sélectionner une Catégorie &mdash;</option>
        {{foreach from=$listCategories item=curr_category}}
        <option value="{{$curr_category->doc_categorie_id}}" {{if $docGed->doc_categorie_id == $curr_category->doc_categorie_id}} selected="selected" {{/if}} >
          {{$curr_category->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Remarques</label></th>
    <td>
      <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button class="tick" type="button" onclick="redactionDoc(this.form);">
        Accepter la demande 
      </button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'La procédure ',objName:'{{$info_proc|escape:javascript}}'})" title="Supprimer la Procédure">
        Supprimer
      </button>
    </td>
  </tr>
</table>