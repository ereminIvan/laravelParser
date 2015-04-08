<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParserNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parser_news', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

			$table->increments('id');
            $table->string('title')->default('');
            $table->text('description')->default('');
            $table->text('text')->default('');
            $table->text('uri');
            $table->tinyInteger('is_viewed', false, true)->default(0);
            $table->tinyInteger('is_archived', false, true)->default(0);
            $table->timestamp('viewed_at')->default(null);
            $table->timestamps();
            $table->integer('user_id')->unsigned()->default(null);
//            $table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parser_news');
	}

}
