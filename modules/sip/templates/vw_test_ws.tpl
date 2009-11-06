{{* $Id: vw_tests_client.tpl 6287 2009-05-13 15:37:54Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7240 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="testClientAuth" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <table class="form">
    <tr>
      <th class="title" colspan="2">Client avec authentification</th>
    </tr>
    <tr>
      <th>Opération</th>
      <td>
        Ajouter <input type="radio" name="operation" value="add" {{if $operation == "add"}}checked="checked"{{/if}}/> 
        Soustraire <input type="radio" name="operation" value="subtract" {{if $operation == "subtract"}}checked="checked"{{/if}}/>
        Autre <input type="radio" name="operation" value="autre" {{if $operation == "autre"}}checked="checked"{{/if}}/>  
      </td>
    </tr>
    <tr>
      <th>Entier 1</th>
      <td>
        <input type="text" name="entier1" value="{{$entier1}}" /> 
      </td>
    </tr>
    <tr>
      <th>Entier 2</th>
      <td>
        <input type="text" name="entier2" value="{{$entier2}}" /> 
      </td>
    </tr>
    {{if $result}}
    <tr>
      <th><strong>Résultat</strong></th>
      <td> {{$result}} </td>
    </tr>
    {{/if}}
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">Valider</button>
      </td>
    </tr> 
  </table>
</form>

 <hr />
 
<form name="upload_form" action="?m={{$m}}&amp;tab={{$tab}}" enctype="multipart/form-data" method="post">
  <input type="hidden" name="MAX_FILE_SIZE"  value="4096" /><!-- 4MB -->
  <table class="form">
    <tr>
      <th class="title" colspan="2">Test événement H'XML</th>
    </tr>
    <tr>
      <th>Evénement XML</th>
      <td>
        <input type="file" name="file" />
      </td>
    </tr>
    <tr>
      <th><strong>Résultat</strong></th>
      <td class="text"> 
        <div class="{{if $errors}}small-error{{else}}small-info{{/if}}">
          {{if $errors}}
            Le document est invalide. <br />
            {{$errors}}
          {{else}}
            Le document est valide.
          {{/if}}
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">Valider</button>
      </td>
    </tr> 
  </table>
</form>