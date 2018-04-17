<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public static function storeProducts(array $objects)
    {
        $dataSet = [];
        foreach ($objects as $key => $value){
            $dataSet[] = [
              'name'            => $key,
              'speed'           => $value['Скорость отжима'][0],
              'energy_class'    => $value['Класс энергопотребления'],
              'program_count'   => $value['Количество программ'],
              'linen_dry_count' => $value['Количество белья при сушке'],
            ];
            print_r($value);
        }
        print_r($objects);

//        \DB::table('products')->insert($dataSet);
    }
}
