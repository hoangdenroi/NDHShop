<?php

namespace App\Services;

use App\Models\GiftPage;

/**
 * GiftRenderService — Render template HTML + CSS + JS cho gift pages.
 *
 * Ưu tiên:
 * 1. Nếu có rendered_html (cache) → dùng luôn
 * 2. Nếu không → render từ template + data → lưu cache
 *
 * Xử lý thêm:
 * - Inject watermark cho gói basic
 * - Inject meta tags SEO / Open Graph
 * - Dọn dẹp placeholders chưa được thay thế
 */
class GiftRenderService
{
    /**
     * Render HTML đầy đủ cho gift page (không watermark, không meta).
     * Dùng để pre-render và cache vào DB.
     */
    public function render(GiftPage $giftPage): string
    {
        $template = $giftPage->template;
        $data = $giftPage->page_data ?? [];

        // 1. Giải mã Base64
        $htmlCode = $template->decoded_html;
        $cssCode = $template->decoded_css;
        $jsCode = $template->decoded_js;

        // 2. Chèn CSS vào <head>
        if ($cssCode) {
            $styleTag = "<style>\n{$cssCode}\n</style>";
            if (str_contains($htmlCode, '</head>')) {
                $htmlCode = str_replace('</head>', "{$styleTag}\n</head>", $htmlCode);
            } else {
                $htmlCode = $styleTag . "\n" . $htmlCode;
            }
        }

        // 3. Chèn JS vào trước </body>
        if ($jsCode) {
            $scriptTag = "<script>\n{$jsCode}\n</script>";
            if (str_contains($htmlCode, '</body>')) {
                $htmlCode = str_replace('</body>', "{$scriptTag}\n</body>", $htmlCode);
            } else {
                $htmlCode .= "\n" . $scriptTag;
            }
        }

        // 4. Thay thế placeholders {{KEY}} bằng data
        foreach ($data as $key => $value) {
            $htmlCode = str_replace(
                '{{' . $key . '}}',
                htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'),
                $htmlCode
            );
        }

        // 5. Dọn dẹp placeholders còn sót (tương thích ngược khi admin sửa template)
        $htmlCode = preg_replace('/\{\{.*?\}\}/', '', $htmlCode);

        return $htmlCode;
    }

    /**
     * Lấy HTML đã render (ưu tiên cache, nếu không có thì render mới).
     */
    public function getRenderedHtml(GiftPage $giftPage): string
    {
        // Ưu tiên cache
        if (!empty($giftPage->rendered_html)) {
            return $giftPage->rendered_html;
        }

        // Render mới và cache lại
        $html = $this->render($giftPage);
        $giftPage->update(['rendered_html' => $html]);

        return $html;
    }

    /**
     * Inject watermark cho gói basic.
     */
    public function injectWatermark(string $html): string
    {
        $watermark = <<<'HTML'
<div class="ndh-watermark" style="position:fixed;bottom:12px;left:50%;transform:translateX(-50%);z-index:99999;background:rgba(0,0,0,0.7);color:#fff;padding:6px 16px;border-radius:20px;font-size:12px;font-family:sans-serif;pointer-events:auto;backdrop-filter:blur(4px);box-shadow:0 2px 8px rgba(0,0,0,0.2);">
    <a href="/" target="_blank" style="color:#fff;text-decoration:none;display:flex;align-items:center;gap:4px;">
        ✨ Powered by <strong>NDHShop</strong>
    </a>
</div>
HTML;

        // Chèn trước </body>
        if (str_contains($html, '</body>')) {
            return str_replace('</body>', "{$watermark}\n</body>", $html);
        }

        // Fallback: nối ở cuối
        return $html . "\n" . $watermark;
    }

    /**
     * Inject meta tags SEO / Open Graph.
     */
    public function injectMetaTags(string $html, GiftPage $giftPage): string
    {
        // Xóa thẻ <title> cũ
        $html = preg_replace('/<title[^>]*>.*?<\/title>/is', '', $html);

        $url = url("/g/{$giftPage->share_code}");
        $metaImage = $giftPage->meta_image
            ? "<meta property=\"og:image\" content=\"{$giftPage->meta_image}\">"
            : '';

        $metaTags = <<<HTML
<title>{$giftPage->meta_title}</title>
<meta property="og:title" content="{$giftPage->meta_title}">
<meta property="og:type" content="website">
<meta property="og:url" content="{$url}">
{$metaImage}
<meta property="og:description" content="Trang quà tặng tương tác được tạo tại NDHShop">
HTML;

        if (str_contains($html, '</head>')) {
            return str_replace('</head>', "{$metaTags}\n</head>", $html);
        } elseif (str_contains($html, '<head>')) {
            return str_replace('<head>', "<head>\n{$metaTags}", $html);
        }

        return $metaTags . "\n" . $html;
    }

    /**
     * Cập nhật cache rendered_html.
     */
    public function refreshCache(GiftPage $giftPage): void
    {
        $html = $this->render($giftPage);
        $giftPage->update(['rendered_html' => $html]);
    }
}
