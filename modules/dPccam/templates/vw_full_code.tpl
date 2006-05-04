<!-- $Id$ -->

{literal}
<script type="text/javascript">

function pageMain() {
  initGroups("chap");
}
  
</script>
{/literal}

<table class="fullCode">
  <tr>
  	<td class="pane">

  	  <table>
        <tr>
           <td colspan="2">
            <form action="index.php?m={$m}&amp;tab={$tab}" target="_self" name="selection" method="get" >
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="tab" value="{$tab}" />

            <table class="form">
              <tr>
                <th class="mandatory">Code de l'acte:</th>
                <td>
                  <input tabindex="1" type="text" name="codeacte" value="{$codeacte}" />
                  <input tabindex="2" type="submit" value="afficher" />
                </td>
              </tr>
            </table>

            </form>
          </td>
        </tr>
        
        {if $canEdit}
        <tr>
          <td colspan="2">
            <form name="addFavoris" action="./index.php?m={$m}" method="post">
            
            <input type="hidden" name="dosql" value="do_favoris_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="favoris_code" value="{$codeacte}" />
            <input type="hidden" name="favoris_user" value="{$user}" />

            <table class="form">
              <tr>
                <td class="button"><input class="button" type="submit" name="btnFuseAction" value="Ajouter à mes favoris" /></td>
              </tr>
            </table>

            </form>
          </td>
        </tr>
        {/if}
        
        <tr>
          <td><strong>Description</strong><br />{$libelle}</td>
        </tr>

        {foreach from=$rq item=curr_rq}
        <tr>
          <td><em>{$curr_rq}</em></td>
        </tr>
        {/foreach}
 
        <tr>
          <td><strong>Activités associées</strong></td>
        </tr>
 
        {foreach from=$act item=curr_act}
        <tr>
          <td style="vertical-align: top; width: 100%">
            <ul>
              <li>Activité {$curr_act->numero} ({$curr_act->type|escape}) : {$curr_act->libelle|escape}
                <ul>
                  <li>Phase(s) :
                    <ul>
                      {foreach from=$curr_act->phases item=curr_phase}
                      <li>Phase {$curr_phase->phase} : {$curr_phase->libelle|escape} : {$curr_phase->tarif}&euro;</li>
                      {/foreach}
                    </ul>
                  </li>
                  <li>Modificateur(s) :
                    <ul>
                      {foreach from=$curr_act->modificateurs item=curr_mod}
                      <li>{$curr_mod->code} : {$curr_mod->libelle|escape}</li>
                      {/foreach}
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </td>
        </tr>
        {/foreach}
        
        <tr>
          <td><strong>Procédure associée:</strong></td>
        </tr>
        
        <tr>
          <td>
            <a href="index.php?m={$m}&amp;tab={$tab}&amp;codeacte={$codeproc}"><strong>{$codeproc}</strong></a>
            <br />
            {$textproc}
          </td>
        </tr>
      </table>

    </td>
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Place dans la CCAM: {$place}</th>
        </tr>
        
        {foreach from=$chap item=curr_chap}
        <tr class="groupcollapse" id="chap{$curr_chap.rang}" onclick="flipGroup('{$curr_chap.rang}', 'chap')">
          <th style="text-align:left">{$curr_chap.rang}</th>
          <td>{$curr_chap.nom}<br /></td>
        </tr>
        <tr class="chap{$curr_chap.rang}">
          <td></td>
          <td><em>{if $curr_chap.rq}{$curr_chap.rq|nl2br}{else}* Pas d'informations{/if}</em></td>
        </tr>
        {/foreach}
        
      </table>

    </td>
  </tr>
  <tr>
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Actes associés ({$asso|@count})</th>
        </tr>
        
        {foreach name=associations from=$asso item=curr_asso}
        <tr>
          <th><a href="index.php?m={$m}&amp;tab=2&amp;codeacte={$curr_asso.code}">{$curr_asso.code}</a></th>
          <td>{$curr_asso.texte}</td>
        </tr>
        {/foreach}
      </table>

    </td>
    <td class="pane">

      <table>
        <tr>
          <th class="category" colspan="2">Actes incompatibles ({$incomp|@count})</th>
        </tr>
        
        {foreach name=incompatibilites from=$incomp item=curr_incomp}
        <tr>
          <th><a href="index.php?m={$m}&amp;tab=vw_full_code&amp;codeacte={$curr_incomp.code}">{$curr_incomp.code}</a></th>
          <td>{$curr_incomp.texte}</td>
        </tr>
        {/foreach}
      </table>

    </td>
  </tr>
</table>
