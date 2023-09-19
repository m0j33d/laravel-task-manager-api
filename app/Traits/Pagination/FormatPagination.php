<?php

namespace App\Traits\Pagination;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait FormatPagination
{
    protected function generatePagination()
    {
        return [
            'links' => [
                'current_page' => $this->currentPage(),
                // 'first_page_url' => $this->url(1),
                // 'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
            ],
            'meta' => [
                // 'from' => $this->firstPage(),
                'last_page' => $this->lastPage(),
                // 'path' => $this->path(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total()
            ]
        ];
    }


    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return mixed|\Illuminate\Http\Resources\Json\AnonymousResourceCollection 
     */
    public static function collection(mixed $resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            $className = static::class;
            return new $className($resource);
        }

        return parent::collection($resource);
    }
}
