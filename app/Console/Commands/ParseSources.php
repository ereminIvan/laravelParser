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
        $sources = ParserSource::query()->where('is_active', 1)->distinct()->get();

        $this->comment(PHP_EOL . 'Collected list of sources:' . count($sources));
        try {
            foreach ($sources as $source) {
                $this->info("Source: {$source->type} | {$source->uri} | {$source->executed_at}");

                $items = ParserFactory::factory(
                    $source->type,
                    $source->uri,
                    explode(';', $source->keywords),
                    $source->executed_at
                )->parse();

                $this->comment('Hits count: ' . count($items));

                foreach ($items as $item) {
                    //todo Check for unique | Unique buy source and keywords
                    ParserNews::create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'text' => $item['text'],
                        'uri' => $item['link'],
                        'source_created_at' => $item['created_at']
                    ]);
                }
                $source->executed_at = date('Y-m-d H:i:s');
                $source->save();
            }
        } catch (\Exception $e) {
            $this->error(PHP_EOL . "ERROR[{$e->getCode()}]: {$e->getMessage()}");
        }
    }

}
