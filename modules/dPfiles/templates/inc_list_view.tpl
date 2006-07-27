<div class="accordionMain" id="accordionConsult">
{{foreach from=$listCategory item=curr_listCat}}
  <div id="Acc{{$curr_listCat->file_category_id}}">
    <div id="Acc{{$curr_listCat->file_category_id}}Header" class="accordionTabTitleBar">
      {{$curr_listCat->nom}}
    </div>
    <div id="Acc{{$curr_listCat->file_category_id}}Content"  class="accordionTabContentBox">
      <table class="tbl">
      {{foreach from=$object->_ref_files item=curr_file}}
        {{if $curr_file->file_category_id == $curr_listCat->file_category_id}}
        <tr>
          <td class="{{cycle name=cellicon values="dark, light"}}">
            <a href="javascript:ZoomFileAjax({{$curr_file->file_id}});">
              <img src="mbfileviewer.php?file_id={{$curr_file->file_id}}&amp;phpThumb=1" alt="-" />
            </a>
          </td>
          <td class="text {{cycle name=celltxt values="dark, light"}}" style="vertical-align: middle;">
            {{$curr_file->_shortview}}<br />
            {{$curr_file->_file_size}}<br />
            le {{$curr_file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}
          </td>
          <td class="button {{cycle name=cellform values="dark, light"}}">
            <form name="editFile{{$curr_file->file_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPfiles" />
              <input type="hidden" name="dosql" value="do_file_aed" />
              <input type="hidden" name="file_id" value="{{$curr_file->file_id}}" />
              <input type="hidden" name="del" value="0" />
              <select name="file_category_id" onchange="submitFileChangt(this.form)">
                {{foreach from=$listCategory item=curr_cat}}
                <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $curr_file->file_category_id}}selected="selected"{{/if}} >
                  {{$curr_cat->nom}}
                </option>
                {{/foreach}}
              </select>
              <br />
              <button type="button" class="trash" onclick="confirmDeletion(this.form, {typeName:'le fichier',objName:'{{$curr_file->file_name|escape:javascript}}',ajax:1,target:'systemMsg'},{onComplete:reloadListFile})">
                Supprimer
              </button>
            </form>
          </td>
        </tr>
        {{/if}}
      {{/foreach}}
      </table>
    </div>
  </div>
{{/foreach}}            
</div>
<script language="Javascript" type="text/javascript">
new Rico.Accordion( $('accordionConsult'), {panelHeight:350} );
</script>