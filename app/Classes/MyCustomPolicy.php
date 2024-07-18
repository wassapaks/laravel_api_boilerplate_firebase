<?php
namespace App\Classes;

use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;

class MyCustomPolicy extends Basic
{
    public function configure()
    {
        parent::configure();
        
        $this->addDirective(Directive::FRAME_ANCESTORS, 'none');
        $this->addDirective(Directive::DEFAULT, 'none');
    }
}