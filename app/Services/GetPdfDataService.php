<?php

namespace App\Services;

use Illuminate\Http\Request;

class GetPdfDataService
{
    public function __invoke(Request $request): array
    {
        dd(
            $request->all()
        );

        return [];
    }
}