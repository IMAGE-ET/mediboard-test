<div>
<a href="?m=dPportail&amp;tab=vw_forumtheme">Liste des thèmes</a> > 
<a href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_theme_id={{$forum_theme->_id}}">{{$forum_theme->_view}}</a> >
<a href="?m=dPportail&amp;tab=vw_forummessage&amp;forum_theme_id={{$forum_theme->_id}}&amp;forum_thread_id={{$forum_thread->_id}}">{{$forum_thread->_view}}</a>
</div>

<table class="main">
  <tr>
    <td>
    
      <table class="tbl">
        <tr>
          <th class="title" colspan="2">{{$forum_thread->title}}</th>
        </tr>
        <tr>
          <td width="130">
          <a href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$forum_thread->_ref_user->_id}}" style="font-weight: bold;">{{$forum_thread->_ref_user->_view}}</a>
          {{$forum_thread->_ref_user->_ref_function->text}}<br />
          {{if $forum_thread->_ref_user->discipline_id}}
            {{$forum_thread->_ref_user->_ref_discipline->text}}<br />
          {{/if}}
          <a class="button edit" href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_thread_id={{$forum_thread->_id}}" title="Modifier le thread">Modifier</a>
          </td>
          <td style="vertical-align: top;">
            {{$forum_thread->date|date_format:"le %d/%m/%Y à %Hh%M"}}
            <hr />
            {{$forum_thread->body}}
            {{if $forum_thread->_ref_user->titres}}
              {{$forum_thread->_ref_user->titres}}<hr />
            {{/if}}
          </td>
        </tr>
        {{foreach from=$listMessages item=currMessage}}
        <tr {{if $currMessage->_id == $forum_message->_id}}class="selected"{{/if}}>
          <td>
          <a href="?m=mediusers&tab=vw_idx_mediusers&user_id={{$currMessage->_ref_user->_id}}" style="font-weight: bold;">{{$currMessage->_ref_user->_view}}</a>
          {{$currMessage->_ref_user->_ref_function->text}}<br />
          {{if $currMessage->_ref_user->discipline_id}}
            {{$currMessage->_ref_user->_ref_discipline->text}}<br />
          {{/if}}
          <a class="button edit" href="?m=dPportail&amp;tab=vw_forummessage&amp;forum_message_id={{$currMessage->_id}}" title="Modifier le message">Modifier</a>
          </td>
          <td style="vertical-align: top;">
            {{$currMessage->date|date_format:"le %d/%m/%Y à %Hh%M"}}
            <hr />
            {{$currMessage->body}}</td>
        </tr>
        {{/foreach}}
            
      </table>
      
      <a class="button new" href="?m=dPportail&amp;tab=vw_forummessage&amp;forum_thread_id={{$forum_thread->_id}}&amp;forum_theme_id={{$forum_theme->_id}}&amp;forum_message_id=0">Répondre</a><br />
    </td>
  </tr>
  <tr>
    <td>
      <form name="editmessage" action="?m=dPportail" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_forummessage_aed" />
      
      <input type="hidden" name="forum_message_id" value="{{$forum_message->_id}}" />
      <input type="hidden" name="forum_thread_id" value="{{$forum_thread->_id}}" />
      <input type="hidden" name="forum_theme_id" value="{{$forum_theme->_id}}" />
      
      {{mb_field object=$forum_message field="user_id" hidden="true"}}
      {{mb_field object=$forum_message field="date" hidden="true"}}
      
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $forum_message->_id}}
          <th class="title modify" colspan="2">Modification du message "{{$forum_message->_view}}"</th>
          {{else}}
          <th class="title" colspan="2">Nouveau message</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$forum_message field="body"}}</th>
          <td class="text">{{mb_field object=$forum_message field="body"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $forum_message->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le message',objName:'{{$forum_message->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
