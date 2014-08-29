<!-- $Id: vw_idx_planning.tpl 7567 2009-12-09 17:03:37Z MyttO $ -->

<table class="tbl">
  <tr>
    <th>{{mb_title class=CColorLibelleSejour field=libelle}}</th>
    <th>Occurences</th>
    <th>{{mb_title class=CColorLibelleSejour field=color}}</th>
  </tr>

  {{foreach from=$libelle_counts key=libelle item=count name=color}}
    {{assign var=index value=$smarty.foreach.color.iteration}}
    <tr>
      <td class="text">{{$libelle}}</td>
      <td>{{$count}}</td>
      <td>
        {{assign var=libelle value=$libelle|upper}}
        {{assign var=color value=$colors.$libelle}}
        <form name="Edit-Color-{{$index}}" action="?" onsubmit="return onSubmitFormAjax(this);" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          {{mb_key object=$color}}
          {{mb_class object=$color}}

          {{mb_field object=$color field=libelle hidden=1}}
          {{mb_field object=$color field=color form="Edit-Color-$index" onchange="this.form.onsubmit()"}}
        </form>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">{{tr}}None{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>