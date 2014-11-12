'use strict';

module.exports = PostsModule;

var util = require('util');

var POSTS_URL = 'http://api.profitroli.tld/posts',
    POSTS_PAGINATOR_URL_FORMAT = POSTS_URL + '?offset=%d&limit=%d',
    LIMIT = 2;

/**
 * Creates new instance of Commits module.
 * @param {UHR} $uhr Universal HTTP(S) request.
 * @param {jQuery} $jQuery jQuery library.
 * @param {ServiceLocator} $serviceLocator Service locator to resolve plugin.
 * @constructor
 */
function PostsModule($uhr, $jQuery, $serviceLocator) {
    this._uhr = $uhr;
    this.$ = $jQuery;
    if (this.$context.isBrowser) {
        this.lazyLoader = $serviceLocator.resolve('lazyLoader');
        this.lazyLoader.containerId = 'posts-feed';
        this.lazyLoader.loaderId = 'posts-loader';
        this.lazyLoader.moreItemsCount = LIMIT;
        this.lazyLoader.maxItemsCount = 10000;
        this.lazyLoader.itemTemplateName = 'posts__item';
        // factory to get next N items from data source
        this.lazyLoader.factory =
            PostsModule.prototype.itemsFactory.bind(this);
    }
}

/**
 * Current UHR instance.
 * @type {UHR}
 * @private
 */
PostsModule.prototype._uhr = null;

/**
 * Current jQuery instance.
 * @type {jQuery}
 */
PostsModule.prototype.$ = null;

/**
 * Current lazy loader for infinite scroll.
 * @type {LazyLoader}
 * @private
 */
PostsModule.prototype.lazyLoader = null;

/**
 * Current offset number.
 * @type {number}
 * @private
 */
PostsModule.prototype._offset = 0;

/**
 * Renders commit list of Catberry Framework repository.
 * This method is called when need to render "index" template
 * of module "commits".
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
PostsModule.prototype.renderIndex = function () {
    this._offset = 0;

    return this.getItems(this._offset, LIMIT)
        .then(function (items) {
            return {posts: items};
        });
};

/**
 * Does something after index placeholder is rendered.
 * This method is invoked only in browser.
 */
PostsModule.prototype.afterRenderIndex = function () {
    this.lazyLoader.enableInfiniteScroll();
};

/**
 * Current factory for feed items.
 * @param {jQuery} last Last element in feed.
 * @param {number} limit How many items to load.
 * @returns {Promise<Array>} Promise for next chunk of items.
 */
PostsModule.prototype.itemsFactory = function (last, limit) {
    var self = this;
    return this.getItems(this._offset + limit, LIMIT)
        .then(function (items) {
            self._offset += LIMIT;
            return items;
        });
};

/**
 * Gets specified page of items.
 * @param {number} offset offset.
 * @param {number} limit Items count to load.
 * @returns {Promise<Array>} Promise for items.
 */
PostsModule.prototype.getItems = function (offset, limit) {
    return this._uhr.get(
        util.format(POSTS_PAGINATOR_URL_FORMAT, offset, limit)
    )
        .then(function (result) {
            if (result.status.code >= 400 && result.status.code < 600) {
                throw new Error(result.status.text);
            }

            return result.content;
        });
};

/**
 * Handles commit details hash change.
 * @param {Object} event Event object.
 * @returns {Promise|undefined} Promise for nothing.
 */
PostsModule.prototype.handleDetails = function (event) {
    if (event.isEnding) {
        this.$('#details-' + event.args.sha).remove();
        return;
    }

    var self = this,
        link = this.$('#' + event.args.sha);

    link.addClass('loading');

    return this._uhr.get(COMMITS_URL + '/' + event.args.sha)
        .then(function (result) {
            link.removeClass('loading');
            if (result.status.code >= 400 && result.status.code < 600) {
                throw new Error(result.status.text);
            }

            return self.$context.render(
                self.$context.name, 'details', result.content
            );

        }, function (reason) {
            link.removeClass('loading');
            throw reason;
        })
        .then(function (content) {
            self.$(content)
                .attr('id', 'details-' + event.args.sha)
                .insertAfter(link);
        });
};