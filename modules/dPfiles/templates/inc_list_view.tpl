            <div class="accordionMain" id="accordionConsult">
            {{foreach from=$listCategory item=curr_listCat}}
              <div id="{{$curr_listCat->nom}}">
                <div id="{{$curr_listCat->nom}}Header" class="accordionTabTitleBar">
                  {{$curr_listCat->nom}}
                </div>
                <div id="{{$curr_listCat->nom}}Content"  class="accordionTabContentBox">
                  <table class="tbl">
                  {{foreach from=$object->_ref_files item=curr_file}}
                    {{if $curr_file->file_category_id == $curr_listCat->file_category_id}}
                    <tr>
                      <td>
                        <a href="index.php?m={{$m}}&amp;file_id={{$curr_file->file_id}}">
                          <img src="mbfileviewer.php?file_id={{$curr_file->file_id}}&amp;phpThumb=1" alt="-" />
                        </a>
                      </td>
                      <td style="vertical-align: middle;">
                        {{$curr_file->_view}}<br />
                        {{$curr_file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}
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