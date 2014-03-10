<table class="form">
  <col style="width: 50%"/>
  {{assign var="class" value="CCompteRendu"}}
  <tr>
    <th class="category" colspan="2">
      {{tr}}config-dPcompteRendu-CCompteRendu-acces{{/tr}}
    </th>
    {{assign var="var" value="access_group"}}
    {{mb_include module=system template=inc_config_bool}}
    {{assign var="var" value="access_function"}}
    {{mb_include module=system template=inc_config_bool}}
  </tr>
  {{assign var="class" value="CAideSaisie"}}
  <tr>
    <th class="category" colspan="2">
      {{tr}}config-dPcompteRendu-CAideSaisie-acces{{/tr}}
    </th>
    {{assign var="var" value="access_group"}}
    {{mb_include module=system template=inc_config_bool}}
    {{assign var="var" value="access_function"}}
    {{mb_include module=system template=inc_config_bool}}
  </tr>
  {{assign var="class" value="CListeChoix"}}
  <tr>
    <th class="category" colspan="2">
      {{tr}}config-dPcompteRendu-CListeChoix-acces{{/tr}}
    </th>
    {{assign var="var" value="access_group"}}
    {{mb_include module=system template=inc_config_bool}}
    {{assign var="var" value="access_function"}}
    {{mb_include module=system template=inc_config_bool}}
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>