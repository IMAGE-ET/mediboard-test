<script type="text/javascript">

PairEffect.initGroup('tree-content');
  
</script>

<form name="editCatalogue" id="currCatalogue" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPlabo" />
  <input type="hidden" name="dosql" value="do_catalogue_aed" />
  <input type="hidden" name="catalogue_labo_id" value="" />
  <input type="hidden" name="del" value="0" />
</form>


{{assign var="catalogue_id" value=$catalogue->_id}}
{{foreach from=$listCatalogues item="_catalogue"}}
{{include file="tree_catalogues.tpl"}}
{{/foreach}}

{{if $catalogue->_id}}
<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      <a style="float:right;" href="#nothing" onclick="view_log('{{$catalogue->_class_name}}', {{$catalogue->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </a>
      {{$catalogue->_view}}
    </th>
  </tr>
  <tr>
    <th class="category">Examen</th>
    <th class="category">Type</th>
    <th class="category">Unité</th>
    <th class="category">Min</th>
    <th class="category">Max</th>
  </tr>
  {{foreach from=$catalogue->_ref_examens_labo item="curr_examen"}}
  <tr>
    <td>
      <div class="draggable" id="examen-{{$curr_examen->_id}}">
      <script type="text/javascript">
        new Draggable('examen-{{$curr_examen->_id}}', oDragOptions);
      </script>
      {{$curr_examen->_view}}
      </div>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->type}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->min}} {{$curr_examen->unite}}
      </a>
    </td>
    <td>
      <a href="?m={{$m}}&amp;tab=vw_edit_examens&amp;examen_labo_id={{$curr_examen->_id}}">
        {{$curr_examen->max}} {{$curr_examen->unite}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>
{{/if}}