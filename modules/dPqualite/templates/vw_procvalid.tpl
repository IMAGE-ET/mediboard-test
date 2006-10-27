{{if $docGed->doc_ged_id}}
<script language="Javascript" type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

function annuleDoc(oForm,annulation){
  oForm.elements["ged[annule]"].value = annulation;
  oForm._validation.value = 1;  
  oForm.elements["ged[etat]"].value = {{$docGed->etat}};
  oForm.submit();
}

function validDoc(oForm){
  if(oForm.elements["suivi[remarques]"].value == ""){
    alert("Veuillez saisir vos remarques dans la zone 'Remarques'.");
    oForm.elements["suivi[remarques]"].focus();
  }else{
    oForm.elements["suivi[doc_ged_suivi_id]"].value = "";
    oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_TERMINE}};
    oForm.elements["suivi[actif]"].value = 1;
    oForm.submit();
  }
}

function refuseDoc(oForm){
  if(oForm.elements["suivi[remarques]"].value == ""){
    alert("Veuillez saisir un motif de refus dans la zone 'Remarques'.");
    oForm.elements["suivi[remarques]"].focus();
  }else{
    oForm.elements["suivi[doc_ged_suivi_id]"].value = "";    
    {{if $docGed->doc_ged_id && $docGed->_lastactif->doc_ged_suivi_id}}
    oForm.elements["ged[doc_theme_id]"].value={{$docGed->doc_theme_id}};
    oForm.elements["ged[titre]"].value="{{$docGed->titre}}";
    {{else}}
    oForm.elements["ged[doc_theme_id]"].value="";
    oForm.elements["ged[doc_categorie_id]"].value="";
    oForm.elements["ged[doc_chapitre_id]"].value="";
    oForm.elements["ged[titre]"].value="";
    {{/if}}
    oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_TERMINE}};      
    oForm.submit();
  }
}

function redactionDoc(oForm){
  oForm.elements["suivi[doc_ged_suivi_id]"].value = "";
  oForm.elements["ged[etat]"].value = {{$smarty.const.CDOC_REDAC}};
  if(oForm.onsubmit()){
    oForm.submit();
  }
}
</script>
{{/if}}
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $procDemande|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Prodécure demandée en Attente
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
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{if $currProc->_lastactif->doc_ged_suivi_id}}
                Révision de la procédure {{$currProc->_reference_doc}}
              {{else}}
                Nouvelle Procédure
              {{/if}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_lastentry->date|date_format:"%A %d %B %Y à %Hh%M"}}
            </a>
          </td>
          {{if $currProc->annule}}
          <td class="text" style="background-color:#f00;">[ANNULE] {{$currProc->_lastentry->remarques|nl2br}}</td>
          {{else}}
          <td>{{$currProc->_lastentry->remarques|nl2br}}</td>
          {{/if}}
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
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_reference_doc}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">{{$currProc->_ref_theme->nom}}</td>
          <td class="text">
          {{if $currProc->annule}}
          <span style="background-color:#f00;">[ANNULE]</span>
          {{/if}}
          {{$currProc->_etat_actuel}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="5">
            Aucune Procédure En Cours
          </td>
        </tr>
        {{/foreach}}
      </table>

      {{if $procTermine|@count}}
        <br />
        {{if !$procAnnuleVisible}}
        <a class="buttontrash" href="index.php?m={{$m}}&amp;procAnnuleVisible=1">
        Afficher les Procédures Annulées
        </a>
        {{else}}
        <a class="buttoncancel" href="index.php?m={{$m}}&amp;procAnnuleVisible=0">
        Cacher les Procédures Annulées
        </a>
        <table class="form">
          <tr>
            <th class="category" colspan="5">
              Prodécure Annulées
            </th>
          </tr>
          <tr>
            <th class="category">Titre</th>
            <th class="category">Référence</th>
            <th class="category">Etablissement</th>
            <th class="category">Thème</th>
            <th class="category">Etat</th>
          </tr>
          {{foreach from=$procTermine item=currProc}}
          <tr>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}&amp;lastactif=1">
                {{$currProc->titre}}
              </a>
            </td>
            <td>
              <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}&amp;lastactif=1">
                {{$currProc->_reference_doc}}
              </a>
            </td>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}&amp;lastactif=1">
                {{$currProc->_ref_group->text}}
              </a>
            </td>
            <td class="text">{{$currProc->_ref_theme->nom}}</td>
            <td class="text" style="background-color:#f00;">
            
              <form name="ProcRetablirFrm{{$currProc->doc_ged_id}}" action="?m={{$m}}" method="post">
              <input type="hidden" name="dosql" value="do_docged_aed" />
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="_validation" value="1" />
      
              <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />  
              <input type="hidden" name="ged[user_id]" value="{{$currProc->user_id}}" />
              <input type="hidden" name="ged[group_id]" value="{{$currProc->group_id}}" />
              <input type="hidden" name="ged[annule]" value="0" />
              <input type="hidden" name="ged[etat]" value="{{$currProc->etat}}" />
              <button class="change" type="submit">
                Retablir
              </button>
              {{if $currProc->etat==CDOC_TERMINE}}
              Document Non Disponible
              {{else}}
              <strong>Document en Révision</strong>
              {{/if}}
              </form>
            </td>
          </tr>
          {{/foreach}}
        </table>
        {{/if}}
      {{/if}}


    </td>
    <td class="halfPane">      
      {{if $docGed->doc_ged_id}}
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_docged_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_validation" value="0" />
      
      <input type="hidden" name="ged[doc_ged_id]" value="{{$docGed->doc_ged_id}}" />  
      <input type="hidden" name="ged[user_id]" value="{{$docGed->user_id}}" />
      <input type="hidden" name="ged[group_id]" value="{{$docGed->group_id}}" />
      <input type="hidden" name="ged[annule]" value="{{$docGed->annule}}" />
      <input type="hidden" name="ged[etat]" value="" />
      
      <input type="hidden" name="suivi[doc_ged_suivi_id]" value="{{$docGed->_lastentry->doc_ged_suivi_id}}" />
      <input type="hidden" name="suivi[user_id]" value="{{$user_id}}" />  
      <input type="hidden" name="suivi[actif]" value="{{$docGed->_lastentry->actif}}" /> 
      <input type="hidden" name="suivi[file_id]" value="{{$docGed->_lastentry->file_id}}" />    

      {{if $docGed->etat==CDOC_DEMANDE && !$lastactif}}
        {{include file="inc_procvalid_demande.tpl"}}
      {{elseif $docGed->etat==CDOC_REDAC && !$lastactif}}
        {{include file="inc_procvalid_redaction.tpl"}}
      {{elseif $docGed->etat==CDOC_VALID && !$lastactif}}
        {{include file="inc_procvalid_validation.tpl"}}
      {{else}}
        {{include file="inc_procvalid_termine.tpl"}}
      {{/if}}

      </form>
      {{/if}}
    </td>
  </tr>
</table>