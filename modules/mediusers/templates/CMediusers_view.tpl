{{include file=CMbObject_view.tpl}}

{{assign var=mediuser value=$object}}

<table class="tbl tooltip">
  
  <tr>
    <td class="button">
      {{mb_include_script module=dPpersonnel script=plage ajax=true}}
    	
      {{if isset($modules.dPpersonnel|smarty:nodefaults) && $modules.dPpersonnel->_can->edit}}
			  <button type="button" class="search" onclick="PlageConge.showForUser('{{$mediuser->_id}}');">
				  Congés
			  </button>
      {{/if}}
    </td>
  </tr>
</table>

