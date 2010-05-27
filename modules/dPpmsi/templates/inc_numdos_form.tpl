{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $sejour->_ref_numdos}}
  <script type="text/javascript">
  Main.add(function () {
    prepareForm(document.forms.editNumdos{{$sejour->_id}});
  });
  </script>
  
  <form name="editNumdos{{$sejour->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$sejour->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$sejour->_ref_numdos->_id}}" />
    
    <table class="form" style="table-layout:fixed">
      <tr>
        <th class="category" colspan="4">
          Numéro de dossier
          <script type="text/javascript">
            SejourHprimSelector.init{{$sejour->_id}} = function(){
              this.sForm      = "editNumdos{{$sejour->_id}}";
              this.sId        = "id400";
              this.sIPPForm   = "editIPP";
              this.sIPPId     = "id400";
              this.sIPP       = document.forms.editIPP.id400.value;
              this.sPatNom    = "{{$patient->nom}}";
              this.sPatPrenom = "{{$patient->prenom}}";
              this.pop();
            };
          </script>
        </th>
      </tr>
      <tr>
        <th>
          <label for="id400" title="Saisir le numéro de dossier">Numéro de dossier</label>
        </th>
        <td>
          <input type="text" class="notNull" name="id400" value="{{$sejour->_ref_numdos->id400}}" size="8" />
          <input type="hidden" class="notNull" name="tag" value="{{$sejour->_ref_numdos->tag}}" />
          <input type="hidden" class="notNull" name="object_id" value="{{$sejour->_id}}" />
          <input type="hidden" class="notNull" name="object_class" value="CSejour" />
          <input type="hidden" name="last_update" value="{{$sejour->_ref_numdos->last_update}}" />
          <em>(Suggestion : {{$sejour->_guess_num_dossier}}) </em>
        </td>
        <td class="button" rowspan="2">
          <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        </td>
        <td class="button" rowspan="2">
          {{if $hprim21installed}}
            <button class="search" type="button" onclick="SejourHprimSelector.init{{$sejour->_id}}()">{{tr}}Search{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
    </table>
  </form>
{{else}}
  <div class="big-warning">
    Il est propable qu'aucun tag ne soit spécifié pour le numéro de dossier, il n'est donc pas possible de manipuler les numéros de dossiers.<br />
    Allez dans <a href="?m=dPplanningOp&amp;tab=configure">la configuration du module {{tr}}module-dPplanningOp-court{{/tr}}</a>.
  </div>
{{/if}}
