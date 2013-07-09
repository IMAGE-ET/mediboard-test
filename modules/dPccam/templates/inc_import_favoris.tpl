{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<h2>Import de favoris pour la nomenclature {{$nomenclature|upper}}</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants dans un fichier CSV (<strong>au format ISO</strong>) dont les champs sont séparés par
  <strong>;</strong> et les textes par <strong>"</strong>, la première ligne étant sautée :
  <ul>
    <li>Nom et Prénom du praticien *</li>
    <li>Tag</li>
    {{if $nomenclature == "ccam"}}
      <li>Chapitres CCAM (séparés par des .)</li>
    {{/if}}
    <li>Code *</li>
    {{if $nomenclature == "ccam"}}
      <li>Type d'objet (Consultation, Intervention ou Séjour) <b>Intervention</b> par défaut</li>
    {{/if}}
  </ul>
  <em>* : champs obligatoires</em>
</div>

<form method="post" action="?m={{$m}}&nomenclature={{$nomenclature}}&a=ajax_import_favoris&dialog=1" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="a" value="ajax_import_favoris" />
  
  <input type="file" name="import" />
  
  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="4">{{$results|@count}} favoris traités</th>
    </tr>
    <tr>
      <th class="section">Etat</th>
      <th class="section">Praticien</th>
      <th class="section">Code</th>
      <th class="section">Tag</th>
    </tr>
    {{foreach from=$results item=_result}}
      <tr>
        <td class="text">
          {{if $_result.error}}
            {{$_result.error}}
          {{else}}
            OK
          {{/if}}
        </td>
        <td>
          {{$_result.praticien}}
        </td>
        <td>
          {{$_result.code}}
        </td>
        <td>
          {{$_result.tag}}
        </td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}