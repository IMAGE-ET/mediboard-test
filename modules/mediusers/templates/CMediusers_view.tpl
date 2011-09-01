{{include file=CMbObject_view.tpl}}

{{assign var=mediuser value=$object}}

<table class="tbl tooltip">
  
  <tr>
    <td class="button">
      {{mb_script module=dPpersonnel script=plage ajax=true}}
    	
      {{if isset($modules.dPpersonnel|smarty:nodefaults) && $modules.dPpersonnel->_can->edit}}
			  <button type="button" class="search" onclick="PlageConge.showForUser('{{$mediuser->_id}}');">
				  Congés
			  </button>
      {{/if}}
      {{if isset($modules.messagerie|smarty:nodefaults) && $modules.messagerie->_can->edit}}
        <a class="action" href="#nothing" onclick="MbMail.create('{{$mediuser->_id}}')">
          <button type="button">
            <img src="images/icons/mbmail.png" title="Envoyer un message" /> Message
          </button>
        </a>
      {{/if}}
    </td>
  </tr>
</table>

