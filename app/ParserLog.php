<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParserLog extends Model
{
	protected $fillable = ['url'];


    /**
     * Store links of objects
     * @TOOD - Implement functionality of ticking already visited links
     * @param $links
     */
    public function storeObjLinks($links)
	{
		foreach ($links as $link){
		    $model = new static;
		    $model::firstOrCreate(['url' => $link]);
        }
    }
}
