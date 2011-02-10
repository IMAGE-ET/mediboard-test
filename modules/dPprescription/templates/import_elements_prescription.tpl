{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<h2>Import de catalogue d'éléments de prescriptions</h2>

<div class="small-info">
	{{if $schemaPath == "csv"}}
	  Merci de fournir un fichier CSV comportant les colonnes suivantes :
    <ul>
      <li>Nom de chapitre</li>
      <li>Nom de catégorie</li>
      <li>Nom de l'élément</li>
    </ul>
		
		Exemple de fichier CSV valide:<br />
		<table class="main tbl">
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Albumine dans LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Chlore dans LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Dosage des IGG du LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Glucose dans LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Iso&eacute;lectrofocalisation du LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie LCR</td>
	      <td>Production intrath&eacute;cale d'immunoglobulines dans LCR</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie SANG</td>
	      <td>Acide urique s&eacute;rique</td>
	    </tr>
	    <tr>
	      <td>biologie</td>
	      <td>Biochimie SANG</td>
	      <td>Albumine s&eacute;rique</td>
	    </tr>
      <tr>
        <td>biologie</td>
        <td>Biochimie SANG</td>
        <td>Aldolase s&eacute;rique</td>
      </tr>
      <tr>
        <td>...</td>
        <td>...</td>
        <td>...</td>
      </tr>
		</table>
	{{else}}
    Merci de fournir un document XML valide, au regard du schéma suivant :
    <ul>
    	<li><a href="{{$schemaPath}}">Schéma d'import</a></li>
		</ul>
	{{/if}}
</div>

<form action="" method="post" enctype="multipart/form-data">
  <input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
	
	Fichier:
  <input type="file" name="docPath" size="40">

  Etablissement: 
  <select name="group_id">
    <option value="no_group"> Tous </option>
    {{foreach from=$groups item=_group}}
      <option value="{{$_group->_id}}">{{$_group->_view}}</option>
    {{/foreach}}
  </select>
  
  <button type="submit" class="submit">Importer</button>
</form>

