module.exports = {
  list: function(params, callback) {
    var spec = {
      collection: {collection: 'Posts', params: params}
    };

    this.app.fetch(spec, function(err, result) {
      callback(err, result);
    });
  },

  view: function(params, callback) {
    var spec = {
      model: {model: 'Repo', params: params, ensureKeys: ['language', 'watchers_count']},
      build: {model: 'Build', params: params}
    };
    this.app.fetch(spec, function(err, result) {
      callback(err, result);
    });
  }
};
