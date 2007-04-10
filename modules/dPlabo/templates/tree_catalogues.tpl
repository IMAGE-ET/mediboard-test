{{assign var="children" value=$_catalogue->_ref_catalogues_labo|@count}}
<div class="{{if $children}}expand{{/if}} header" id="catalogue-{{$_catalogue->_id}}-trigger" style="margin: 2px 0; height: 20px; border: 1px solid black">
  <button class="edit" onclick="window.redirect('?m={{$m}}&amp;tab=vw_edit_examens&amp;catalogue_labo_id={{$_catalogue->_id}}');" style="float:right">
    Examens
  </button>
  {{$_catalogue->identifiant}} &mdash;
  {{$_catalogue->libelle}}
  
</div>
{{if $children}}
<div class="content" id="catalogue-{{$_catalogue->_id}}" style="margin: 2px 0; border: 1px solid blue; padding-left: 10px;">
  {{foreach from=$_catalogue->_ref_catalogues_labo item="_catalogue"}}
  {{include file="tree_catalogues.tpl"}}
  {{/foreach}}
</div>
{{/if}}
