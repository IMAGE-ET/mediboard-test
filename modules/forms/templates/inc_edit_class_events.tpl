<script type="text/javascript">
  $$("a[href='#fields-events']")[0].set("count", {{$ex_class->_ref_events|@count}});
</script>

<table class="main layout">
  <tr>
    <td style="width: 20em;">
      <button type="button" class="new" style="float: right;" onclick="ExClassEvent.create({{$ex_class->_id}})">
        {{tr}}CExClassEvent-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClassEvent field=event_name}}</th>
          <th>{{tr}}CExClassEvent-back-constraints{{/tr}}</th>
        </tr>
        {{foreach from=$ex_class->_ref_events item=_event}}
          <tr data-event_id="{{$_event->_id}}" {{if $_event->disabled}} class="opacity-50" {{/if}}>
            <td class="text">
              <a href="#1" onclick="ExClassEvent.edit({{$_event->_id}}); return false;">
                {{$_event}}
              </a>
            </td>
            <td>{{$_event->_count.constraints}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2" class="empty">{{tr}}CExClassEvent.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="exClassEventEditor">
      <div class="small-info">
        Veuillez cliquer sur un évènement pour le modifier.
      </div>
    </td>
  </tr>
</table>