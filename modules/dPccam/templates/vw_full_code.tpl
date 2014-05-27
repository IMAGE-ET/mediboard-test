<script>
function viewCodeComplet(){
  var oForm = document.findCode;
  $V(oForm._codes_ccam, "{{$code->code}}");
  oForm.submit();
}

function selectCode(code,tarif) {
  window.opener.CCAMSelector.set(code, tarif);
  window.close();
}

Main.add(function () {
  PairEffect.initGroup("chapEffect");

  var element = getForm("findCode")._codes_ccam;
  var url = new Url("ccam", "httpreq_do_ccam_autocomplete");
  url.autoComplete(element, 'codeacte_auto_complete',{
    minChars: 2,
    frequency: 0.15,
    select: "code"
  });
});
</script>

<table class="fullCode">
  <tr>
    <td class="pane">
      <table>
        <tr>
          <td colspan="2">
            <form action="?" name="findCode" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
              <input type="hidden" name="dialog" value="{{$dialog}}" />

              <table class="form">
                <tr>
                  <th><label for="_codes_ccam" title="Code CCAM de l'acte">Code de l'acte</label></th>
                  <td>
                    <input tabindex="1" type="text" size="30" name="_codes_ccam" class="code ccam autocomplete" value="{{if $codeacte!="-"}}{{$codeacte|stripslashes}}{{/if}}" />
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
                  <form name="addFavoris" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
                    <input type="hidden" name="m" value="ccam" />
                    <input type="hidden" name="dosql" value="do_favoris_aed" />

                    <input type="hidden" name="favoris_code" value="{{$codeacte}}" />
                    <input type="hidden" name="favoris_user" value="{{$user}}" />

                    <select name="object_class" class="{{$favoris->_props.object_class}}">
                      <option value="COperation"  {{if $object_class == "COperation"}} selected="selected" {{/if}}>{{tr}}COperation{{/tr}}</option>
                      <option value="CConsultation" {{if $object_class == "CConsultation"}} selected="selected" {{/if}}>{{tr}}CConsultation{{/tr}}</option>
                      <option value="CSejour" {{if $object_class == "CSejour"}} selected="selected" {{/if}}>{{tr}}CSejour{{/tr}}</option>
                    </select>
                    <button class="submit" type="submit">
                      Ajouter à mes favoris
                    </button>
                  </form>
                  {{/if}}

                  {{if $dialog && !$hideSelect}}
                    <button class="tick" type="button" onclick="selectCode('{{$codeacte}}','{{$code->_default}}')">Sélectionner ce code</button>
                  {{/if}}
                </td>
              </tr>
            </table>

          </td>
        </tr>
        
        <tr>
          <td><strong>Description</strong><br />{{$code->libelleLong}}</td>
        </tr>

        {{foreach from=$code->remarques item=_rq}}
          <tr>
            <td><em>{{$_rq|nl2br}}</em></td>
          </tr>
        {{/foreach}}
 
        {{if $code->activites|@count}}
        <tr>
          <td><strong>Activités associées</strong></td>
        </tr>
        
        {{foreach from=$code->activites item=_act}}
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
                      <li class="empty">Aucun modificateur applicable à cet acte</li>
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
        
        {{if $code->procedure.code}}
        <tr>
          <td><strong>Procédure associée</strong></td>
        </tr>
        
        <tr>
          <td>
            <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;_codes_ccam={{$code->procedure.code}}"><strong>{{$code->procedure.code}}</strong></a>
            <br />
            {{$code->procedure.texte}}
          </td>
        </tr>
        {{/if}}
        
        {{if $code->remboursement !== null}}
          <tr>
            <td><strong>Remboursement</strong></td>
          </tr>
          <tr>
            <td>{{tr}}CDatedCodeCCAM.remboursement.{{$code->remboursement}}{{/tr}}</td>
          </tr>
        {{/if}}
        
        {{if $code->forfait !== null}}
          <tr>
            <td><strong>Forfait spécifique</strong></td>
          </tr>
          <tr>
            <td>{{tr}}CDatedCodeCCAM.remboursement.{{$code->forfait}}{{/tr}}</td>
          </tr>
        {{/if}}
      </table>
    </td>

    <td class="pane">
      <table>
        <tr>
          <th class="category">Place dans la CCAM {{$code->place}}</th>
        </tr>
        
        {{foreach from=$code->chapitres item=_chap}}
          <tr id="chap{{$_chap.rang}}-trigger">
            <td>
              {{$_chap.rang}}
              <br />
              {{$_chap.nom}}
            </td>
          </tr>
          <tbody class="chapEffect" id="chap{{$_chap.rang}}">
            <tr>
              <td>
                <ul>
                  <em>
                    {{foreach from=$_chap.rq item=rq}}
                      <li>{{$rq}}</li>
                    {{foreachelse}}
                      <li>Pas d'informations</li>
                    {{/foreach}}
                  </em>
                </ul>
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
          <th class="category" colspan="2">Actes associés</th>
        </tr>
        {{foreach from=$code->activites item=_activite}}
          <tr>
            <td colspan="2"><strong>{{$_activite->type}} ({{$_activite->assos|@count}})</strong></td>
          </tr>
          {{foreach name=associations from=$_activite->assos item=_asso}}
            <tr>
              <th>
                <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;_codes_ccam={{$_asso.code}}">
                  {{$_asso.code}}
                </a>
              </th>
              <td>{{$_asso.texte}}</td>
            </tr>
          {{/foreach}}
        {{/foreach}}
      </table>
    </td>

    <td class="pane">
      <table>
        <tr>
          <th class="category" colspan="2">Actes incompatibles ({{$code->incomps|@count}})</th>
        </tr>

        {{foreach name=incompatibilites from=$code->incomps item=_incomp}}
          <tr>
            <th>
              <a href="?m={{$m}}&amp;dialog={{$dialog}}&amp;{{$actionType}}={{$action}}&amp;_codes_ccam={{$_incomp.code}}">
                {{$_incomp.code}}
              </a>
            </th>
            <td>{{$_incomp.texte}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2">Pas de code incompatible</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
