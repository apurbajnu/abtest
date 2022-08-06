<?php

namespace Apurbajnu\Abtest\Events;

class ExperimentNewVisitor
{
    public $experiment;

    public function __construct($experiment)
    {
        $this->experiment = $experiment;
    }
}
