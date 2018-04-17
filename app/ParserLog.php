<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParserLog extends Model
{
	protected $fillable = ['url'];

	public function getLinks()
	{

	}
	
	public function storeObjLinks($links)
	{
		$parserLog = new static();
		dd($links);


		return $parserLog;
    }
}
