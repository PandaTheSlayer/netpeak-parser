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

Artisan::command('run-parser {url}', function ($url){
	$parser = new ParserClass(new \GuzzleHttp\Client(), new \Symfony\Component\DomCrawler\Crawler());
    $parser->getLinks($url);
});
