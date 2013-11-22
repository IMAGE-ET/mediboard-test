RegressionChecker = {
  getFile: function (element) {
    var header = element.up('.tree-header');
    var path = header.id.match(/files-modules:(.*)-header/)[1];
    return path.replace(/:/g, '/');
  },

  run: function(button) {
    alert('run this : ' + this.getFile(button));
  },
  show: function(button) {
    new Url('developpement', 'regression_view_check') .
      addParam('file', this.getFile(button)) .
      requestModal(-200, -100);
  }
};
