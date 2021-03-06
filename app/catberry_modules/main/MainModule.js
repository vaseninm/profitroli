'use strict';

module.exports = MainModule;

var SUBTITLES = {
	posts: 'Новости',
	users: 'Пользователи'
};

/**
 * Creates new instance of main module.
 * @param {string} title Site title.
 * @constructor
 */
function MainModule($config) {
	this._title = $config.title;
}

/**
 * Current site title.
 * @type {string}
 * @private
 */
MainModule.prototype._title = '';

/**
 * Renders HEAD element of page.
 * This method is called when need to render "head" template of module "main".
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
MainModule.prototype.renderHead = function () {
	return {
		title: this._title,
		subtitle: SUBTITLES[this.$context.state.page]
	};
};
