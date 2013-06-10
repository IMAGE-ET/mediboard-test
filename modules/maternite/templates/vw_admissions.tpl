<script type="text/javascript">
  Main.add(function() {
    Calendar.regField(getForm("changeDate").elements["date"], null, {noView: true});
  });
  
  editOperation = function(operation_id, sejour_id) {
    var url = new Url("dPplanningOp", "vw_edit_urgence");
    url.addParam("operation_id", operation_id);
    url.addParam("sejour_id", sejour_id);
    url.addParam("dialog", 1);
    url.modal({
      width: "95%",
      height: "95%",
      onClose: function(){ document.location.reload(); } // Laisser dans une fonction anonyme a cause de l'argument "period"
    });
  }
</script>
{{mb_script module=admissions    script=admissions}}
{{mb_script module=maternite     script=naissance}}
{{mb_script module=dPcompteRendu script=document}}
{{mb_script module=dPcompteRendu script=modele_selector}}
{{mb_script module=dPcabinet     script=file}}

<table class="main">
  <tr>
    <td style="width: 100px">
      <table class="tbl" style="text-align: center;">
        <tr>
          <th class="title" colspan="4">
            <a style="display: inline;" href="?m=maternite&tab=vw_admissions&date={{$prev_month}}">&lt;&lt;&lt;</a>
            {{$date|date_format:"%b %Y"}}
            <a style="display: inline;" href="?m=maternite&tab=vw_admissions&date={{$next_month}}">&gt;&gt;&gt;</a>
          </th>
        </tr>
        <tr>
          <th rowspan="2">Date</th>
        </tr>
        <tr>
          <th class="text">
            <a class="{{if $view == "all"}}selected{{else}}selectable{{/if}}" title="Toutes les admissions"
              href="?m=maternite&&tab=vw_idx_admission&view=all">
              Adm.
            </a>
          </th>
          <th class="text">
            <a class="{{if $view == "non_prep"}}selected{{else}}selectable{{/if}}" title="Admissions non préparées"
              href="?m=maternite&&tab=vw_idx_admission&view=non_prep">
              Non prép.
            </a>
          </th>
          <th class="text">
            <a class="{{if $view == "non_eff"}}selected{{else}}selectable{{/if}}" title="Admissions non effectuées"
              href="?m=maternite&&tab=vw_idx_admission&view=non_eff">
              Non eff.
            </a>
          </th>
        </tr>
      
        {{foreach from=$days key=day item=counts}}
        <tr {{if $day == $date}}class="selected"{{/if}}>
          {{assign var=day_number value=$day|date_format:"%w"}}
          <td align="right"
            {{if in_array($day, $bank_holidays)}}
              style="background-color: #fc0"
            {{elseif $day_number == '0' || $day_number == '6'}}
              style="background-color: #ccc;"
            {{/if}}>
            <a href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
              <strong>
                {{$day|date_format:"%a"|upper|substr:0:1}}
                {{$day|date_format:"%d"}}
              </strong>
            </a>
          </td>
          <td {{if $view == "all" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
          </td>
          <td {{if $view == "non_prep" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num3}}{{$counts.num3}}{{else}}-{{/if}}
          </td>
          <td {{if $view == "non_eff" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <table class="tbl" id="admissions">
        <tr>
          <th class="title" colspan="11">
            <strong>
              <a href="?m=maternite&tab=vw_admissions&date={{$date_before}}" style="display: inline;">&lt;&lt;&lt;</a>
              {{$date|date_format:$conf.longdate}}
              <form name="changeDate" method="get">
                <input type="hidden" name="m" value="maternite" />
                <input type="hidden" name="tab" value="vw_admissions" />
                <input type="hidden" name="date" value="{{$date}}" onchange="this.form.submit();"/>
                
              </form>
              <a href="?m=maternite&tab=vw_admissions&date={{$date_after}}" style="display: inline;">&gt;&gt;&gt;</a>
            </strong>
          </th>
        </tr>

        <tr>
          <th colspan="7" class="category">Parturiente</th>
          <th colspan="4" class="category">Enfants</th>
        </tr>
        
        <tr>
          <th class="category narrow">Admettre</th>
          <th class="category">{{tr}}CPatient{{/tr}}</th>
          <th class="narrow">
            <input type="text" size="3" onkeyup="Admissions.filter(this, 'admissions')" id="filter-patient-name" />
          </th>
          <th class="category">{{mb_label class=CSejour field=entree}}</th>
          <th class="category">Acc.</th>
          <th class="category narrow">Terme</th>
          <th class="category">Praticiens</th>
          
          <th class="category">Rangs / Heures</th>
          <th class="category">Enfants</th>
          <th class="category">Séjours</th>
          <th class="category"></th>
        </tr>
        {{foreach from=$sejours item=_sejour}}
        <tbody class="hoverable">
          {{assign var=grossesse value=$_sejour->_ref_grossesse}}
          {{assign var=patient   value=$_sejour->_ref_patient}}
          {{assign var=operation value=$_sejour->_ref_last_operation}}
          <tr>
            <td rowspan="{{$grossesse->_ref_naissances|@count}}">
              <form name="editSejour-{{$_sejour->_id}}" method="post">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_sejour_aed" />
                {{mb_key object=$_sejour}}
                {{mb_field object=$_sejour field=entree_reelle hidden=true}}
                {{if $_sejour->entree_reelle}}
                  <button type="button" onclick="$V(this.form.entree_reelle, ''); this.form.submit();" class="cancel">{{tr}}Cancel{{/tr}}</button>
                {{else}}
                  <button type="button" onclick="$V(this.form.entree_reelle, 'now'); this.form.submit();" class="tick">{{tr}}CSejour-admit{{/tr}}</button>
                {{/if}}
              </form>
            </td>
            <td colspan="2" rowspan="{{$grossesse->_ref_naissances|@count}}" class="text">
              <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$_sejour->_ref_patient}}</span>
            </td>
            <td rowspan="{{$grossesse->_ref_naissances|@count}}">
              <button class="print notext" onclick="Naissance.printDossier('{{$_sejour->_id}}')">
                {{tr}}Print{{/tr}}
              </button>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
                {{mb_value object=$_sejour field=entree date=$date}}
              </span>
            </td>
            {{if $_sejour->grossesse_id}}
              <td {{if $grossesse->_ref_naissances|@count}}rowspan="{{$grossesse->_ref_naissances|@count}}"{{/if}} class="button">
                {{if $operation->_id}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$operation->_guid}}')">
                    {{mb_value object=$operation field=time_operation}}
                  </span>
                  <button type="button" class="edit notext" onclick="editOperation('{{$operation->_id}}')"></button>
                {{elseif $_sejour->grossesse_id}}
                  <button type="button" class="add notext" onclick="editOperation(0, '{{$_sejour->_id}}')"></button>
                {{/if}}
              </td>
              <td rowspan="{{$grossesse->_ref_naissances|@count}}">
                <span onmouseover="ObjectTooltip.createEx(this, '{{$grossesse->_guid}}')">
                  {{$grossesse->terme_prevu|date_format:$conf.date}}
                </span>
              </td>
              <td class="text" rowspan="{{$grossesse->_ref_naissances|@count}}">
                {{foreach from=$grossesse->_praticiens item=_praticien}}
                  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_praticien}}
                {{/foreach}}
              </td>
              {{foreach from=$grossesse->_ref_naissances item=_naissance name=foreach_naissance}}
                {{assign var=sejour_enfant value=$_naissance->_ref_sejour_enfant}}
                {{assign var=enfant value=$sejour_enfant->_ref_patient}}
                {{if !$smarty.foreach.foreach_naissance.first}}
                  <tr>
                {{/if}}
                <td {{if !$_naissance->heure}}class="empty"{{/if}}>
                  <button class="edit notext" onclick="Naissance.edit('{{$_naissance->_id}}', null, null, 0, 'document.location.reload')">
                    {{tr}}Edit{{/tr}}
                  </button>
                  {{if $_naissance->heure}}
                    Le {{$enfant->naissance|date_format:$conf.date}} à {{$_naissance}}
                  {{else}}
                   {{$_naissance}}
                  {{/if}}
                </td>
                <td>
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$enfant->_guid}}')">
                    {{$enfant}}
                  </span>
                </td>
                
                <td>
                  <button class="print notext" onclick="Naissance.printDossier('{{$sejour_enfant->_id}}')">
                    {{tr}}Print{{/tr}}
                  </button>
                  {{assign var=sejour_enfant value=$_naissance->_ref_sejour_enfant}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour_enfant->_guid}}')">
                    {{mb_value object=$sejour_enfant field=entree date=$date}}
                  </span>
                </td>
                {{if $smarty.foreach.foreach_naissance.first}}
                  <td class="narrow" rowspan="{{$grossesse->_ref_naissances|@count}}">
                    <button type="button" class="add notext" title="Créer un dossier provisoire"
                      onclick="Naissance.edit(null, null, '{{$_sejour->_id}}', 1, 'document.location.reload')"></button>
                  </td>
                {{/if}}
                {{if $smarty.foreach.foreach_naissance.first}}
                  </tr>
                {{/if}}
              {{foreachelse}}
                <td colspan="3"></td>
                <td class="narrow" rowspan="{{$grossesse->_ref_naissances|@count}}">
                  <button type="button" class="add notext" title="Créer un dossier provisoire"
                    onclick="Naissance.edit(null, null, '{{$_sejour->_id}}', 1, 'document.location.reload')"></button>
                </td>
              {{/foreach}}
            {{else}}
              <td colspan="7"  rowspan="{{$grossesse->_ref_naissances|@count}}">
                <div class="small-info">
                  {{tr}}CGrossesse-no_link_sejour{{/tr}}
                </div>
              </td>
            {{/if}}
         </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="11" class="empty">{{tr}}CSejour.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>