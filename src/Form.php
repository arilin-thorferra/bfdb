<?php

declare(strict_types=1);

namespace Bfdb;

/**
 * Form builder
 */
class Form
{
    protected $values;
    protected $url;
    protected $errors = [];

    /**
     * Constructor
     */
    public function __construct(array $values = [], string $url = '')
    {
        $this->values = $values;
        $this->url = $url;
    }

    /**
     * Return value of existing field in the form. If the field does not
     * exist or has not been filled in, a null symbol ("&empty;") will be
     * returned.
     *
     * @param string $key
     * @return mixed
     */
    public function val(string $key): mixed
    {
        if (key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        return '&empty;';
    }

    /**
     * Make an HTML tag. If a key is supplied, it will be used to look up the
     * proper value from the form object's value property, so forms can be
     * resubmitted. If a body is supplied, it will be wrapped in open/close
     * tags; otherwise, just a single tag will be generated.
     *
     * @param string $tag
     * @param array $attrs
     * @param string $key
     * @param string $body
     * @return string
     */
    protected function maketag(
        string $tag,
        array $attrs = [],
        string $key = '',
        string $body = ''
    ): string {
        $has_error = key_exists($key, $this->errors);
        if ($has_error) {
            if (key_exists('class', $attrs)) {
                $attrs['class'] .= ' form-error';
            } else {
                $attrs['class'] = 'form-error';
            }
        }
        $out = "<$tag";
        if (key_exists($key, $this->values)) {
            $out .= " value=\"{$this->values[$key]}\"";
        }
        foreach ($attrs as $attr => $value) {
            if ($value === true) {
                $out .= " $attr";
            } else {
                $out .= " $attr=\"$value\"";
            }
        }
        $out .= '>';
        if ($body) {
            $out .= "$body</$tag>";
        }
        if ($has_error) {
            $out .= " <span class='form-error msg'>{$this->errors[$key]}</span>";
        }
        return $out;
    }

    /**
     * Open the form
     *
     * @param array $attrs
     * @return void
     */
    public function start(array $attrs = []): string
    {
        $attrs = ['method' => 'post'] + $attrs;
        if ($this->url) {
            $attrs = $attrs + ['action' => $this->url];
        }
        $attrs = $this->addClass($attrs, 'form');
        return $this->maketag('form', $attrs) .
            "<input type=\"hidden\" name=\"_tok\" value=\"{$_SESSION['token']}\">";
    }

    /**
     * Create an input tag
     *
     * @param string $type
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function input(string $type, string $name, array $attrs = []): string
    {
        $tag = 'input';
        $attrs = compact('type', 'name') + $attrs;
        $attrs = $this->addClass($attrs, 'field');
        return $this->maketag($tag, $attrs, $name);
    }

    /**
     * Shorthand for a text input tag
     *
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function text(string $name, array $attrs = []): string
    {
        return $this->input('text', $name, $attrs);
    }

    /**
     * Shorthand for an email input tag
     *
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function email(string $name, array $attrs = []): string
    {
        return $this->input('email', $name, $attrs);
    }

    /**
     * Shorthand for a password input tag. This should be used in preference
     * to input(), as it will blank out any password values if the form is
     * being resubmitted.
     *
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function password(string $name, array $attrs = []): string
    {
        if (key_exists($name, $this->values)) {
            unset($this->values[$name]);
        }
        return $this->input('password', $name, $attrs);
    }

    /**
     * Create a dropdown input
     *
     * @param string $name
     * @param array $options
     * @param array $attrs
     * @return string
     */
    public function select(string $name, array $options, array $attrs = []): string
    {
        $body = '';
        foreach ($options as $value => $body) {
            $body .= "<option value\"$value\"";
            if (key_exists($name, $this->values) && $this->values[$name] == $value) {
                $body .= " selected";
            }
            $body .= '>';
        }
        $attrs = $this->addClass($attrs, 'field');
        return $this->maketag('select', $attrs, '', $body);
    }

    /**
     * Create a text area input
     *
     * @param string $name
     * @param array $attrs
     * @return string
     */
    public function textarea(string $name, array $attrs = []): string
    {
        $body = (key_exists($name, $this->values)) ? $this->values[$name] : '';
        $attrs = $this->addClass($attrs, 'field');
        return $this->maketag('textarea', $attrs, '', $body);
    }

    /**
     * Output a label for a form element
     *
     * @param string $text
     * @param string $for
     * @param array $attrs
     * @return string
     */
    public function label(string $text, string $for, array $attrs = []): string
    {
        $attrs = $attrs + ['for' => $for];
        $attrs = $this->addClass($attrs, 'label');
        return $this->maketag('label', $attrs, '', $text);
    }

    /**
     * Output a submit button
     *
     * @param string $value
     * @param array $attrs
     * @return string
     */
    public function submit(string $value = 'Submit', array $attrs = []): string
    {
        $attrs = ['type' => 'submit', 'value' => $value] + $attrs;
        $tag = 'input';
        $attrs = $this->addClass($attrs, 'field button');
        return $this->maketag($tag, $attrs);
    }

    /**
     * Output a generic button
     *
     * @param string $text
     * @param array $attrs
     * @return string
     */
    public function button(string $text, array $attrs = []): string
    {
        if (!array_key_exists('value', $attrs)) {
            $attrs['value'] = $text;
        }
        if (!array_key_exists('type', $attrs)) {
            $attrs['type'] = 'submit';
        }
        $tag = 'button';
        $attrs = $this->addClass($attrs, 'field button');
        return $this->maketag($tag, $attrs, '', $text);
    }

    /**
     * Close the form
     *
     * @return string
     */
    public function end(): string
    {
        return "</form>";
    }
    
    /**
     * Set an error message on a form field
     *
     * @param string $field
     * @param string $msg
     * @return void
     */
    public function setError(string $field, string $msg): void
    {
        $this->errors[$field] = $msg;
    }
    
    /**
     * Return whether the current form object has errors
     *
     * @return boolean
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Validate the CSRF token
     *
     * @return boolean
     */
    public function validateToken(): bool
    {
        if (!key_exists('_tok', $this->values)) {
            return true;
        }
        return $this->values['_tok'] === $_SESSION['token'];
    }
    
    /**
     * Add a class attribute to the attribute array
     *
     * @param array $attrs
     * @param string $class
     * @return array
     */
    protected function addClass(array $attrs, string $class): array
    {
        if (array_key_exists('class', $attrs)) {
            $attrs['class'] .= ' ' . $class;
        } else {
            $attrs['class'] = $class;
        }
        return $attrs;
    }
}
