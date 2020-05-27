<?php

declare(strict_types=1);

namespace Action;

use \Http\Response;

abstract class BaseAction
{
    /**
     * Request and response instance variables
     */
    protected $request;
    protected $response;

    const ALLOW = [];
    const DENY = [];

    /**
     * Action constructor
     * 
     * This takes an Http\Request as its parameter, and initializes a new
     * Http\Response as well, which inherited class functions can return.
     *
     * @param Http\Request $request
     */
    public function __construct(\Http\Request $request)
    {
        $this->request = $request;
        $this->response = new Response();
    }

    /**
     * Render a template to the response
     * 
     * This is a convenience action that creates a new template object and
     * calls its render action with the supplied arguments, then sets the
     * response body to the result.
     *
     * @param string $file
     * @param array $args
     * @return Http\Response
     */
    protected function renderResponse(string $file, array $args=[]) : Response
    {
        $template = new \Template();
        $class = explode('\\', static::class);
        $file = end($class) . '/' . $file;
        return $this->response->setBody($template->render($file, $args));
    }
    
    /**
    * Render a template with a form to the response
    * 
    * This is another convenience action that verifies an already-submitted
    * form has a valid CSRF token, then adds the form to the argument list
    * before calling renderResponse.
    *
    * @param string $file
    * @param array $args
    * @param string $formvar
    * @return Http\Response
    */
    protected function renderFormResponse(
        string $file, \Form $f, array $args=[], $formvar='f'
    ) : Response
    {
        if ($f->validateToken() == false) {
            return $this->response->setStatus(403);
        }
        $args = $args + [$formvar => $f];
        return $this->renderResponse($file, $args);
    }

    /**
     * Check if access to an action is allowed. This method is called after
     * the route has been resolved but before the action method is called,
     * and returns TRUE if the user should be allowed to take the action.
     * The BaseAction implementation checks the method against a list of
     * methods allowed to logged-in users or a list of methods denied to them,
     * with the allow list taking precedence if both are defined. This method
     * could be extended in a child Action class to perform other checks,
     * like an administration role.
     *
     * @param string $method
     * @return boolean
     */
    public function denyAccess(string $method) : bool
    {
        if (!empty($this::ALLOW)) {
            if (!in_array($method, $this::ALLOW) && !isset($_SESSION['user'])) {
                return true;
            }
        }
        if (!empty($this::DENY)) {
            if (in_array($method, $this::DENY) && !isset($_SESSION['user'])) {
                return true;
            }
        }
        return false;
    }
}
