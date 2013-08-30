{{*
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<br/>
<div class="small-info">
  <div>Pour vous assurez du bon fonctionnement du module. Veuillez paramétrer les différents points ci-dessous :</div>
</div>
<br/>

<fieldset>
  <legend>Vérification des paramètres</legend>
  <table class="tbl" style="width: auto">
    <tr>
      <th class="section">Données / Commande</th>
      <th class="section">Test</th>
    </tr>
    <tr>
      <td>
        {{tr}}Identification_praticien(ADELI/RPPS){{/tr}}
      </td>
      <td>
        Vérification automatique impossible
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Identification_group(FINESS/SIRET){{/tr}}
      </td>
      <td>
        {{if $group->siret || $group->finess}}ok{{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Mediboard_oid{{/tr}}
      </td>
      <td>
        {{if $mb_oid}}ok{{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Cda_oid{{/tr}}
      </td>
      <td>
        {{if $cda_oid}}ok{{/if}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}Install_Java{{/tr}}
      </td>
      <td>
        {{if $java}}ok{{/if}}
      </td>
    </tr>
  </table>
</fieldset>
<br/>
<fieldset>
  <legend>Association à effectuer</legend>
  <form name="form_type_code" method="POST">
    <input type="hidden" name="m" value="cda" />
    <input type="hidden" name="dosql" value="do_cda_association_aed" />
    <input type="hidden" name="group_id" value="{{$group->_id}}"/>
    <table class="form">
      <tr>
        <th class="title">
          Catégories de fichier
        </th>
        <th class="title">
          Association
        </th>
      </tr>
      {{foreach from=$categories item=_category}}
        <tr>
          <td>
            {{$_category->nom}}
          </td>
          <td>
            <select name="select[{{$_category->_id}}]">
              <option value="">&mdash; Aucune association &mdash;</option>
              {{foreach from=$type_code item=_type_code}}
                <option value="{{$_type_code.code}}"
                        {{if $_category->_ref_last_id400->id400 === $_type_code.code}}selected{{/if}}>
                  {{$_type_code.displayName}}
                </option>
                {{foreachelse}}
                null
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
            Aucune categorie
          </td>
        </tr>
      {{/foreach}}
      <tr>
        <th class="title">
          Etablissement
        </th>
        <th class="title">
          Association
        </th>
      </tr>
      <tr>
        <td>
          {{$group->text}}
        </td>
        <td>
          <select name="group_type">
            <option value="">&mdash; Aucune association &mdash;</option>
            {{foreach from=$type_group item=_type_group}}
              <option value="{{$_type_group.code}}"
                      {{if $group->_ref_last_id400->id400 === $_type_group.code}}selected{{/if}}>
                {{$_type_group.displayName}}
              </option>
              {{foreachelse}}
              null
            {{/foreach}}
          </select>
        </td>
      </tr><tr>
        <td colspan="2">
          <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
</fieldset>