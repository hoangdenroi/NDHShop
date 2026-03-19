<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\GiftPage;
use App\Models\GiftTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftController extends Controller
{
    /**
     * Hiển thị gallery các mẫu template quà tặng (user-facing).
     */
    public function index(Request $request)
    {
        $query = GiftTemplate::active();

        // Lọc theo category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Ưu tiên hiển thị mẫu premium trước, rồi đến mới nhất
        $templates = $query->orderByDesc('is_premium')->latest('id')->paginate(16)->withQueryString();
        $categories = GiftTemplate::CATEGORIES;

        return view('pages.app.gifts.templates', compact('templates', 'categories'));
    }

    /**
     * Form tạo thiệp quà tặng dựa trên template.
     */
    public function create(GiftTemplate $template)
    {
        if (! $template->is_active) {
            abort(404, 'Mẫu template không tồn tại hoặc đã bị khóa.');
        }

        return view('pages.app.gifts.create', compact('template'));
    }

    /**
     * Xử lý lưu thiệp — tạo draft, chưa active, chưa có share_code.
     * Sau khi tạo xong → redirect sang trang chọn plan.
     */
    public function store(Request $request, GiftTemplate $template)
    {
        if (! $template->is_active) {
            abort(404, 'Mẫu template không tồn tại hoặc đã bị khóa.');
        }

        // Validate dữ liệu từ form dựa trên schema của mẫu
        $rules = $this->buildSchemaRules($template);

        $validated = $request->validate(array_merge($rules, [
            'meta_title' => 'nullable|string|max:255',
            'meta_image' => 'nullable|url|max:2048',
        ]));

        // Lấy data user nhập (dựa trên schema) và xử lý tách dòng
        $pageData = $this->processPageData($request->input('data', []));

        // Tạo gift page ở trạng thái DRAFT
        $giftPage = GiftPage::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'page_data' => $pageData,
            'meta_title' => $validated['meta_title'] ?? $pageData['TITLE'] ?? 'Quà tặng từ '.Auth::user()->name,
            'meta_image' => $validated['meta_image'] ?? $pageData['IMAGE'] ?? $template->thumbnail,
            'status' => GiftPage::STATUS_DRAFT,
            'plan' => GiftPage::PLAN_BASIC,
            'is_active' => false,
        ]);

        // Tăng usage count của template
        $template->incrementUsage();

        return redirect()->route('app.gifts.choose-plan', $giftPage->unitcode)
            ->with('success', 'Đã lưu nội dung thiệp! Tiếp tục chọn gói dịch vụ.');
    }

    /**
     * Trang chọn gói dịch vụ (Basic / Premium).
     */
    public function choosePlan(GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        // Chỉ cho chọn plan khi ở trạng thái draft
        if ($gift->status !== GiftPage::STATUS_DRAFT) {
            return redirect()->route('app.gifts.my-gifts')
                ->with('info', 'Gift này đã được kích hoạt.');
        }

        $plans = [
            GiftPage::PLAN_BASIC => [
                'name' => 'Basic',
                'price' => GiftPage::PLAN_PRICES[GiftPage::PLAN_BASIC],
                'duration' => GiftPage::PLAN_DURATIONS[GiftPage::PLAN_BASIC].' ngày',
                'features' => [
                    'Chia sẻ link quà tặng',
                    'Hiển thị 7 ngày',
                    'Có watermark NDHShop',
                ],
                'disabled' => [
                    'Không có thống kê lượt xem',
                    'Không chỉnh sửa sau khi kích hoạt',
                    'Không hỗ trợ kỹ thuật',
                ],
            ],
            GiftPage::PLAN_PREMIUM => [
                'name' => 'Premium',
                'price' => GiftPage::PLAN_PRICES[GiftPage::PLAN_PREMIUM],
                'duration' => 'Vĩnh viễn',
                'features' => [
                    'Chia sẻ link quà tặng',
                    'Hiển thị vĩnh viễn',
                    'Không có watermark NDHShop',
                    'Thống kê lượt xem chi tiết',
                    'Cho phép chỉnh sửa sau khi kích hoạt',
                    'Hỗ trợ kỹ thuật 24/7',
                ],
                'disabled' => [],
            ],
        ];

        return view('pages.app.gifts.choose-plan', compact('gift', 'plans'));
    }

    /**
     * Danh sách thiệp user đã tạo.
     */
    public function myGifts()
    {
        $gifts = GiftPage::where('user_id', Auth::id())
            ->with('template')
            ->latest()
            ->paginate(10);

        return view('pages.app.gifts.my-gifts', compact('gifts'));
    }

    /**
     * Form chỉnh sửa thiệp đã tạo (chỉ khi draft).
     */
    public function edit(GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        $template = $gift->template;

        return view('pages.app.gifts.edit', compact('gift', 'template'));
    }

    /**
     * Xử lý cập nhật thiệp.
     */
    public function update(Request $request, GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        $template = $gift->template;

        // Validate dữ liệu từ form dựa trên schema của mẫu
        $rules = $this->buildSchemaRules($template);

        $validated = $request->validate(array_merge($rules, [
            'meta_title' => 'nullable|string|max:255',
            'meta_image' => 'nullable|url|max:2048',
        ]));

        $pageData = $this->processPageData($request->input('data', []));

        $gift->update([
            'page_data' => $pageData,
            'meta_title' => $validated['meta_title'] ?? $pageData['TITLE'] ?? $gift->meta_title,
            'meta_image' => $validated['meta_image'] ?? $pageData['IMAGE_1'] ?? $gift->meta_image,
        ]);

        // Nếu gift đang active → cập nhật cache rendered_html
        if ($gift->status === GiftPage::STATUS_ACTIVE) {
            app(\App\Services\GiftRenderService::class)->refreshCache($gift);
        }

        return redirect()->route('app.gifts.my-gifts')->with('success', 'Cập nhật trang quà tặng thành công!');
    }

    /**
     * Xóa / Ẩn trang quà tặng user đã tạo.
     */
    public function destroy(GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        $gift->softDelete();

        return back()->with('success', 'Đã xóa trang quà tặng.');
    }

    // ──── Private Methods ────

    /**
     * Build validation rules từ template schema.
     */
    private function buildSchemaRules(GiftTemplate $template): array
    {
        $schemaFields = $template->getSchemaFields();
        $rules = [];

        foreach ($schemaFields as $field) {
            $rule = [];
            if ($field['required'] ?? false) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if (($field['type'] ?? '') === 'url' || ($field['type'] ?? '') === 'image') {
                $rule[] = 'string';
            }

            if (isset($field['maxLength'])) {
                $rule[] = 'max:'.$field['maxLength'];
            }

            if (isset($field['limit']) && in_array($field['type'] ?? '', ['textarea', 'image', 'url'])) {
                $rule[] = function ($attribute, $value, $fail) use ($field) {
                    if (is_string($value)) {
                        $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $value))), fn ($line) => $line !== '');
                        if (count($lines) > $field['limit']) {
                            $label = $field['label'] ?? $field['key'];
                            $fail("Trường \"{$label}\" chỉ được nhập tối đa {$field['limit']} nội dung/dòng.");
                        }
                    }
                };
            }

            $rules['data.'.$field['key']] = $rule;
        }

        return $rules;
    }

    /**
     * Tự động tách các field có nhiều dòng (xuống dòng) thành KEY1, KEY2...
     */
    private function processPageData(array $inputData): array
    {
        $pageData = [];
        foreach ($inputData as $key => $value) {
            $pageData[$key] = $value;
            // Nếu là chuỗi có chứa ký tự xuống dòng
            if (is_string($value) && str_contains($value, "\n")) {
                $lines = array_map('trim', explode("\n", str_replace("\r", '', $value)));
                $i = 1;
                foreach ($lines as $line) {
                    if ($line !== '') {
                        $pageData[$key.$i] = $line;
                        $i++;
                    }
                }
            }
        }

        return $pageData;
    }
}
