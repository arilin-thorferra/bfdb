<?php

declare(strict_types=1);

namespace Http;

class Response
{
    /**
     * Response variables
     */
    private $status_code;
    private $headers;
    private $body;

    /**
     * Status code to reason map
     */
    const REASON = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        205 => 'Reset Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        410 => 'Gone',
        415 => 'Unsupported Media Type',
        418 => "I'm a teapot",
        500 => 'Internal Server Error'
    ];

    /**
     * Constructor
     *
     * @param integer $status_code
     * @param string $body
     * @param array $headers
     */
    public function __construct(
        int $code=200, string $body='', array $headers=[]
    ) {
        $this->body = $body;
        $this->headers = $headers;
        $this->setStatus($code);
    }

    /**
     * Modify an existing header or add a new one
     *
     * @param string $header
     * @param string $value
     * @return Response
     */
    public function setHeader(string $header, string $value) : Response
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * Return all the headers of the response
     *
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Set the body of the response to a new value
     *
     * @param string $body
     * @return Response
     */
    public function setBody(string $body) : Response
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Add to the body of the response. By default, $body_part will be
     * separated from the existing body with a newline character; this can be
     * overridden by explicitly specifying a delimiter.
     *
     * @param string $body_part
     * @param string $delimiter
     * @return Response
     */
    public function addToBody(string $body_part, string $delimiter="\n") : Response
    {
        $this->body = $this->body . $delimiter . $body_part;
        return $this;
    }

    /**
     * Return the body of the response
     *
     * @return string
     */
    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * Set the status code of the response. By default this replaces the
     * existing body; pass FALSE as the second parameter to prevent this.
     *
     * @param integer $code
     * @param bool $clear
     * @return Response
     */
    public function setStatus(int $code, bool $clear=true) : Response
    {
        $this->status_code = $code;
        if ($clear && $code != 204) {
            $t = new \Template();
            if (file_exists(BASE_DIR . "templates/_$code.phtml")) {
                $template = $t->render("_$code");
            } else {
                $reason = self::REASON[$code];
                $template = $t->render('_error', compact('reason', 'code'));
            }
            $this->body = $template;
        }
        return $this;
    }

    /**
     * Get the status code of the response
     *
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status_code;
    }

    public function redirect($url, int $type=303) : Response
    {
        $this->setStatus($type);
        $this->setHeader('Location', $url);
        return $this;
    }
}
