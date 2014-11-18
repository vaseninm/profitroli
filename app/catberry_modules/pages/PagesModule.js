'use strict';

module.exports = PagesModule;

/**
 * Creates new instance of Pages module.
 * @constructor
 */
function PagesModule($uhr) {
    this._uhr = $uhr;
}

PagesModule.prototype._uhr = null;

/**
 * Renders page content.
 * This method is called when need to render index template of module pages.
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
PagesModule.prototype.renderIndex = function () {
    if (this.$context.state.id && parseInt(this.$context.state.id) == this.$context.state.id) {
        var placeholder = 'details';
    } else if (this.$context.state.id) {
        var placeholder = this.$context.state.id;
    } else {
        var placeholder = 'index';
    }
	return {
        page: this.$context.state.page,
        placeholder: placeholder
    };
};

/**
 * Renders page navigation tabs.
 * This method is called when need to render "navigation" template
 * of module "pages".
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
PagesModule.prototype.renderNavigation = function () {
	if (!this.$context.state.page) {
		this.$context.redirect('/posts');
		return;
	}
    var user, current;
    var self = this;

    return Promise.resolve({})
        .then(function(result) {
            result['current'] = {};
            result['current'][self.$context.state.page] = true;

            return result;
        })
        .then(function(result) {
            if (self.$context.cookies.get('token')) {
                return self._uhr.get(
                    'http://api.pt.tld/users/me?token=' + self.$context.cookies.get('token')
                ).then(function (getResult) {
                        result['user'] = getResult.content;
                        return result;
                    });
            } else {
                result['user'] = null;
                return result;
            }
        });
};