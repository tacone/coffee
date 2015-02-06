<?php

namespace Tacone\Coffee\Widget;

use Tacone\Coffee\Attribute\Attribute;
use Tacone\Coffee\Helper\QueryStringPolicy;
use Tacone\Coffee\Helper\RouteHelper;

/**
 * A Form that automatise the process of reading HTTP input,
 * applying the validation, saving and redirecting.
 *
 * To make it easier, DataEdit defaults to getIndex() for any
 * redirect.
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

    protected $urlPolicy;

    public function __construct($source = null, $gridUrl = null)
    {
        $this->urlPolicy = new QueryStringPolicy();
        $source = $this->load($source);
        $this->gridUrl = RouteHelper::toUrl('@getIndex');
        $this->active = new Attribute(true);
        $this->processed = new Attribute(false);
        $this->observeViews();
        parent::__construct($source);
    }
    protected function load($source) {
        $model =  $source::find($this->urlPolicy->id());
        return $model?:$source;
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
