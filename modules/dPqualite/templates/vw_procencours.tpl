<script language="Javascript" type="text/javascript">

function popFile(file_id){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(file_id, 0);
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id=0">
        Créer une nouvelle Procédure
      </a>
      
      {{if $procTermine|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="5">
            Informations
          </th>
        </tr>
        <tr>
          <th class="category">Date</th>
          <th class="category">Demande formulée</th>
          <th class="category">Conclusion</th>
          <th class="category">Remarques</th>
          <th class="category"></th>
        </tr>
        {{foreach from=$procTermine item=currProc}}
        <tr>
          <td class="text">
            {{$currProc->_lastentry->date|date_format:"%d %b %Y à %Hh%M"}}
          </td>
          <td class="text">
            {{if $currProc->_lastactif->doc_ged_suivi_id}}
            Révision de la procédure {{$currProc->_reference_doc}}
            {{else}}
            Nouvelle Procédure
            {{/if}}
          </td>
          
          {{if $currProc->_lastactif->doc_ged_suivi_id && $currProc->_lastactif->doc_ged_suivi_id>$currProc->_firstentry->doc_ged_suivi_id}} 
          <td class="text">
            <strong>Accepté</strong>
          {{else}}
          <td class="text" style="color: #f00;">
            <strong>Refusé</strong>
          {{/if}}
          </td>
          <td class="text">
            {{$currProc->_lastentry->remarques|nl2br}}
          </td>
          <td class="text">
            <form name="ProcInfos{{$currProc->doc_ged_id}}Frm" action="?m={{$m}}" method="post">
            <input type="hidden" name="dosql" value="do_docged_aed" />
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />  
            <input type="hidden" name="ged[user_id]" value="" />  
            <input type="hidden" name="_validation" value="1" />
            {{if $currProc->_lastactif->doc_ged_suivi_id && $currProc->_lastactif->doc_ged_suivi_id>$currProc->_firstentry->doc_ged_suivi_id}} 
            <input type="hidden" name="del" value="0" />  
            <button type="submit" class="tick">
              OK
            </button>
            {{else}}
            <input type="hidden" name="del" value="1" />  
            <button type="submit" class="trash">
              OK
            </button>
            {{/if}}            
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />
      {{/if}}
      
      
      {{if $procDemande|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Prodécure en cours de demande
          </th>
        </tr>
        <tr>
          <th class="category">Demande formulée</th>
          <th class="category">Etablissement</th>
          <th class="category">Date</th>
          <th class="category">Remarques</th>
        </tr>
        {{foreach from=$procDemande item=currProc}}
        <tr>
          <td class="text">
            <form name="ProcDem{{$currProc->doc_ged_id}}Frm" action="?m={{$m}}" method="post">
            <input type="hidden" name="dosql" value="do_docged_aed" />
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />
            <input type="hidden" name="del" value="0" />
            {{assign var="date_proc" value=$currProc->_lastentry->date|date_format:"%d %b %Y à %Hh%M"}}
            <a class="buttonedit notext" href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}"></a>            {{if $currProc->_lastactif->doc_ged_suivi_id}}
              Révision de la procédure {{$currProc->_reference_doc}}
            {{else}}
              Nouvelle Procédure
            {{/if}}
            </form>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">
            {{$currProc->_lastentry->date|date_format:"%A %d %B %Y à %Hh%M"}}
          </td>
          <td class="text">
            {{$currProc->_lastentry->remarques|nl2br}}
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />      
      {{/if}}
      
      <table class="form">
        <tr>
          <th class="category" colspan="5">Procédure En attente</th>
        </tr>
        <tr>
          <th class="category">Titre</th>
          <th class="category">Référence</th>
          <th class="category">Etablissement</th>
          <th class="category">Thème</th>
          <th class="category">Etat</th>
        </tr>
        {{foreach from=$procEnCours item=currProc}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_reference_doc}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">{{$currProc->_ref_theme->nom}}</td>
          <td>{{$currProc->_etat_actuel}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="5">
            Aucune Procédure En Cours
          </td>
        </tr>
        {{/foreach}}
      </table>

    </td>
    <td class="halfPane">      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_docged_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />      
      
      <input type="hidden" name="ged[doc_ged_id]" value="{{$docGed->doc_ged_id}}" />  
      <input type="hidden" name="ged[user_id]" value="{{$user_id}}" />
      <input type="hidden" name="ged[annule]" value="{{$docGed->annule}}" />
            
      <input type="hidden" name="suivi[user_id]" value="{{$user_id}}" />  
      <input type="hidden" name="suivi[actif]" value="0" /> 
      <input type="hidden" name="suivi[file_id]" value="" />    
      
      <table class="form">
        <tr>          
          {{if $docGed->doc_ged_id && ($docGed->etat==CDOC_DEMANDE || $docGed->etat==CDOC_TERMINE)}}
            <th class="title" colspan="2" style="color: #f00;">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              {{if $docGed->etat==CDOC_TERMINE}}
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              Demande de modification d'une Procédure
              {{else}}
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="{{$docGed->_lastentry->doc_ged_suivi_id}}" />              
              Modification d'une demande de Procédure
              {{/if}}
            </th>
          {{elseif $docGed->doc_ged_id && $docGed->etat==CDOC_REDAC}}
            <th class="title" colspan="2">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_VALID}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_REDAC}}" />
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              Rédaction d'un document
            </th>
          {{elseif $docGed->doc_ged_id}}
            <th class="title" colspan="2" style="color: #f00;">
              Validation en cours
            </th>
          {{else}}
            <th class="title" colspan="2">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              Création d'une Procédure
            </th>
          {{/if}}                    
        </tr>       
        {{if $docGed->etat==CDOC_TERMINE || $docGed->etat==CDOC_DEMANDE || !$docGed->doc_ged_id}}
          {{if $docGed->doc_ged_id && $docGed->etat!=CDOC_TERMINE}}
          <tr>
            <th>Date</th>
            <td>{{$docGed->_lastentry->date|date_format:"%A %d %B %Y à %Hh%M"}}</td>
          </tr>
          {{/if}}
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
            <th>
              <label for="ged[group_id]" title="Etablissement où sera appliqué la procédure">Etablissement</label>
            </th>
            <td colspan="2">
              <select title="{{$docGed->_props.group_id}}" name="ged[group_id]">
              {{foreach from=$etablissements item=curr_etab}}
                <option value="{{$curr_etab->group_id}}" {{if ($docGed->doc_ged_id && $docGed->group_id==$curr_etab->group_id) || (!$docGed->doc_ged_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
              {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Remarques</label></th>
            <td>
              <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}|notNull">{{$docGed->_lastentry->remarques}}</textarea>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              {{if $docGed->doc_ged_id && $docGed->etat!=CDOC_TERMINE}}
              <button class="modify" type="submit">
                Modifier
              </button>
              {{assign var="date_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y à %Hh%M"}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la demande de procédure du ',objName:'{{$date_proc|escape:javascript}}'})" title="Supprimer cette demande de Procédure">
                Supprimer
              </button>              
              {{else}}
              <button class="modify" type="submit">
                Créer
              </button>              
              {{/if}}
            </td>
          </tr>
        {{elseif $docGed->etat==CDOC_REDAC}}
          <tr>
            <th>Procédure</th>
            <td>
              {{$docGed->_reference_doc}}
              <input type="hidden" name="file_class" value="CDocGed" />
              <input type="hidden" name="file_object_id" value="{{$docGed->doc_ged_id}}" />
              <input type="hidden" name="file_category_id" value="" />
            </td>
          </tr>
          <tr>
            <th>Titre</th>
            <td>{{$docGed->titre}}</td>
          </tr>
          <tr>
            <th>Visé par</th>
            <td class="text">{{$docGed->_lastentry->_ref_user->_view}} ({{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}})</td>
          </tr>
          {{if $docGed->_lastentry->file_id}}
          <tr>
            <th>Dernier Fichier lié</th>
            <td>
              <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
                <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
              </a>
            </td>
          </tr>
          {{/if}}
          <tr>
            <th>Dernière Remarque</th>
            <td class="text">
              {{$docGed->_lastentry->remarques|nl2br}}
            </td>
          </tr>          
          
          <tr>
            <th><label for="formfile">Fichier</label></th>
            <td>
              <input type="file" name="formfile" size="0" title="str|notNull" />
            </td>
          </tr>
          <tr>
            <th><label for="suivi[remarques]" title="Veuillez saisir vos remarques">Remarques</label></th>
            <td>
              <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}|notNull"></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button class="modify" type="submit">
                Ajouter
              </button>
            </td>
          </tr>
        {{else}}
          <tr>
            <td class="button" colspan="2">
              <br />Le document suivant est en cours de validation auprès du service Qualité.
              <br />Vous ne pouvez pas y apporter de modification.
              <br />
              <br />
              <a href="javascript:popFile({{$docGed->_lastentry->file_id}})" title="Voir le Fichier">
                <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
              </a>
              <br />{{$docGed->_reference_doc}}
              <br />{{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}
            </td>
          </tr>
        {{/if}}
      </table>
      </form>
    </td>
  </tr>
</table>