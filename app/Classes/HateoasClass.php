<?php

namespace App\Classes;

use Hateoas\HateoasBuilder;
use App\Hateoas\Books;
class HateoasClass
{
    private $links;
    public function __construct($representation)
    {
        $hateoas = HateoasBuilder::create()
            ->setDebug(true)
            ->build();
        $json = $hateoas->serialize($representation, 'json');
        $this->links = json_decode($json, true);

    }

    public function getLinks(){
        return $this->links['_links'];
    }
}
