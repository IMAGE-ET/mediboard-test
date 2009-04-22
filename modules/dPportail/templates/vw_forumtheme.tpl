<div>
<a href="?m=dPportail&amp;tab=vw_forumtheme">Liste des thèmes</a>
</div>

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th colspan="4" class="title">Th&egrave;mes</th>
        </tr>
        {{foreach from=$listThemes item=currTheme}}
        <tr {{if $currTheme->_id == $forum_theme->_id}}class="selected"{{/if}}>
          <td><a href="?m=dPportail&amp;tab=vw_forumthread&amp;forum_theme_id={{$currTheme->_id}}" style="font-weight:bold;">{{$currTheme->title}}</a><br />{{$currTheme->desc}}</td>
          <td>{{$currTheme->_ref_forum_threads|@count}} thread{{if $currTheme->_ref_forum_threads|@count > 1}}s{{/if}}</td>
          <td>
            <a class="button edit" href="?m=dPportail&amp;tab=vw_forumtheme&amp;forum_theme_id={{$currTheme->_id}}">Modifier</a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      <a class="button new" href="?m=dPportail&amp;tab=vw_forumtheme&amp;forum_theme_id=0">Creer un nouveau theme</a>
    </td>
    <td class="halfPane">
      <form name="edittheme" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_forumtheme_aed" />
      <input type="hidden" name="forum_theme_id" value="{{$forum_theme->_id}}" />
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $forum_theme->_id}}
          <th class="title modify" colspan="2">Modification du thème "{{$forum_theme->_view}}"</th>
          {{else}}
          <th class="title" colspan="2">Nouveau thème</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$forum_theme field="title"}}</th>
          <td class="text">{{mb_field object=$forum_theme field="title"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$forum_theme field="desc"}}</th>
          <td class="text">{{mb_field object=$forum_theme field="desc"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $forum_theme->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le theme',objName:'{{$forum_theme->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
