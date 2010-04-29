SejoursSSR = {
  filter: function(input) {
		var table = $("sejours-ssr");
		table.select("tr").invoke("show");
	  
	  var term = $V(input);
	  if (!term) return;
	  
	  table.select(".CPatient-view").each(function(e) {
	    if (!e.innerHTML.like(term)) {
	      e.up("tr").hide();
	    }
	  });
	}
}
