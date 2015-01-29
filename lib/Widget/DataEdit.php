<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Helper\RouteHelper;

/**
 * A Form that automatise the process of reading HTTP input,
 * applying the validation, saving and redirecting.
 */

class DataEdit extends DataForm
{
    protected $gridUrl;
    /**
     * @var Attribute
     */
    public $active;
    /**
     * @var Attribute
     */
    public $processed;

    public function __construct($source = null, $gridUrl = null)
    {
        $this->gridUrl = RouteHelper::toUrl('@getIndex');
        $this->active = new Attribute(true);
        $this->processed = new Attribute(false);
        $this->observeViews();
        parent::__construct($source);
    }

    protected function process()
    {
        $this->populate();
        $this->writeSource();
        if ($this->submitted() && $this->validate()) {
            $this->save();
            return redirect_now($this->gridUrl);
        }
    }

    protected function observeViews()
    {
        $widget = $this;
        app()['events']->listen('composing:*', function ($view) use ($widget) {
            if ($this->active() && !$this->processed()) {
                // first of all, we mark the widget as processed, so we don't
                // process it again
                $this->processed(true);
                $widget->process();
            }
        });
    }
}
