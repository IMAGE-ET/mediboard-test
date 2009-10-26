<!-- $Id$ -->

<table class="main">
  <tr>
    <td colspan="2">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_protocole&amp;protocole_id=0">Créer un nouveau protocole</a>
          
      <form name="selectFrm" action="?" method="get">
      
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" {{if $dialog}} name="a" {{else}} name="tab" {{/if}} value="vw_protocoles" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />

      <table class="form">
        <tr>
          <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien</label></th>
          <td>
            <select name="chir_id" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les praticiens</option>
              {{foreach from=$listPrat item=curr_prat}}
              {{if $curr_prat->_ref_protocoles|@count}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
                {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
              </option>
              {{/if}}
              {{/foreach}}
            </select>
          </td>
          <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM</label></th>
          <td>
            <select name="code_ccam" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les codes</option>
              {{foreach from=$listCodes|smarty:nodefaults key=curr_code item=code_nomber}}
              <option value="{{$curr_code}}" {{if $code_ccam == $curr_code}} selected="selected" {{/if}}>
                {{$curr_code}} ({{$code_nomber}})
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>    
      
    </td>
  </tr>

  <tr>
    <td>
		{{include file=inc_list_protocoles.tpl}}
    </td>
    {{if $protSel->_id && !$dialog}}
    <td class="halfPane">
      <br />
			{{include file=inc_details_protocole.tpl}}
    </td>
    {{/if}} 
  </tr>
</table>