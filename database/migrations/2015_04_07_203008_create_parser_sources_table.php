<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParserSourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parser_sources', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('type');
            $table->text('uri');
            $table->text('keywords');
            $table->tinyInteger('active', false, true);
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parser_sources');
	}

}
