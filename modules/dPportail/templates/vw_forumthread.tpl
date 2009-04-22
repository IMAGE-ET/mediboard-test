<div>
<a href="?m=dPportail&amp;tab=vw_forumtheme">Liste des thèmes</a> > 
<a href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_theme_id={{$forum_theme->_id}}">{{$forum_theme->_view}}</a>
</div>

<table class="main">
  <tr>
    <td class="halfPane">
      <table width="100%">
      
      {{foreach from=$listThemes item=currTheme}}
        <tr>
          <th class="title"><a href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_theme_id={{$currTheme->_id}}">{{$currTheme->_view}} ({{$currTheme->_ref_forum_threads|@count}} threads)</a></th>
        </tr>
        
        {{if $currTheme->_id == $forum_theme->_id}}
        <tr>
        <td>
          <table class="tbl">
          {{if $listThreads|@count > 0}}
            {{foreach from=$listThreads item=currThread}}
            <tr {{if $currThread->_id == $forum_thread->_id}}class="selected"{{/if}}>
              <td>
              <a href="?m=dPportail&amp;tab=vw_forummessage&amp;forum_thread_id={{$currThread->_id}}&amp;forum_message_id=0" title="Messages du thread">
              {{$currThread->title}}
              </a><br /><span class="small">{{$currThread->body}}</span>
              </td>
              <td>{{$currThread->_ref_forum_messages|@count}} réponse{{if $currThread->_ref_forum_messages|@count > 1}}s{{/if}}</td>
              <td><a class="button edit" href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_thread_id={{$currThread->_id}}" title="Modifier le thread">Modifier</a>
            </tr>
            {{/foreach}}
          {{else}}
            <tr><td>Pas de thread dans ce thème ...</td></tr>
          {{/if}}
          </table>
        </td>
        </tr>
        {{/if}}
          
      {{/foreach}}
        
      </table>
      <a class="button new" href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_thread_id=0&amp;forum_theme_id={{$forum_theme->_id}}">Nouveau thread</a>
    </td>
    <td class="halfPane">
      <form name="editthread" action="?m=dPportail&amp;tab=vw_forumthread&amp;forum_theme_id={{$forum_theme->_id}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_forumthread_aed" />
      <input type="hidden" name="forum_thread_id" value="{{$forum_thread->_id}}" />
      <input type="hidden" name="forum_theme_id" value="{{$forum_theme->_id}}" />
      {{mb_field object=$forum_thread field="user_id" hidden="true"}}
      {{mb_field object=$forum_thread field="date" hidden="true"}}
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $forum_thread->_id}}
          <th class="title modify" colspan="2">Modification du thread "{{$forum_thread->_view}}"</th>
          {{else}}
          <th class="title" colspan="2">Nouveau thread</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$forum_thread field="title"}}</th>
          <td class="text">{{mb_field object=$forum_thread field="title"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$forum_thread field="body"}}</th>
          <td class="text">{{mb_field object=$forum_thread field="body"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $forum_thread->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le thread',objName:'{{$forum_thread->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
