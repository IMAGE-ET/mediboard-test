
<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-owner', true);
});
</script>

<form name="selectPrat" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
        
<tr>
  <th class="category" colspan="10">Filtrer les modèles</th>
</tr>

<tr>
  <th>{{mb_label object=$filtre field=chir_id}}</th>
  <td>
    <select name="chir_id" onchange="submit()">
      <option value="">&mdash; Choisir un utilisateur</option>
      {{foreach from=$praticiens item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" 
        	{{if $curr_prat->_id == $filtre->chir_id}} selected="selected" {{/if}}
        >
          {{$curr_prat->_view}}
        </option>
      {{/foreach}}
    </select>
  </td>

  <th>{{mb_label object=$filtre field=object_class}}</th>
  <td>{{mb_field object=$filtre field=object_class onchange="this.form.submit()" canNull=true defaultOption="&mdash; Tous les objets"}}</td>

  <th>{{mb_label object=$filtre field=type}}</th>
  <td>{{mb_field object=$filtre field=type onchange="this.form.submit()" canNull=true defaultOption="&mdash; Tous les types"}}</td>
</tr>

</table>

</form>

<ul id="tabs-owner" class="control_tabs">
  <li><a href="#owner-prat">{{$user->_view}} <small>({{$modeles.prat|@count}})</small></a></li>
  <li><a href="#owner-func">{{$user->_ref_function->_view}} <small>({{$modeles.func|@count}})</small></a></li>
  <li><a href="#owner-etab">{{$user->_ref_function->_ref_group->_view}} <small>({{$modeles.etab|@count}})</small></a></li>
</ul>
<hr class="control_tabs" />

<div id="owner-prat" style="display: none;">
{{include file=inc_modeles.tpl modeles=$modeles.prat}}
</div>

<div id="owner-func" style="display: none;">
{{include file=inc_modeles.tpl modeles=$modeles.func}}
</div>

<div id="owner-etab" style="display: none;">
{{include file=inc_modeles.tpl modeles=$modeles.etab}}
</div>
