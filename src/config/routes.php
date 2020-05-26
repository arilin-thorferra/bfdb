<?php

/**
 * Routes are defined with a matching regex on the left side (the key) and a
 * tuple of the Action Class and the class method on the right (the value).
 * The actual method in the class is the HTTP method, an underscore, and the
 * method here, so the same route could be used to call different get_ and
 * post_ methods. Capture groups in the route regex will be passed to the
 * method as an argument array.
 */

return [
    '/'                     => ['Index', 'index'],
    '/about'                => ['Index', 'about'],

    '/login'                => ['Session', 'login'],
    '/logout'               => ['Session', 'logout'],

    '/register'             => ['User', 'create'],
    '/account'              => ['User', 'show'],
    '/account/edit'         => ['User', 'edit'],
    '/account/delete'       => ['User', 'delete'],

    '/c/find'               => ['Character', 'search'],
    '/c/list'               => ['Character', 'list'],
    '/c/new/'               => ['Character', 'create'],
    '/c/show/(.+)'          => ['Character', 'show'],
    '/c/edit/(.+)'          => ['Character', 'edit'],
    '/c/delete/(.+)'        => ['Character', 'delete']
];
 