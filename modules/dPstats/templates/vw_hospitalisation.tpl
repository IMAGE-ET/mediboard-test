<table class="main">
  <tr>
    <td>
      <form name="hospitalisation" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      
      <table class="form">
        <tr>
          <th colspan="4" class="category">Occupation des lits</th>
        </tr>

        <tr>
          <td>{{mb_label object=$filter field="_date_min_stat"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_min_stat" form="hospitalisation" canNull="false" register=true}} </td>

          <td>{{mb_label object=$filter field="_service"}}</td>
          <td>
            <select name="service_id">
              <option value="0">&mdash; Tous les services</option>
              {{foreach from=$listServices item=curr_service}}
              <option value="{{$curr_service->service_id}}" {{if $curr_service->service_id == $filter->_service}}selected="selected"{{/if}}>
                {{$curr_service->nom}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <td>{{mb_label object=$filter field="_date_max_stat"}}</td>
          <td class="date">{{mb_field object=$filter field="_date_max_stat" form="hospitalisation" canNull="false" register=true}} </td>

          <td>{{mb_label object=$filter field="praticien_id"}}</td>
          <td>
            <select name="prat_id">
              <option value="0">&mdash; Tous les praticiens</option>
              {{foreach from=$listPrats item=curr_prat}}
              <option value="{{$curr_prat->user_id}}" {{if $curr_prat->user_id == $filter->praticien_id}}selected="selected"{{/if}}>
                {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <td>{{mb_label object=$filter field="type"}}</td>
          <td>
            <select name="type">
              <option value="">&mdash; Tous les types d'hospi</option>
              <option value="1" {{if $filter->type == "1"}}selected="selected"{{/if}}>Hospi complètes + ambu</option>
              {{foreach from=$filter->_specs.type->_locales key=key_hospi item=curr_hospi}}
              <option value="{{$key_hospi}}" {{if $key_hospi == $filter->type}}selected="selected"{{/if}}>
                {{$curr_hospi}}
              </option>
              {{/foreach}}
            </select>
          </td>
         
          <td>{{mb_label object=$filter field="_specialite"}}</td>
          <td>
            <select name="discipline_id">
              <option value="0">&mdash; Toutes les spécialités</option>
              {{foreach from=$listDisciplines item=curr_disc}}
              <option value="{{$curr_disc->discipline_id}}" {{if $curr_disc->discipline_id == $filter->_specialite }}selected="selected"{{/if}}>
                {{$curr_disc->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <td colspan="4" class="button"><button type="submit" class="search">Afficher</button></td>
        </tr>
        
        <tr>
          <td colspan="4" class="button">
            <img alt="Patients par service" src='?m=dPstats&amp;a=graph_patparservice&amp;suppressHeaders=1&amp;debut={{$filter->_date_min_stat}}&amp;fin={{$filter->_date_max_stat}}&amp;service_id={{$filter->_service}}&amp;prat_id={{$filter->praticien_id}}&amp;type_adm={{$filter->type}}&amp;discipline_id={{$filter->_specialite}}' />
            <img alt="Admissions par type d'hospitalisation" src='?m=dPstats&amp;a=graph_patpartypehospi&amp;suppressHeaders=1&amp;debut={{$filter->_date_min_stat}}&amp;fin={{$filter->_date_max_stat}}&amp;service_id={{$filter->_service}}&amp;prat_id={{$filter->praticien_id}}&amp;type_adm={{$filter->type}}&amp;discipline_id={{$filter->_specialite}}' />
          </td>
        </tr>
        
      </table>
      
      </form>
    </td>
  </tr>
</table>