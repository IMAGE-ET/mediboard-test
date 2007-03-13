{{include file="../../mediboard./templates/common.tpl"}}

<body onload="main()">

<div id="waitingMsgMask" class="chargementMask" style="display: none;"></div>
<div id="waitingMsgText" class="chargementText" style="display: none;">
  <table class="tbl">
    <tr>
      <th class="title">
        <div class="loading"><span id="waitingInnerMsgText">Chargement en cours</span></div>
      </th>
    </tr>
  </table>
</div>
{{if !$offline}}
<script type="text/javascript">
function popChgPwd() {
  var url = new Url;
  url.setModuleAction("admin", "chpwd");
  url.popup(400, 300, "ChangePassword");
}
</script>

{{if !$dialog}}

{{foreach from=$messages item=currMsg}}
  <div style='background: #aaa; color: #fff;'><strong>{{$currMsg->titre}}</strong> : {{$currMsg->corps}}</div>
{{/foreach}}

<table id="header" cellspacing="0"><!-- IE Hack: cellspacing should be useless --> 
  <tr>
    <td id="menubar">
      <table>
        <tr>
          {{foreach from=$affModule item=currModule}}    
          <td align='center' style='border-right: 1px solid #000'>
            <a href='?m={{$currModule.modName}}'>
              <strong>
                {{$currModule.modNameCourt}}
              </strong>
            </a>
          </td>
          {{/foreach}}
          <td id="new">
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td id="user">
      <table>
        <tr>
          <td id="userWelcome">
            <form name="ChangeGroup" action="" method="get">
              <span title="{{tr}}last connection{{/tr}} : {{$AppUI->user_last_login|date_format:"%A %d %B %Y %H:%M"}}">
                {{tr}}Welcome{{/tr}} {{$AppUI->user_first_name}} {{$AppUI->user_last_name}}
              </span>
              <input type="hidden" name="m" value="{{$m}}" />
              <select name="g" onchange="ChangeGroup.submit();">
                {{foreach from=$Etablissements item=currEtablissement key=keyEtablissement}}
                <option value="{{$keyEtablissement}}" {{if $keyEtablissement==$g}}selected="selected"{{/if}}>
                  {{$currEtablissement->_view}}
                </option>
                {{/foreach}}
              </select>
            </form>
          </td>      
          <td id="userMenu">
            {{$helpOnline|smarty:nodefaults}} |
            {{$suggestion|smarty:nodefaults}} |
            <a href="#" onclick="popChgPwd();">Changez votre mot de passe</a> |
            <a href="?m=mediusers&amp;a=edit_infos">{{tr}}My Info{{/tr}}</a> |
            <a href="./index.php?m=admin&amp;a=edit_prefs&amp;user_id={{$AppUI->user_id}}">{{tr}}Préférences{{/tr}}</a> |
            <a href="./index.php?logout=-1">{{tr}}Logout{{/tr}}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
{{/if}}
{{/if}}
<table id="main">
  <tr>
    <td>
      <div id="systemMsg">
        {{$errorMessage|nl2br|smarty:nodefaults}}
      </div>
      {{if !$dialog && !$offline}}
      <table class='titleblock'>
        <tr>
          {{if $titleBlockData.icon}}
          <td>
            {{$titleBlockData.icon|smarty:nodefaults}}
          </td>
          {{/if}}
          <td class="titlecell">
            {{tr}}{{$titleBlockData.name}}{{/tr}}
          </td>
        </tr>
      </table>
      {{/if}}