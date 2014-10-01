module.exports = function(match) {
    match('',                   'posts#list');
    match('posts/:id',              'posts#view');


    match('users',       'users#list');
    match('users/:id',       'users#view');
    match('users/login',       'users#login');
    match('users/register',       'users#register');

    match('invite/create',       'invite#create');
};
