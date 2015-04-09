<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Models\ParserSource;

class Parser
{
    const CHAR_LIST = 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя';
    /** @var \App\Models\ParserSource */
    protected $source;

    /**
     * @param \App\Models\ParserSource $source
     */
    public function __construct(ParserSource $source)
    {
        $this->source = $source;
    }
}