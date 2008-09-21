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

{{if !$ajax}}
<h2>Génération d'un fichier H'XML pour {{$mbObject->_view}}</h2>
{{/if}}

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
      {{if !$ajax}}
      <h3>XML: Schema de validation</h3>
      <ul>
        <li>Consulter <a href="{{$doc->schemafilename}}">le Schema de validation H'XML</a>.</li>
      </ul>
      {{/if}}
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
      {{if $ftp->logs|@count}}
        <h3>Envoi du document au serveur S@nté.com</h3>
      <ul>
      {{foreach from=$ftp->logs|smarty:nodefaults item=log}}
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
  {{if !$ajax && $typeObject == "op"}}
    <td>
      <form name="formEdit" action="?" method="get" onsubmit="return checkForm(this)">  
      <input type="hidden" name="m" value="{{$m}}"/>
      <input type="hidden" name="tab" value="{{$tab}}"/>
      <input type="hidden" name="_class_name" value="COperation"/>
      <table class="form">
        <tr>
          <th class="title" colspan="2">{{$mbObject->_view}}, le {{$mbObject->_datetime|date_format:"%d/%m/%Y"}}</th>
        </tr>
        <tr>
          <th class="category" colspan="2">Identifiants Mediboard</th>
        </tr>
        <tr>	
          <th>Intervention</th>
          <td>
            <input type="hidden" class="notNull ref class|COperation" name="mb_operation_id" value="{{$mbObject->_id}}"/>
	          {{if $mbObject->_id}}
    	      <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_operation_view" value="{{$mbObject->_view|stripslashes}}" />
    	      {{else}}
    	      <input type="text" size="30" readonly="readonly" ondblclick="ObjectSelector.init()" name="_operation_view" value="" />
    	      {{/if}}
            <button type="button" onclick="ObjectSelector.init()" class="search">Rechercher</button>
            <script type="text/javascript">
              ObjectSelector.init = function(){
                this.sForm     = "formEdit";
                this.sId       = "mb_operation_id";
                this.sView     = "_operation_view";
                this.sClass    = "_class_name";  
                this.onlyclass = "false";
                this.pop();
              }
            </script>
          </td>
        </tr>
        {{if $mbObject->operation_id}}
        <tr>
          <th class="category" colspan="2">Identifiants S@nté.com</th>
        </tr>
        <tr>
          <th>
            <label for="sc_patient_id" title="Choisir un identifiant de patient correspondant à l'intervention">Identifiant de patient</label>
          </th>
          <td>
            <input type="text" class="notNull num length|8" name="sc_patient_id" value="{{$mbSejour->_ref_patient->_IPP}}" size="8" maxlength="8" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="sc_venue_id" title="Choisir un identifiant pour la venue correspondant à l'intervention">Identifiant de venue</label><br />
            Suggestion :
          </th>
          <td>
            <input type="text" class="notNull num length|8" name="sc_venue_id" value="{{$mbSejour->_num_dossier}}" size="8" maxlength="8" />
            <br />
            {{$mbSejour->_guess_num_dossier}}
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Identifiants CMCA</th>
        </tr>
        <tr>
          <th>
            <label for="_cmca_uf_preselection" title="Choisir une pré-selection pour remplir les unités fonctionnelles">Pré-sélection</label>
          </th>
          <td>
            <select onchange="choosePreselection(this)">
              <option value="">&mdash; Choisir une pré-selection</option>
              <option value="ABS|ABSENT">(ABS) Absent</option>
              <option value="AEC|ARRONDI EURO">(AEC) Arrondi Euro</option>
              <option value="AEH|ARRONDI EURO">(AEH) Arrondi Euro</option>
              <option value="AMB|CHIRURGIE AMBULATOIRE">(AMB) Chirurgie Ambulatoire</option>
              <option value="CHI|CHIRURGIE">(CHI) Chirurgie</option>
              <option value="CHO|CHIRURGIE COUTEUSE">(CHO) Chirurgie Coûteuse</option>
              <option value="EST|ESTHETIQUE">(EST) Esthétique</option>
              <option value="EXL|EXL POUR RECUP V4 V5">(EXL) EXL pour récup. v4 v5</option>
              <option value="EXT|EXTERNES">(EXT) Externes</option>
              <option value="MED|MEDECINE">(MED) Médecine</option>
              <option value="PNE|PNEUMOLOGUE">(PNE) Pneumologie</option>
              <option value="TRF|TRANSFERT >48H">(TRF) Transfert > 48h</option>
              <option value="TRI|TRANSFERT >48H">(TRI) Transfert > 48h</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="cmca_uf_code" title="Choisir un code pour l'unité fonctionnelle">Code de l'unité fonctionnelle</label>
          </th>
          <td>
            <input type="text" class="notNull str maxLength|10" name="cmca_uf_code" value="{{$mbObject->code_uf}}" size="10" maxlength="10" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="cmca_uf_libelle" title="Choisir un libellé pour l'unité fonctionnelle">Libellé de l'unité fonctionnelle</label>
          </th>
          <td>
            <input type="text" class="notNull str maxLength|35" name="cmca_uf_libelle" value="{{$mbObject->libelle_uf}}" size="35" maxlength="35" />
          </td>
        </tr>
        {{/if}}
        <tr>
          <td class="button" colspan="2">
  	        <button class="tick" type="submit">Générer le document</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>

  {{if $doc_valid}}  
  <tr>
    <td>
      <form name="formFTP" action="?m={{$m}}&amp;{{if $a}}a={{$a}}{{/if}}&amp;dialog={{$dialog}}" method="post" onsubmit="return checkForm(this)">
      <table class="form">
        <tr>
          <th class="title" colspan="2">Envoi du document vers un serveur FTP</th>
        </tr>
        <tr>
          <th>
            <label for="hostname" title="Nom pleinement qualifié de l'hôte FTP">Nom du server</label>
          </th>
          <td>
            <input type="text" class="notNull str" name="hostname" value="{{$ftp->hostname}}"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="username" title="Nom de l'utilisateur FTP">Utilisateur</label>
          </th>
          <td>
            <input type="text" class="notNull str" name="username" value="{{$ftp->username}}"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="userpass" title="Nom de l'utilisateur FTP">Mot de passe</label>
          </th>
          <td>
            <input type="password" class="notNull str" name="userpass" value="{{$ftp->userpass}}"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="fileprefix" title="Préfixe pour le nom de fichier à télécharger">Préfix de nom de fichier</label>
          </th>
          <td>
            <input type="text" class="notNull str" name="fileprefix" value="{{$fileprefix}}"/>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Envoyer le document</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{/if}}
  {{/if}}
</table>
