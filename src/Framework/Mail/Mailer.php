<?php

namespace OP\Framework\Mail;

use InvalidArgumentException;
use OP\Support\Facades\Blade;

class Mailer
{
    /**
     * The e-mail related view.
     *
     * @var string
     */
    protected $view;

    /**
     * The e-mail related view parameters.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Can be one of 'raw' or 'html'
     *
     * @var string
     */
    protected $format = 'html';

    /**
     * The mail recipients(s).
     *
     * @var array
     */
    protected $to = [];

    /**
     * The mail subject.
     *
     * @var string
     */
    protected $subject = '';

    /**
     * The message content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * The view used to build the mail content.
     *
     * @param string|array The message receipient(s)
     *
     * @return self
     */
    public function to($to)
    {
        $this->to += is_array($to) ? $to : [$to];

        return $this;
    }

    /**
     * The view used to build the mail content.
     *
     * @return self
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * The mail subject.
     *
     * @return self
     */
    public function subject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * The mail content. Filled automatically if you provide a view.
     *
     * @return self
     */
    public function content(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * The parameters sent to the view to build the mail content.
     *
     * @return self
     */
    public function with(array $params = [])
    {
        $this->with = $params;

        return $this;
    }

    /**
     * Set the mail content as html.
     *
     * @return self
     */
    public function html()
    {
        $this->format = 'html';

        return $this;
    }

    /**
     * Set the mail content as raw.
     *
     * @return self
     */
    public function raw()
    {
        $this->format = 'raw';

        return $this;
    }

    /**
     * Build the mail content using blade compiler.
     *
     * @return self
     */
    protected function build()
    {
        if ($this->view) {
            $this->content = Blade::template($this->view, $this->with);
        }

        return $this;
    }

    /**
     * Check mandatory fields.
     *
     * @return self
     * @throws InvalidArgumentException
     */
    protected function validate()
    {
        foreach (['to', 'subject', 'content'] as $property) {
            if (!$this->{$property}) {
                throw new InvalidArgumentException(
                    sprintf('Mailer error : Missing mandatory parameter %s.', $property)
                );
            }
        }

        return $this;
    }

    /**
     * Compiles the mail content using view and params, and send the message using WP mailer.
     *
     * @return bool
     */
    public function send()
    {
        $this->build();
        $this->validate();

        return wp_mail(
            $this->to,
            $this->subject,
            $this->content
        );
    }

    /**
     * Returns a new instance of mailer.
     *
     * @return self
     */
    public static function make()
    {
        return new static();
    }
}
