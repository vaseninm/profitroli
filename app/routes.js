'use strict';
// jscs:disable maximumLineLength
// This file contains definitions of rules how location URLs are translated
// to "render" methods of catberry's modules.
//
// Format:
// /some/:parameter[module1,module2,module3]
//
// More details here:
// https://github.com/catberry/catberry/blob/master/docs/index.md#url-route-definition

module.exports = [
	'/:page[main, pages]',
	'/:page[main, pages]/:id[pages, posts, comments, users]',
	'/:page[main, pages]?query=:query[search]'
];