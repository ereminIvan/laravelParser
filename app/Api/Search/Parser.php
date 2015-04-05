<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Search;

class Parser
{
    /** @var \App\Api\Search\Parser\Source */
    protected $source;

    public function __construct(Parser\Source $source)
    {
        $this->source = $source;
    }
} 