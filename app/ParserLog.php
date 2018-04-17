<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ParserLog extends Model
{
	protected $fillable = ['url'];

	
	public function storeObjLinks($links)
	{
		foreach ($links as $link){
		    $model = new static;
		    $model::firstOrCreate(['url' => $link]);
        }
    }
}
