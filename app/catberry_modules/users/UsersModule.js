'use strict';

module.exports = UsersModule;

var util = require('util');

var USERS_URL = 'http://api.pt.tld/users',
    USERS_ITEM_URL = 'http://api.pt.tld/users/%d',
    USERS_LOGIN_URL = 'http://api.pt.tld/users/login',
    USERS_REG_URL = 'http://api.pt.tld/users',
    USERS_PAGE_URL_FORMAT = USERS_URL + '?offset=%d&limit=%d',
    PER_PAGE = 2;

/**
 * Creates new instance of Commits module.
 * @param {UHR} $uhr Universal HTTP(S) request.
 * @param {jQuery} $jQuery jQuery library.
 * @param {ServiceLocator} $serviceLocator Service locator to resolve plugin.
 * @constructor
 */
function UsersModule($uhr, $jQuery, $serviceLocator) {
    this._uhr = $uhr;
    this.$ = $jQuery;
    if (this.$context.isBrowser) {
        this.lazyLoader = $serviceLocator.resolve('lazyLoader');
        this.lazyLoader.containerId = 'users-feed';
        this.lazyLoader.loaderId = 'users-loader';
        this.lazyLoader.moreItemsCount = PER_PAGE;
        this.lazyLoader.maxItemsCount = 10000;
        this.lazyLoader.itemTemplateName = 'users__item';
        // factory to get next N items from data source
        this.lazyLoader.factory =
            UsersModule.prototype.itemsFactory.bind(this);
    }
}

/**
 * Current UHR instance.
 * @type {UHR}
 * @private
 */
UsersModule.prototype._uhr = null;

/**
 * Current jQuery instance.
 * @type {jQuery}
 */
UsersModule.prototype.$ = null;

/**
 * Current lazy loader for infinite scroll.
 * @type {LazyLoader}
 * @private
 */
UsersModule.prototype.lazyLoader = null;

/**
 * Current offset number.
 * @type {number}
 * @private
 */
UsersModule.prototype._offset = 0;

/**
 * Renders commit list of Catberry Framework repository.
 * This method is called when need to render "index" template
 * of module "commits".
 * @returns {Promise<Object>|Object|undefined} Data context.
 */
UsersModule.prototype.renderIndex = function () {
    this._offset = 0;

    return this.getItems(this._offset, PER_PAGE)
        .then(function (items) {
            return items;
        });
};

UsersModule.prototype.renderDetails = function () {
    return this._uhr.get(
        util.format(USERS_ITEM_URL, this.$context.state.id)
    ).then(function (result) {
        if (result.status.code >= 400 && result.status.code < 600) {
            throw new Error(result.status.text);
        }

        return result.content;
    });
};

UsersModule.prototype.renderLogin = function () {
    return;
};

UsersModule.prototype.submitLogin = function (event) {
    var self = this;
    this._uhr.post(USERS_LOGIN_URL, {data: {
        email: event.values.email,
        password: event.values.password
    }}).then(function(result) {
        self.$context.cookies.set({
            key: 'token',
            value: result.content.key,
            path: '/'
        });
        self.$context.redirect('/');
        return;
    });
};

UsersModule.prototype.handleLogout = function event() {
    this.$context.cookies.set({
        key: 'token',
        value: '',
        expire: new Date(0)
    });
    this.$context.redirect('/');
    return;
};

UsersModule.prototype.renderRegister = function () {
    return;
};

UsersModule.prototype.submitRegister = function (event) {
    var self = this;
    this._uhr.post(USERS_REG_URL, {data: {
        email: event.values.email,
        password: event.values.password,
        name: event.values.name,
        phone: event.values.phone,
        invite: event.values.invite
    }}).then(function(result) {
        //self.$context.cookies.set({
        //    key: 'token',
        //    value: result.content.key,
        //    path: '/'
        //});
        self.$context.redirect('/');
        return;
    });
};

/**
 * Does something after index placeholder is rendered.
 * This method is invoked only in browser.
 */
UsersModule.prototype.afterRenderIndex = function () {
    this.lazyLoader.enableInfiniteScroll();
};

/**
 * Current factory for feed items.
 * @param {jQuery} last Last element in feed.
 * @param {number} limit How many items to load.
 * @returns {Promise<Array>} Promise for next chunk of items.
 */
UsersModule.prototype.itemsFactory = function (last, limit) {
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
UsersModule.prototype.getItems = function (offset, limit) {
    return this._uhr.get(
        util.format(USERS_PAGE_URL_FORMAT, offset, limit)
    )
        .then(function (result) {
            if (result.status.code >= 400 && result.status.code < 600) {
                throw new Error(result.status.text);
            }

            return result.content;
        });
};