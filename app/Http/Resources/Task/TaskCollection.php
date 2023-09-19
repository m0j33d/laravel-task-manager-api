<?php

namespace App\Http\Resources\Task;

use App\Traits\Pagination\FormatPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    use FormatPagination;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count' => $this->count(),
            'tasks' => $this->collection,
            'pagination' => ($this->resource instanceof LengthAwarePaginator && $this->count() > 0) ? $this->generatePagination() : null
        ];
    }
}
