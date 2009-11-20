{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function viewCodeComplet(){
  var oForm = document.selection;
  oForm.codeacte.value = "{{$code->code}}";
  oForm.submit();
}

function selectCode(code,tarif) {
  window.opener.CCAMSelector.set(code,tarif);
  window.close();
}

function updateFields(selected) {
  Element.cleanWhitespace(selected);
  var dn = selected.childNodes;
  $('selection_codeacte').value = dn[0].firstChild.nodeValue;
}

Main.add(function () {
  PairEffect.initGroup("chapEffect");
  
  new Ajax.Autocompleter(
    'selection_codeacte',
    'codeacte_auto_complete',
    '?m=dPccam&ajax=1&suppressHeaders=1&a=httpreq_do_ccam_autocomplete', {
      minChars: 2,
      frequency: 0.15,
      updateElement: updateFields
    }
  );
});
  
</script>

<table class="fullCode">
  <tr>
      <td>

        <table>
        <tr>
           <td colspan="2">
            <form action="?" name="selection" method="get" >

            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
            <input type="hidden" name="dialog" value="{{$dialog}}" />

            <table class="form">
              <tr>
                <th><label for="codeacte" title="Code CCAM de l'acte">Code de l'acte</label></th>
                <td>
                  <input tabindex="1" type="text" size="30" name="codeacte" class="code ccam" value="{{if $codeacte!="-"}}{{$codeacte|stripslashes}}{{/if}}" />
                  <div style="display: none;" class="autocomplete" id="codeacte_auto_complete"></div>                 
                  <button tabindex="2" class="search" type="submit">Afficher</button>
                  {{if $codeComplet}}
                  <button class="search" type="button" onclick="viewCodeComplet()">Code complet</button>
                  {{/if}}
                </td>
              </tr>
            </table>

            </form>
          </td>
        </tr>
        
        <tr>
          <td>
            <table class="form">
              <tr>
                <td class="button">
                  {{if $can->edit}}
                  <form name="addFavoris" action="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}" method="post">
            
                  <input type="hidden" name="dosql" value="do_favoris_aed" />
                  <input type="hidden" name="del" value="0" />
                 
                  <input type="hidden" name="favoris_code" value="{{$codeacte}}" />
                  <input type="hidden" name="favoris_user" value="{{$user}}" />
                  
                  
                  
                 <select name="object_class" class="{{$favoris->_props.object_class}}">
                    <option value="COperation"  {{if $object_class == "COperation"}} selected="selected" {{/if}}>Intervention</option>
                    <option value="CConsultation" {{if $object_class == "CConsultation"}} selected="selected" {{/if}}>Consultation</option>
                    <option value="CSejour" {{if $object_class == "CSejour"}} selected="selected" {{/if}}>Séjour</option>
                 </select>
                  <button class="submit" type="submit" name="btnFuseAction">
                    Ajouter à mes favoris
                  </button>

                  </form>
                  {{/if}}

                  {{if $dialog}}
                    {{if !$hideSelect}}
                      <button class="tick" type="button" onclick="selectCode('{{$codeacte}}','{{$tarif}}')">Sélectionner ce code</button>
                    {{/if}}
                  {{/if}}
                </td>
              </tr>
            </table>

          </td>
        </tr>
        
        <tr>
          <td><strong>Description</strong><br />{{$libelle}}</td>
        </tr>

        {{foreach from=$rq item=_rq}}
        <tr>
          <td><em>{{$_rq|nl2br}}</em></td>
        </tr>
        {{/foreach}}
 
        {{if $act|@count}}
        <tr>
          <td><strong>Activités associées</strong></td>
        </tr>
        
        {{foreach from=$act item=_act}}
        <tr>
          <td style="vertical-align: top; width: 100%">
            <ul>
              <li>
              	Activité {{$_act->numero}} <em>({{$_act->type}}) {{$_act->libelle}}</em> :
                <ul>
                  <li>Phase(s) :
                    <ul>
                      {{foreach from=$_act->phases item=_phase}}
                      <li>
                      	Phase {{$_phase->phase}} <em>({{$_phase->libelle}})</em> : {{$_phase->tarif|currency}}
                      	{{if $_phase->charges}}
                      		<br />Charges supplémentaires de cabinets possibles : {{$_phase->charges|currency}}
                      	{{/if}}
                      </li>
                      {{/foreach}}
                    </ul>
                  </li>
                  <li>Modificateur(s) :
                    <ul>
                      {{foreach from=$_act->modificateurs item=_mod}}
                      <li>{{$_mod->code}} : {{$_mod->libelle}}</li>
                      {{foreachelse}}
                      <li><em>Aucun modificateur applicable à cet acte</em></li>
                      {{/foreach}}
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </td>
        </tr>
        {{/foreach}}
        {{/if}}
        
        {{if $codeproc}}
        <tr>
          <td><strong>Procédure associée</strong></td>
        </tr>
        
        <tr>
          <td>
            <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;codeacte={{$codeproc}}"><strong>{{$codeproc}}</strong></a>
            <br />
            {{$textproc}}
          </td>
        </tr>
        {{/if}}
        
        {{if $remboursement !== null}}
        <tr>
          <td><strong>Remboursement</strong></td>
        </tr>
        
        <tr>
          <td>{{tr}}CCodeCCAM.remboursement.{{$remboursement}}{{/tr}}</td>
        </tr>
        {{/if}}
      </table>

    </td>
    <td>

      <table>
        <tr>
          <th class="category" colspan="2">Place dans la CCAM {{$place}}</th>
        </tr>
        
        {{foreach from=$chap item=_chap}}
        <tr id="chap{{$_chap.rang}}-trigger">
          <th style="text-align:left">{{$_chap.rang}}</th>
          <td>{{$_chap.nom}}<br /></td>
        </tr>
        <tbody class="chapEffect" id="chap{{$_chap.rang}}">
          <tr>
            <td />
            <td>
              <em>
                {{if $_chap.rq}}
                {{$_chap.rq|nl2br}}
                {{else}}
                * Pas d'informations
                {{/if}}
              </em>
            </td>
          </tr>
        </tbody>
        {{/foreach}}
        
      </table>

    </td>
  </tr>
  <tr>
    <td>

      <table>
        <tr>
          <th class="category" colspan="2">Actes associés ({{$asso|@count}})</th>
        </tr>
        
        {{foreach name=associations from=$asso item=_asso}}
        <tr>
          <th><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;codeacte={{$_asso.code}}">{{$_asso.code}}</a></th>
          <td>{{$_asso.texte}}</td>
        </tr>
        {{/foreach}}
      </table>

    </td>
    <td>

      <table>
        <tr>
          <th class="category" colspan="2">Actes incompatibles ({{$incomp|@count}})</th>
        </tr>
        
        {{foreach name=incompatibilites from=$incomp item=_incomp}}
        <tr>
          <th><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;codeacte={{$_incomp.code}}">{{$_incomp.code}}</a></th>
          <td>{{$_incomp.texte}}</td>
        </tr>
        {{/foreach}}
      </table>

    </td>
  </tr>
</table>
