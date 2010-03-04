<!-- $Id$ -->

{{mb_include_script module="system" script="object_selector"}}

<script type="text/javascript">

function choosePreselection(oSelect) {
  if (!oSelect.value) { 
    return;
  }
  
  var aParts = oSelect.value.split("|");
  var sLibelle = aParts.pop();
  var sCode = aParts.pop();

  var oForm = oSelect.form;
  oForm.cmca_uf_code.value = sCode;
  oForm.cmca_uf_libelle.value = sLibelle;
  
  oSelect.value = "";
}

</script>

<table class="main">
  <tr>
    <td class="text">
      {{if $mbObject->_id}}
      {{if $typeObject == "op"}}
      {{assign var="mbSejour" value=$mbObject->_ref_sejour}}
      {{else}}
      {{assign var="mbSejour" value=$mbObject}}
      {{/if}}
      {{if !$doc_valid}}
      <h3>Document non valide : pensez à valider les valeurs suivantes !</h3>
      <ul>
        <li>
          {{if $mbSejour->_ref_patient->_IPP}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Identifiant S@nté.com du patient : 
          '{{$mbSejour->_ref_patient->_IPP}}'
          </div>
        </li>
        <li>
          {{if $mbSejour->_num_dossier}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Identifiant S@nté.com de la venue : 
          '{{$mbSejour->_num_dossier}}'
          </div>
        </li>
        <li>
          {{if $mbSejour->_ref_praticien->adeli}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Code Adeli du praticien responsable du séjour : 
          '{{mb_value object=$mbSejour->_ref_praticien field=adeli}}' 
          (Dr {{$mbSejour->_ref_praticien->_view}})
          </div>
        </li>
        {{if $typeObject =="op"}}
        <li>
          {{if $mbObject->_ref_chir->_view}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Code Adeli du chirurgien responsable de l'intervention : 
          '{{mb_value object=$mbObject->_ref_chir field=adeli}}'
          (Dr {{$mbObject->_ref_chir->_view}})
          </div>
        </li>
        <li>
          {{if $mbObject->code_uf}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Code d'unité fonctionnelle S@nté.com : 
          '{{$mbObject->code_uf}}'
          </div>
        </li>
        <li>
          {{if $mbObject->libelle_uf}}
          <div class="message">
          {{else}}
          <div class="error">
          {{/if}}
          Libellé d'unité fonctionnelle S@nté.com : 
          '{{$mbObject->libelle_uf}}'
          </div>
        </li>
        {{/if}}
      </ul>
      {{/if}}
      <h3>

      {{if $doc->documentfilename}}
      <h3>XML: Génération du document</h3>
      <ul>
        <li>
          Consulter <a href="{{$doc->documentfilename}}">le Document H'XML</a>: 
          Le document <strong>{{if $doc_valid}}est valide!{{else}}n'est pas valide...{{/if}}</strong>
        </li>
        {{if $typeObject == "op"}}
        <li>
          Visualiser <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$mbObject->_id}}">l'intervention correspondante</a>
        </li>
        {{/if}}
      </ul>
      {{/if}}
      {{if $logs|@count}}
        <h3>Envoi du document au serveur S@nté.com</h3>
      <ul>
      {{foreach from=$logs|smarty:nodefaults item=log}}
        <li>{{$log}}</li>
      {{/foreach}}
      </ul>
      {{/if}}
      <h3>Tous les fichiers envoyés pour cet objet</h3>
      <ul>
        {{foreach from=$doc->sentFiles item=curr_file}}
        <li>
          Fichier <a href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_path={{$curr_file.path}}">{{$curr_file.name}}</a>
          envoyé le {{$curr_file.datetime|date_format:"%A %d %B %Y à %H:%M:%S"}}
        </li>
        {{foreachelse}}
        Aucun fichier envoyé
        {{/foreach}}
      </ul>
      {{/if}}
    </td>
  </tr>
  <tr>
</table>