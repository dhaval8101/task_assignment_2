<?php

namespace App\Helpers;

trait PaginationHelper
{
    public function getPaginationParameters($request): array
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', null);

        return [$perPage, $page, $search];
    }
}
