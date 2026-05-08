<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait PartialRenderable
{
    /**
     * Return view. Jika PJAX request, tambah header X-PJAX-URL.
     * Layout blade-lah yang handle rendering berbeda saat PJAX.
     */
    protected function partialView(string $view, array $data = []): mixed
    {
        $request = request();

        if ($this->isPjaxRequest($request)) {
            return response(view($view, $data))
                ->header('X-PJAX-URL', $request->url());
        }

        return view($view, $data);
    }

    protected function isPjaxRequest(Request $request): bool
    {
        return $request->header('X-PJAX') === 'true';
    }
}
