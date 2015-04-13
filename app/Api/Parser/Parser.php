<?php
/**
 * @author Eremin Ivan
 * @email coding.ebola@gmail.com
 */
namespace App\Api\Parser;

use App\Models\ParserSource;

/**
 * @property int    limitPerRequests
 * @property array  keywords
 * @property string sourceURI
 */
abstract class Parser
{
    protected $limitPerRequests = 0;
    protected $sourceURI;
    protected $keywords = [];
	/**
	 * If false then parse outdated news in first start
	 * @var bool
	 */
	protected $firstTimeDateLimit = false;

    /**
     * @param string    $sourceURI
     * @param array     $keywords
     * @param string    $executedAt
	 * @param string    $createdAt
     */
    public function __construct($sourceURI, array $keywords, $executedAt, $createdAt)
    {
		foreach ($keywords as $key => $keyword) {
			$keywords[$key] = trim($keyword);
			if (empty($keywords[$key])) {
				unset($keywords[$key]);
			}
		}

        $this->sourceURI = $sourceURI;
        $this->keywords  = $keywords;
        $this->executedAt = $executedAt;
		$this->createdAt = $createdAt;

		if ($this->firstTimeDateLimit && strtotime($this->executedAt) < 0) {
			$this->executedAt = $this->createdAt;
		}
    }

    /**
     * @return array
     */
    public abstract function parse();

    /**
     * @param string $text
     *
     * @return bool
     */
    protected function test($text)
    {
        preg_match('/(?:'.implode('|', $this->keywords).')/iu', strip_tags($text), $matches);
        return (bool) count($matches);
    }
}