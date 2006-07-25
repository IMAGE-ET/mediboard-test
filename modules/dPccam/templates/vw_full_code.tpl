<!-- $Id$ -->

<script type="text/javascript">

function selectCode(code) {
  window.opener.setCode(code, "ccam");
  window.close();
}

function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $('selection_codeacte').value = dn[0].firstChild.nodeValue;
}

function pageMain() {
  initGroups("chap");
  
  new Ajax.Autocompleter(
    'selection_codeacte',
    'codeacte_auto_complete',
    'index.php?m=dPccam&ajax=1&suppressHeaders=1&a=httpreq_do_ccam_autocomplete', {
      minChars: 2,
      frequency: 0.15,
      updateElement: updateFields
    }
  );
}
  
</script>

{{if $dialog}}
{{assign var="action" value="dialog=1&amp;a"}}
{{else}}
{{assign var="action" value="tab"}}
{{/if}}

<table class="fullCode">
  <tr>
      <td class="pane">

        <table>
        <tr>
           <td colspan="2">
            <form action="?" name="selection" method="get" >

            {{if $dialog}}
            <input type="hidden" name="a" value="{{$a}}" />
            {{else}}
            <input type="hidden" name="tab" value="{{$tab}}" />
            {{/if}}
            
            <input type="hidden" name="dialog" value="{{$dialog}}" />
            <input type="hidden" name="m" value="{{$m}}" />

            <table class="form">
              <tr>
                <th><label for="codeacte" title="Code CCAM de l'acte">Code de l'acte</label></th>
                <td>
                  <input tabindex="1" type="text" size="30" name="codeacte" title="code|ccam" value="{{if $codeacte!="XXXXXXX"}}{{$codeacte}}{{/if}}" />
                  <div style="display: none;" class="autocomplete" id="codeacte_auto_complete"></div>                 
                  <button tabindex="2" class="search" type="submit">Afficher</button>
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
                  {{if $canEdit}}
                  <form name="addFavoris" action="?m={{$m}}&amp;{{$action}}={{$a}}" method="post">
            
                  <input type="hidden" name="dosql" value="do_favoris_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="favoris_code" value="{{$codeacte}}" />
                  <input type="hidden" name="favoris_user" value="{{$user}}" />

                  <button class="submit" type="submit" name="btnFuseAction">
                    Ajouter à mes favoris
                  </button>

                  </form>
                  {{/if}}

                  {{if $dialog}}
                  <button class="tick" type="button" onclick="selectCode('{{$codeacte}}')">Sélectionner ce code</button>
                  {{/if}}
                </td>
              </tr>
            </table>

          </td>
        </tr>
        
        <tr>
          <td><strong>Description</strong><br />{{$libelle}}</td>
        </tr>

        {{foreach from=$rq item=curr_rq}}
        <tr>
          <td><em>{{$curr_rq}}</em></td>
        </tr>
        {{/foreach}}
 
        <tr>
          <td><strong>Activités associées</strong></td>
        </tr>
 
        {{foreach from=$act item=curr_act}}
        <tr>
          <td style="vertical-align: top; width: 100%">
            <ul>
              <li>Activité {{$curr_act->numero}} ({{$curr_act->type|escape}}) : {{$curr_act->libelle|escape}}
                <ul>
                  <li>Phase(s) :
                    <ul>
                      {{foreach from=$curr_act->phases item=curr_phase}}
                      <li>Phase {{$curr_phase->phase}} : {{$curr_phase->libelle|escape}} : {{$curr_phase->tarif}}&euro;</li>
                      {{/foreach}}
                    </ul>
                  </li>
                  <li>Modificateur(s) :
                    <ul>
                      {{foreach from=$curr_act->modificateurs item=curr_mod}}
                      <li>{{$curr_mod->code}} : {{$curr_mod->libelle|escape}}</li>
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
        
        <tr>
          <td><strong>Procédure associée</strong></td>
        </tr>
        
        <tr>
          <td>
            <a href="?m={{$m}}&amp;{{$action}}=vw_full_code&amp;codeacte={{$codeproc}}"><strong>{{$codeproc}}</strong></a>
            <br />
            {{$textproc}}
          </td>
        </tr>
      </table>

    </td>
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Place dans la CCAM {{$place}}</th>
        </tr>
        
        {{foreach from=$chap item=curr_chap}}
        <tr class="groupcollapse" id="chap{{$curr_chap.rang}}" onclick="flipGroup('{{$curr_chap.rang}}', 'chap')">
          <th style="text-align:left">{{$curr_chap.rang}}</th>
          <td>{{$curr_chap.nom}}<br /></td>
        </tr>
        <tr class="chap{{$curr_chap.rang}}">
          <td></td>
          <td><em>{{if $curr_chap.rq}}{{$curr_chap.rq|nl2br}}{{else}}* Pas d'informations{{/if}}</em></td>
        </tr>
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
          <th><a href="?m={{$m}}&amp;{{$action}}=vw_full_code&amp;codeacte={{$curr_asso.code}}">{{$curr_asso.code}}</a></th>
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
          <th><a href="index.php?m={{$m}}&amp;{{$action}}=vw_full_code&amp;codeacte={{$curr_incomp.code}}">{{$curr_incomp.code}}</a></th>
          <td>{{$curr_incomp.texte}}</td>
        </tr>
        {{/foreach}}
      </table>

    </td>
  </tr>
</table>
