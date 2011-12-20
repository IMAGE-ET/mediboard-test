CodeSniffer = {
	run: function(button) {
		this.show(button);
	},

  show: function(button) {
    var basename = $(button).up('.tree-header').down('.basename').textContent.trim();
    var file = '';
    if (basename != 'mediboard') {
      var dir = $(button).up('.tree-content').id;
      file = dir.replace(':', '/') + '/' + basename.strip();
      file = file.split('/').slice(1).join('/');
    }
    
		new Url('dPdeveloppement', 'sniff_file') .
		  addParam("file", file) .
			requestModal(800, 400);
  }
}
