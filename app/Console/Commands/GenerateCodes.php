<?php namespace App\Console\Commands;

use App\Models\ParserNews;
use App\Models\ParserSource;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Api\Parser\ParserFactory;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use Symfony\Component\Console\Input\InputOption;

class GenerateCodes extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate:codes';

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
            ['len', 10, InputOption::VALUE_OPTIONAL, 'Code length'],
            ['count', 20, InputOption::VALUE_OPTIONAL, 'Codes count'],
        ];
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
    {
        function generateCode($len) {
            $charLen = strlen($char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $result = '';
            for ($i = 0; $i < $len; $i++) {
                $result .= $char[rand(0, $charLen - 1)];
            }
            return $result;
        }

        try {
            $len = (int) $this->option('len');
            $count = (int) $this->option('count');

            $this->comment("Generate $count code(s) with $len char(s) length:");

            $codes = [];
            for ($i = 0; $i < $count; $i++) {
                $codes[generateCode($len)] = $i;
            }

            $codesCount = count($codes);
            $collisionsLen = ($count - $codesCount) * 2;
            $this->info("Collisions count: $collisionsLen; Total count: $codesCount");

        } catch (\Exception $e) {
            $this->error(PHP_EOL . "ERROR[{$e->getCode()}]: {$e->getMessage()}");
        }
    }

}
