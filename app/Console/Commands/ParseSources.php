<?php namespace App\Console\Commands;

use App\Models\ParserNews;
use App\Models\ParserSource;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Api\Parser\ParserFactory;
use Symfony\Component\Console\Input\InputOption;

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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['byCreatedAt', null, InputOption::VALUE_NONE, 'If passed then parse outdated news in first start']
        ];
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
    {
        $sources = ParserSource::query()->where('is_active', 1)->distinct()->get();

        $this->info(PHP_EOL . 'Collected list of sources: ' . count($sources));

        try {
            foreach ($sources as $source) {
                $this->comment("Source: {$source->type} | {$source->uri} | {$source->executed_at}");

                //keywords
                $keywords = [];
                foreach (explode(';', $source->keywords) as $key => $keyword) {
                    $keywords[$key] = trim($keyword);
                    if (empty($keywords[$key])) {
                        unset($keywords[$key]);
                    }
                }

                //Parse Items
                $items = ParserFactory::factory(
                    $source->type,
                    $source->uri,
                    $keywords,
                    $this->option('byCreatedAt') ? $source->created_at : $source->executed_at
                )->parse();

                $this->comment('Hits count: ' . count($items));

                //Saving Items
                foreach ($items as $item) {
                    //todo Check for unique | Unique buy source and keywords
                    ParserNews::create([
                        'title'             => $item['title'],
                        'description'       => $item['description'],
                        'text'              => $item['text'],
                        'uri'               => $item['link'],
                        'source_created_at' => $item['created_at'],
						'parser_source_id'  => $source->id
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
