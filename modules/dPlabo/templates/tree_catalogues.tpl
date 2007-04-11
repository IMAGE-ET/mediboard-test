{{* $Id$
  $_catalogue : catalog to display hierarchically
  $catalogue_id : selected catalog 
*}}

{{assign var="children" value=$_catalogue->_ref_catalogues_labo|@count}}
<div class="tree-header {{if $_catalogue->_id == $catalogue_id }}selected{{/if}}" >
  <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;catalogue_labo_id={{$_catalogue->_id}}" style="float:right;">
    {{$_catalogue->_ref_examens_labo|@count}} Examens
  </a>
  <div class="tree-trigger" id="catalogue-{{$_catalogue->_id}}-trigger">showHide</div>  
  <a href="#" onclick="Catalogue.select({{$_catalogue->_id}})">
  {{$_catalogue->_view}}
  </a>
</div>
{{if $children}}
<div class="tree-content" id="catalogue-{{$_catalogue->_id}}" style="display: block;">
  {{foreach from=$_catalogue->_ref_catalogues_labo item="_catalogue"}}
  {{include file="tree_catalogues.tpl"}}
  {{/foreach}}
</div>
{{/if}}