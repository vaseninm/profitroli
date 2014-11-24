'use strict';

module.exports = PostsModule;

var util = require('util');

var POSTS_URL = 'http://api.pt.tld/posts',
    POSTS_ITEM_URL = 'http://api.pt.tld/posts/%d',
    POSTS_PAGE_URL_FORMAT = POSTS_URL + '?offset=%d&limit=%d',
    PER_PAGE = 2;

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
        this.lazyLoader.moreItemsCount = PER_PAGE;
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

    return this.getItems(this._offset, PER_PAGE)
        .then(function (items) {
            return {posts: items};
        });
};

PostsModule.prototype.renderDetails = function () {
    return this._uhr.get(
        util.format(POSTS_ITEM_URL, this.$context.state.id)
    ).then(function (result) {
        if (result.status.code >= 400 && result.status.code < 600) {
            throw new Error(result.status.text);
        }

        return result.content;
    });
};

PostsModule.prototype.renderCreate = function () {
    return;
};

PostsModule.prototype.submitCreate = function (submitEvent) {
    var self = this;
    self._uhr.post(
        'http://api.pt.tld/posts?token=' + self.$context.cookies.get('token'), {
            data: {
                title: submitEvent.values.title,
                text: submitEvent.values.text
            }
        }
    ).then(function (result) {
            self.$context.redirect(util.format('/posts/%d', result.content.id));
            return;
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
    return this.getItems(this._offset + limit, PER_PAGE)
        .then(function (items) {
            self._offset += PER_PAGE;
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
        util.format(POSTS_PAGE_URL_FORMAT, offset, limit)
    )
        .then(function (result) {
            if (result.status.code >= 400 && result.status.code < 600) {
                throw new Error(result.status.text);
            }

            return result.content;
        });
};