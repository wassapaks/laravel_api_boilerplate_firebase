<?php

declare(strict_types=1);

namespace App\Hateoas;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation("self", href = "http://hateoas.web/user/42", attributes = {"type" = "application/json"})
 * @Hateoas\Relation("self", href = "expr('/api/books/' ~ object.getId())")
 */
class Books
{
    /** @Serializer\XmlAttribute */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}