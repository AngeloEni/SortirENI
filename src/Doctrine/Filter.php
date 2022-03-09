<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class Filter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {

    }
}