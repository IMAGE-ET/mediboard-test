<script>
  {{assign var=end_app value="CMbPerformance::end"|static_call:""}}

  {{* Send timing data in HTTP header *}}
  {{assign var=timer value="CMbPerformance::out"|static_call:""}}
  {{assign var=request_uid value="CApp::getRequestUID"|static_call:""}}
  {{mb_default var=dosql value=''}}

  // Perfomance log
  (function(){
    if (MbPerformance.timingSupport) {
      var offset = (performance.timing.responseEnd - performance.timing.fetchStart);
      var serverTiming = {{$timer|@json}};
      var page = {
        m: "{{$m}}",
        a: "{{$dosql|ternary:$dosql:$action}}",
        id: 0,
        guid: "{{$request_uid}}"
      };
      MbPerformance.pageDetail = serverTiming;
      MbPerformance.logScriptEvent.defer("page", page, serverTiming, 0, offset);
    }
  })();
</script>

</body>
</html>