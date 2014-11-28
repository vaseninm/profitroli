'use strict';

module.exports = CommentsModule;

var util = require('util');

var PER_PAGE = 2; //@todo не зыбыть поставить адекватное значение что бы спамить

/**
 * Creates new instance of Commits module.
 * @param {UHR} $uhr Universal HTTP(S) request.
 * @param {jQuery} $jQuery jQuery library.
 * @param {ServiceLocator} $serviceLocator Service locator to resolve plugin.
 * @constructor
 */
function CommentsModule($uhr, $jQuery, $config, $serviceLocator) {
    this._uhr = $uhr;
    this._config = $config;
    this.$ = $jQuery;
    if (this.$context.isBrowser) {
        this.lazyLoader = $serviceLocator.resolve('lazyLoader');
        this.lazyLoader.containerId = 'comments-feed';
        this.lazyLoader.loaderId = 'comments-loader';
        this.lazyLoader.moreItemsCount = PER_PAGE;
        this.lazyLoader.maxItemsCount = 10000;
        this.lazyLoader.itemTemplateName = 'comments__item';
        // factory to get next N items from data source
        this.lazyLoader.factory =
            CommentsModule.prototype.itemsFactory.bind(this);
    }
}

/**
 * Current UHR instance.
 * @type {UHR}
 * @private
 */
CommentsModule.prototype._uhr = null;


CommentsModule.prototype._config = null;

/**
 * Current jQuery instance.
 * @type {jQuery}
 */
CommentsModule.prototype.$ = null;

/**
 * Current lazy loader for infinite scroll.
 * @type {LazyLoader}
 * @private
 */
CommentsModule.prototype.lazyLoader = null;

/**
 * Current offset number.
 * @type {number}
 * @private
 */
CommentsModule.prototype._offset = 0;

/**
 * Renders commit list of Catberry Framework repository.
 * This method is called when need to render "index" template
 * of module "commits".
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
CommentsModule.prototype.renderIndex = function () {
    this._offset = 0;
    var self = this;

    return this.getItems(this._offset, PER_PAGE)
        .then(function (items) {
            self._offset += items.length;
            return {posts: items};
        });
};

CommentsModule.prototype.renderCreate = function () {
    return {
        me: this.$context.renderedData.pages.navigation.user
    };
};

CommentsModule.prototype.submitCreate = function (submitEvent) {
    var self = this;
    self._uhr.post(
        this._config.rest + util.format('/posts/%d/comments?token=%s', self.$context.state.id, self.$context.cookies.get('token')),
        {
            data: {
                text: submitEvent.values.text
            }
        }
    ).then(function (result) {
            if (result.status.code === 201) {
                self.$('#comment-error-form').hide();
                submitEvent.element.context.reset();
                return self.lazyLoader.loadChunk(PER_PAGE);
            } else {
                self.$('#comment-error-form').text(result.content.message).show();
            }
        });
};


/**
 * Does something after index placeholder is rendered.
 * This method is invoked only in browser.
 */
CommentsModule.prototype.afterRenderIndex = function () {
    this.lazyLoader.enableInfiniteScroll();
};

/**
 * Current factory for feed items.
 * @param {jQuery} last Last element in feed.
 * @param {number} limit How many items to load.
 * @returns {Promise<Array>} Promise for next chunk of items.
 */
CommentsModule.prototype.itemsFactory = function (last, limit) {
    var self = this;
    return this.getItems(this._offset, PER_PAGE)
        .then(function (items) {
            if (PER_PAGE > items.length) {
                $('#comments_create').show();
            }
            self._offset += items.length;
            return items;
        });
};

/**
 * Gets specified page of items.
 * @param {number} offset offset.
 * @param {number} limit Items count to load.
 * @returns {Promise<Array>} Promise for items.
 */
CommentsModule.prototype.getItems = function (offset, limit) {
    return this._uhr.get(
        this._config.rest + util.format('/posts/%d/comments?offset=%d&limit=%d', this.$context.state.id, offset, limit)
    ).then(function (result) {
            if (result.status.code !== 200) {
                throw new Error(result.content.message);
            }

            return result.content;
        });
};