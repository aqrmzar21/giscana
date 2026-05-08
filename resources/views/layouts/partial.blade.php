{{--
    Layout partial untuk PJAX/AJAX partial page loading.
    Hanya me-render konten page tanpa HTML penuh (tanpa <html>, <head>, <body>).
    JS di sisi client akan inject konten ini ke #page-content.
--}}
<div id="pjax-content-wrapper">
    {{ $slot }}
</div>
<script type="application/json" id="pjax-meta">
{
    "title": "@yield('title', config('app.name', 'Giscana'))",
    "pageTitle": "@yield('page-title', '')",
    "url": "{{ request()->url() }}"
}
</script>
