<!-- $Id$ -->

{literal}
<script type="text/javascript">

function pageMain() {
  regFieldCalendar("editFrm", "deb", true);
  regFieldCalendar("editFrm", "fin", true);
}

</script>
{/literal}

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{$m}" />
        
    <table class="form">
      <tr>
        <th class="category" colspan="10">Filtrer les messages</th>
      </tr>

      <tr>
        <th><label for="filter_status" title="Filtrer les messages">Filtrer :</label></th>
        <td>
          <select name="filter_status" onchange="this.form.submit()">
           {html_options options=$mp_status selected=$filter_status}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="10"><strong>Liste des messages</strong></th>
    </tr>
    
    <tr>
      <th>Début de publication</th>
      <th>Fin de publication</th>
      <th>Titre</th>
      <th>Contenu</th>
    </tr>

    {foreach from=$messages item=curr_message}
    <tr>
      {eval var=$curr_message->message_id assign="message_id"}
      {assign var="href" value="?m=$m&amp;tab=$tab&amp;message_id=$message_id"}
      <td><a href="{$href}">{$curr_message->deb|date_format:"%d/%m/%Y %H:%M"}</a></td>
      <td><a href="{$href}">{$curr_message->fin|date_format:"%d/%m/%Y %H:%M"}</a></td>
      <td><a href="{$href}">{$curr_message->titre}</a></td>
      <td>{$curr_message->corps|nl2br}</td>
    </tr>
    {/foreach}
      
    </table>

  </td>
  
  <td class="pane">

  <a href="index.php?m={$m}&amp;tab={$tab}&amp;message_id=0"><strong>Créer un message</strong></a>

    <form name="editFrm" action="?m={$m}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_message_aed" />
    <input type="hidden" name="message_id" value="{$message->message_id}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {if $message->message_id}
        Modification d'un message
      {else}
        Création d'un message
      {/if}
      </th>
    </tr>

    <tr>
      <th><label for="deb" title="Date de début de la publication, obligatoire">Début de publication :</label></th>
      <td class="date">
        <div id="editFrm_deb_da">{$message->deb|date_format:"%d/%m/%Y %H:%M"}</div>
        <input type="hidden" name="deb" title="{$message->_props.deb}" value="{$message->deb}" />
        <img id="editFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
      </td>
    </tr>

    <tr>
      <th><label for="fin" title="Date de fin de la publication, obligatoire">Début de fin :</label></th>
      <td class="date">
        <div id="editFrm_fin_da">{$message->fin|date_format:"%d/%m/%Y %H:%M"}</div>
        <input type="hidden" name="fin" title="{$message->_props.fin}" value="{$message->fin}" />
        <img id="editFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
      </td>
    </tr>

    <tr>
      <th><label for="titre" title="Titre du message, obligatoire.">Titre :</label></th>
      <td><input type="text" name="titre" title="{$message->_props.titre}" value="{$message->titre}" /></td>
    </tr>

    <tr>
      <th><label for="corps" title="Corps du message, obligatoire.">Corps :</label></th>
      <td>
        <textarea style="width: 200px" rows="4" name="corps" title="{$message->_props.corps}">{$message->corps}</textarea>
      </td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {if $message->message_id}
        <input type="reset" value="Réinitialiser" />
        <input type="submit" value="Valider" />
        <input type="button" value="Supprimer" onclick="confirmDeletion(this.form,{ldelim}typeName:'message',objName:'{$message->_view|escape:javascript}'{rdelim})" />
        {else}
        <input type="submit" value="Créer" />
        {/if}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>

