<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
  <input type="hidden" name="del" value="0" />

  {{mb_key object=$bilan}}
  {{mb_field object=$bilan field=sejour_id hidden=1}}

  <table class="form">
    <tr>
      <th class="category" colspan="2" style="width: 50%">Bilan d'entrée</th>
      <th class="category" colspan="2" style="width: 50%">Bilan de sortie</th>
    </tr>
		
    <tr>
      <th>{{mb_label object=$bilan field=kine}}</th>
      <td>{{mb_field object=$bilan field=kine tabindex=101 form=Edit-CBilanSSR}}</td>
      <th>{{mb_label object=$bilan field=sortie}}</th>
      <th>
        <select name="_helpers_sortie" size="1" style="width: 80px;" onchange="pasteHelperContent(this)">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{html_options options=$bilan->_aides.sortie.no_enum}}
        </select>
        <input type="hidden" name="_hidden_sortie" value="" />
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CBilanSSR', this.form._hidden_sortie, 'sortie')">
          {{tr}}New{{/tr}}
        </button>
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=ergo}}</th>
      <td>{{mb_field object=$bilan field=ergo tabindex=102 form=Edit-CBilanSSR}}</td>
      <td colspan="2" rowspan="10">{{mb_field object=$bilan field=sortie tabindex=109}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=psy}}</th>
      <td>{{mb_field object=$bilan field=psy tabindex=103 form=Edit-CBilanSSR}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=ortho}}</th>
      <td>{{mb_field object=$bilan field=ortho tabindex=104 form=Edit-CBilanSSR}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=diet}}</th>
      <td>{{mb_field object=$bilan field=diet tabindex=105 form=Edit-CBilanSSR}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=social}}</th>
      <td>{{mb_field object=$bilan field=social tabindex=106 form=Edit-CBilanSSR}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=apa}}</th>
      <td>{{mb_field object=$bilan field=apa tabindex=107 form=Edit-CBilanSSR}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=entree}}</th>
			<th>
        <select name="_helpers_entree" size="1" style="width: 80px;" onchange="pasteHelperContent(this)">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{html_options options=$bilan->_aides.entree.no_enum}}
        </select>
        <input type="hidden" name="_hidden_entree" value="" />
        <button class="new notext" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CBilanSSR', this.form._hidden_entree, 'entree')">
          {{tr}}New{{/tr}}
        </button>
			</th>
		</tr>
    <tr>
      <td colspan="2">{{mb_field object=$bilan field=entree tabindex=108}}</td>
    </tr>

    <tr>
      <td class="button" colspan="6">
        <button class="submit" type="submit">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>