<div class="accordionMain" id="accordionConsult">
{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
  <div id="Acc{{$keyCat}}">
    <div id="Acc{{$keyCat}}Header" class="accordionTabTitleBar">
      {{$curr_listCat.name}} ({{$curr_listCat.file|@count}})
    </div>
    <div id="Acc{{$keyCat}}Content"  class="accordionTabContentBox">
      <table class="tbl">
        <tr>
          <td colspan="9">
            <form name="uploadFrm{{$keyCat}}" action="?m={{$m}}" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPfiles" />
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="cat_id" value="{{$keyCat}}" />
            <input type="hidden" name="file_class" value="{{$selClass}}" />
            <input type="hidden" name="file_object_id" value="{{$selKey}}" />
            <input type="hidden" name="file_category_id" value="{{$keyCat}}" />
            <label for="formfile">Ajouter un document</label>
            <input type="file" name="formfile" size="0" />
            <button class="submit" type="submit">Ajouter</button>
            </form>
          </td>
        </tr>
      {{counter start=0 skip=1 assign=curr_data}}
      {{foreach from=$curr_listCat.file item=curr_file}}
        {{if $curr_data is div by 2 || $curr_data==0}}
        <tr>
        {{/if}}
        <td class="halfPane button {{cycle name=cellicon values="dark, light"}}">
          <form name="editFile{{$curr_file->file_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
          <input type="hidden" name="m" value="dPfiles" />
          <input type="hidden" name="dosql" value="do_file_aed" />
          <input type="hidden" name="file_id" value="{{$curr_file->file_id}}" />
          <input type="hidden" name="del" value="0" />
          <a href="javascript:popFile({{$curr_file->file_id}},0);" title="Afficher le grand aper�u">
            <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$curr_file->file_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450" alt="-" border="0" />
            <br />{{$curr_file->_shortview}} {{$curr_file->_file_size}}<br />
            le {{$curr_file->file_date|date_format:"%d/%m/%Y � %Hh%M"}}
          </a>
          <br />
          D�placer vers <select name="file_category_id" onchange="submitFileChangt(this.form)">
            <option value="" {{if $curr_file->file_category_id == ""}}selected="selected"{{/if}}>&mdash; Aucune</option>
            {{foreach from=$listCategory item=curr_cat}}
            <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $curr_file->file_category_id}}selected="selected"{{/if}} >
              {{$curr_cat->nom}}
            </option>
            {{/foreach}}
          </select>
          <button type="button" class="trash" onclick="file_deleted={{$curr_file->file_id}};confirmDeletion(this.form, {typeName:'le fichier',objName:'{{$curr_file->file_name|escape:javascript}}',ajax:1,target:'systemMsg'},{onComplete:reloadListFile})">
            Supprimer ce fichier
          </button>
          </form>
        </td>
        {{if ($curr_data+1) is div by 2}}
        </tr>
        {{/if}}
        {{counter}}
      {{foreachelse}}
      <tr>
        <td colspan="2" class="button">
          Pas de documents            
        </td>
      </tr>
      {{/foreach}}
      </table>
    </div>
  </div>
{{/foreach}}      
</div>
<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), {
  panelHeight: fHeight, 
  onShowTab: storeKeyCat,
  onLoadShowTab: {{if $accordion_open}}{{$accordion_open}}{{else}}0{{/if}}
});
</script>