<?php namespace App\Console\Commands;

use App\Models\ParserNews;
use App\Models\ParserSource;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Api\Parser\ParserFactory;

class ParseSources extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'schedule:parse-sources';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parse all available ParserSource for News';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $sources = ParserSource::query()
            ->where('is_active', 1)
            ->where('executed_at', '<', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->distinct()
            ->get();

        $this->comment(PHP_EOL . 'Collected list of sources:' . count($sources) .  PHP_EOL);

        $executionTime = date('Y-m-d H:i:s +3');
        foreach($sources as $source) {
            foreach(ParserFactory::factory($source)->parse() as $item) {
                ParserNews::create([
                    'title'         => $item['title'],
                    'description'   => $item['description'],
                    'text'          => $item['text'],
                    'uri'           => $item['link'],
                    'created_at'    => $item['created_at'],
                    'executed_at'   => $executionTime,
                ]);
            }
            $source->executed_at = $executionTime;
            $source->save();
        }
        //todo Update all last source.executed_at date

	}

}
