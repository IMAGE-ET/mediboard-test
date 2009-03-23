<!-- $Id$ -->

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
  dn = selected.childNodes;
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
      <td class="pane">

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

        {{foreach from=$rq|smarty:nodefaults item=curr_rq}}
        <tr>
          <td><em>{{$curr_rq|nl2br}}</em></td>
        </tr>
        {{/foreach}}
 
        {{if $act|@count}}
        <tr>
          <td><strong>Activités associées</strong></td>
        </tr>
        
        {{foreach from=$act item=curr_act}}
        <tr>
          <td style="vertical-align: top; width: 100%">
            <ul>
              <li>Activité {{$curr_act->numero}} ({{$curr_act->type}}) : {{$curr_act->libelle}}
                <ul>
                  <li>Phase(s) :
                    <ul>
                      {{foreach from=$curr_act->phases item=curr_phase}}
                      <li>Phase {{$curr_phase->phase}} : {{$curr_phase->libelle}} : {{$curr_phase->tarif}}{{$dPconfig.currency_symbol}}</li>
                      {{/foreach}}
                    </ul>
                  </li>
                  <li>Modificateur(s) :
                    <ul>
                      {{foreach from=$curr_act->modificateurs item=curr_mod}}
                      <li>{{$curr_mod->code}} : {{$curr_mod->libelle}}</li>
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
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Place dans la CCAM {{$place}}</th>
        </tr>
        
        {{foreach from=$chap item=curr_chap}}
        <tr id="chap{{$curr_chap.rang}}-trigger">
          <th style="text-align:left">{{$curr_chap.rang}}</th>
          <td>{{$curr_chap.nom}}<br /></td>
        </tr>
        <tbody class="chapEffect" id="chap{{$curr_chap.rang}}">
          <tr>
            <td />
            <td>
              <em>
                {{if $curr_chap.rq}}
                {{$curr_chap.rq|nl2br}}
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
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Actes associés ({{$asso|@count}})</th>
        </tr>
        
        {{foreach name=associations from=$asso item=curr_asso}}
        <tr>
          <th><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;codeacte={{$curr_asso.code}}">{{$curr_asso.code}}</a></th>
          <td>{{$curr_asso.texte}}</td>
        </tr>
        {{/foreach}}
      </table>

    </td>
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Actes incompatibles ({{$incomp|@count}})</th>
        </tr>
        
        {{foreach name=incompatibilites from=$incomp item=curr_incomp}}
        <tr>
          <th><a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;codeacte={{$curr_incomp.code}}">{{$curr_incomp.code}}</a></th>
          <td>{{$curr_incomp.texte}}</td>
        </tr>
        {{/foreach}}
      </table>

    </td>
  </tr>
</table>
