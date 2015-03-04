<!--  $Id: vw_idx_listes.tpl 12241 2011-05-20 10:29:53Z flaviencrochard $ -->

<script>
  Main.add(Control.Tabs.create.curry("tabs-owner", true));
</script>   

<ul id="tabs-owner" class="control_tabs">
  {{foreach from=$listes key=owner item=_listes}}
  <li>
    <a href="#owner-{{$owner}}" {{if !$_listes|@count}}class="empty"{{/if}}>
      {{$owners.$owner}} 
      <small>({{$_listes|@count}})</small>
    </a>
   </li>
  {{/foreach}}
</ul>

<table class="tbl">
  <tr>
    <th>{{mb_title class=CListeChoix field=nom}}</th>
    <th>{{mb_title class=CListeChoix field=valeurs}}</th>
    <th>{{mb_title class=CListeChoix field=compte_rendu_id}}</th>
  </tr>

  {{foreach from=$listes key=owner item=_listes}}
    <tbody id="owner-{{$owner}}" style="display: none;">
      {{if $can->edit}}
        <tr>
          <td colspan="3" class="button">
            {{assign var=owner_object value=$owners.$owner}}
            {{assign var=ids value=$_listes|@array_keys}}

            <button type="button" class="hslip" onclick="ListeChoix.exportCSV('{{$owner_object->_guid}}', '{{'-'|implode:$ids}}');">
              {{tr}}Export-CSV{{/tr}}
            </button>

            <button type="button" class="hslip" onclick="ListeChoix.importCSV('{{$owner_object->_guid}}');">
              {{tr}}Import-CSV{{/tr}}
            </button>
          </td>
        </tr>
      {{/if}}
      {{foreach from=$_listes item=_liste}}
        <tr {{if $_liste->_id == $liste_id}} class="selected" {{/if}}>
          <td class="text">
            <button class="edit notext"onclick="ListeChoix.edit('{{$_liste->_id}}');">
              {{tr}}Edit{{/tr}}
            </button>
            {{mb_value object=$_liste field=nom}}
          </td>
          <td>
            {{foreach from=$_liste->_valeurs item=_valeur name=valeurs}}
              {{if $smarty.foreach.valeurs.index < 5}}
                <div class="compact">{{$_valeur|spancate:60}}</div>
              {{/if}}
            {{foreachelse}}
            <div class="empty">{{tr}}{{$_liste->_class}}.novalues{{/tr}}</div>
            {{/foreach}}
            {{if $_liste->_valeurs|@count > 5}}
              <div class="compact">
                <strong>
                  + {{math equation="x-5" x=$_liste->_valeurs|@count}} {{tr}}others{{/tr}}
                </strong>
              </div>
            {{/if}}
          </td>
          <td class="text">
            {{assign var=modele value=$_liste->_ref_modele}}
            {{if $modele->_id}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$modele->_guid}}');">
                [{{tr}}{{$modele->object_class}}{{/tr}}] {{$modele->nom}}
              </span>
            {{else}}
              &mdash; {{tr}}All{{/tr}}
            {{/if}}
          </td>
        </tr>
      {{foreachelse}}
        <tr>
          <td colspan="10" class="empty">{{tr}}CListeChoix.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    </tbody>
  {{/foreach}}
</table>
