<?php

declare(strict_types=1);

namespace Bfdb\Action;

use Bfdb\Http\Request;
use Bfdb\Http\Response;
use Bfdb\Template;
use Bfdb\Form;

abstract class BaseAction
{
    /**
     * Request and response instance variables
     */
    protected $request;
    protected $response;

    /**
     * Whitelist: methods in this array can be used by non-logged-in users,
     * and all other methods are denied to them.
     */
    const ALLOW = [];
    /**
     * Blacklist: methods in this array cannot be used by non-logged-in users,
     * and all other methods are allowed to them.
     */
    const DENY = [];

    /**
     * Action constructor
     * 
     * This takes an Http\Request as its parameter, and initializes a new
     * Http\Response as well, which inherited class functions can return.
     *
     * @param Http\Request $request
     */
    public function __construct(Request $request)
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
    protected function renderResponse(string $file, array $args = []): Response
    {
        $template = new Template();
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
        string $file,
        Form $f,
        array $args = [],
        $formvar = 'f'
    ): Response {
        if ($f->validateToken() == false) {
            return $this->response->setStatus(403);
        }
        $args = $args + [$formvar => $f];
        return $this->renderResponse($file, $args);
    }

    /**
     * Authorize access to an action
     * 
     * This method is called before all action methods, and must return
     * TRUE for the user to be allowed to take the action. This default
     * implementation uses the ALLOW/DENY whitelist and blacklist arrays
     * to restrict access to a method to only logged-in users.
     * 
     * This method can be overridden in child classes to perform other checks
     * like administration roles.
     *
     * @param string $method
     * @return boolean
     */
    public function grantAccess(string $method): bool
    {
        if (!empty($this::ALLOW)) {
            if (!in_array($method, $this::ALLOW) && !isset($_SESSION['user'])) {
                return false;
            }
        }
        if (!empty($this::DENY)) {
            if (in_array($method, $this::DENY) && !isset($_SESSION['user'])) {
                return false;
            }
        }
        return true;
    }
}
