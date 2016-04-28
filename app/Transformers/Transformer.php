<?php

namespace App\Transformers;

use League\Fractal;

abstract class Transformer extends Fractal\TransformerAbstract
{

    public function transformCollection( array $items, $custom = '', $args = [] )
    {

        if( !empty($args) )
        {

            if( isset( $args['rankOffset']) )
            {
                $this->rank = $args['rankOffset'];
            }



        }


        return array_map( [ $this, 'transform'.$custom ], $items );
    }

    public abstract function transform( $item );


    protected function insertAfter( $part, $after_key, $array )
    {
        $pos = array_search( $after_key, array_keys($array) ) + 1;

        $res = array_slice($array, 0, $pos, true) +
            $part +
            array_slice($array, $pos, count($array)-$pos, true);

        return $res;
    }

}