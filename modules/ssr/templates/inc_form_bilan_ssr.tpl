<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_fiche_autonomie_aed" />
  <input type="hidden" name="del" value="0" />

  {{mb_key object=$bilan}}
  {{mb_field object=$bilan field=sejour_id hidden=1}}

  <table class="form">
    <tr>
      <th class="category" colspan="2" style="width: 50%">{{tr}}CBilanSSR-entree{{/tr}}</th>
      <th class="category" colspan="2" style="width: 50%">{{tr}}CBilanSSR-sortie{{/tr}}</th>
    </tr>
		
    <tr>
      <th>{{mb_label object=$bilan field=kine}}</th>
      <td>{{mb_field object=$bilan field=kine}}</td>
      <th colspan="2">{{mb_label object=$bilan field=sortie}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=ergo}}</th>
      <td>{{mb_field object=$bilan field=ergo}}</td>
      <td colspan="2" rowspan="10">{{mb_field object=$bilan field=sortie}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=psy}}</th>
      <td>{{mb_field object=$bilan field=psy}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=ortho}}</th>
      <td>{{mb_field object=$bilan field=ortho}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=diet}}</th>
      <td>{{mb_field object=$bilan field=diet}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=social}}</th>
      <td>{{mb_field object=$bilan field=social}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$bilan field=apa}}</th>
      <td>{{mb_field object=$bilan field=apa}}</td>
    </tr>

    <tr>
      <th colspan="2">{{mb_label object=$bilan field=entree}}</th>
		</tr>
    <tr>
      <td colspan="2">{{mb_field object=$bilan field=entree}}</td>
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