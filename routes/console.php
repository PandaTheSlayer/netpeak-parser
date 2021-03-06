<?php

use Illuminate\Foundation\Inspiring;
use App\Modules\Parser\ParserClass;


/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('run-parser', function (){
    $config = Config::get('parser');
	$parser = new ParserClass(new \GuzzleHttp\Client());
    $parser->parseCatalogLink(
        $config['url']
    );
    $objects = $parser->parseObjects($parser->getNotVisitedLinks());
    \App\Product::storeProducts($objects);
});
