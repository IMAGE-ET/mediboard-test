{{mb_default var=prefix value=modal}}

<button 
  type="button" 
  class="new" 
  onclick="Modal.open('{{$prefix}}-regimes', { closeOnClick: $('{{$prefix}}-regimes').down('button.tick') } );"
>
  Régime alimentaire
</button>

{{assign var=fields value="-"|explode:"hormone_croissance-repas_sans_sel-repas_sans_porc-repas_diabete-repas_sans_residu"}}

<script type="text/javascript">
  checkRegimes{{$prefix}} = function() {
    var form = getForm("editSejour");
    var formEasy = getForm("editOpEasy");
    
    {{foreach from=$fields item=_field}}
      if (formEasy) {
        {{if $prefix == "expert"}}
          var valeur = $V(form.{{$_field}});
          $V(formEasy.{{$_field}}, valeur, false);
        {{else}}
          var valeur = $V(formEasy.{{$_field}});
          $V(form.{{$_field}}, valeur);
        {{/if}}
      }
    {{/foreach}}
  }
</script>

<table id="{{$prefix}}-regimes" style="display: none;">
  <tr>
    <th class="category" colspan="2">Régimes alimentaires</th>
  </tr>
  {{foreach from=$fields item=_field}}
  <tr>
    <th>{{mb_label object=$sejour field=$_field}}</th>
    <td>{{mb_field object=$sejour field=$_field}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <td class="button" colspan="2">
      <button class="tick" type="button" onclick="checkRegimes{{$prefix}}()">{{tr}}Validate{{/tr}}</button>
    </td>
  </tr>
</table>
