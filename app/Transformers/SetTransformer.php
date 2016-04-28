<?php namespace App\Transformers;

use App\Set;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SetTransformer extends TransformerAbstract {

    public function transform(Set $set)
    {
        return [
            'scores' => [$set->score1, $set->score2]
        ];
    }

}
