<?php

namespace App\Traits;

trait SearchableTrait
{

    public function serching($query, $searchable_fields)
    {
        if (request()->search) {
            $search = request()->search;
            $query  = $query->where(function ($q) use ($search, $searchable_fields) {

                foreach ($searchable_fields as $searchable_field) {
                    $q->orWhere($searchable_field, 'like', '%' . $search . '%');
                }
            });

            $count = $query->count();
            if (request()->page || request()->perPage) {
                $page       = request()->page;
                $perPage    = request()->perPage ?? 10;
                $query      = $query->skip($perPage * ($page - 1))->take($perPage);
            }
            return ['query' => $query, 'count' => $count];
        }
    }
}
