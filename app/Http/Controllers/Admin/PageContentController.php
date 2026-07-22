<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Models\Setting;
use Illuminate\Http\Request;

class PageContentController extends Controller
{
    private function pages(): array
    {
        return [
            'about_us'         => ['label' => 'من نحن',                    'icon' => 'info',           'legal' => false],
            'contact_us'       => ['label' => 'تواصل معنا',                  'icon' => 'call',           'legal' => false],
            'delivery_policy'  => ['label' => 'سياسة التسليم',               'icon' => 'local_shipping', 'legal' => true],
            'refund_policy'    => ['label' => 'سياسة الاسترداد والإلغاء',     'icon' => 'gavel',          'legal' => true],
            'privacy_policy'   => ['label' => 'سياسة الخصوصية',              'icon' => 'shield_lock',    'legal' => true],
            'terms_of_service' => ['label' => 'الشروط والأحكام',             'icon' => 'description',    'legal' => true],
        ];
    }

    private function schema(string $page): array
    {
        return match ($page) {
            'about_us' => [
                ['number' => null, 'title' => 'عنوان الصفحة (SEO)', 'fields' => [
                    ['key' => 'title',             'label' => 'عنوان الصفحة',                    'type' => 'text'],
                    ['key' => 'badge',              'label' => 'الشارة (Badge)',                  'type' => 'text'],
                    ['key' => 'meta_description',   'label' => 'وصف الصفحة (Meta Description)',   'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'المقدمة', 'fields' => [
                    ['key' => 'intro', 'label' => 'الفقرة التعريفية', 'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'منهجنا', 'fields' => [
                    ['key' => 'approach_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'approach_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'مؤسس المنصة', 'fields' => [
                    ['key' => 'founder_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'founder_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'المعلومات القانونية', 'fields' => [
                    ['key' => 'legal_title',         'label' => 'العنوان',            'type' => 'text'],
                    ['key' => 'legal_law',           'label' => 'نص القانون الحاكم',   'type' => 'textarea'],
                    ['key' => 'legal_address_label', 'label' => 'تسمية العنوان',       'type' => 'text'],
                ]],
                // Note: the address value itself now comes from the region-scoped
                // contact settings ("بيانات التواصل" on the Contact Us edit screen),
                // not from a page_contents field — see ContactInfo::current().
            ],

            'contact_us' => [
                ['number' => null, 'title' => 'عنوان الصفحة (SEO)', 'fields' => [
                    ['key' => 'title',            'label' => 'عنوان الصفحة',                  'type' => 'text'],
                    ['key' => 'badge',             'label' => 'الشارة (Badge)',                'type' => 'text'],
                    ['key' => 'meta_description',  'label' => 'وصف الصفحة (Meta Description)', 'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'المحتوى', 'fields' => [
                    ['key' => 'lead',          'label' => 'فقرة المقدمة',        'type' => 'textarea'],
                    ['key' => 'response_note', 'label' => 'ملاحظة وقت الرد',     'type' => 'textarea'],
                ]],
                // Note: region-scoped contact data (email/phone/whatsapp/hours/
                // address for Egypt & international) is now edited from
                // Admin → Settings → General, not here — see ContactInfo::current().
            ],

            'delivery_policy' => [
                ['number' => null, 'title' => 'عنوان الصفحة (SEO)', 'fields' => [
                    ['key' => 'title',            'label' => 'عنوان الصفحة',                  'type' => 'text'],
                    ['key' => 'badge',             'label' => 'الشارة (Badge)',                'type' => 'text'],
                    ['key' => 'last_updated',      'label' => 'تاريخ آخر تحديث',               'type' => 'text'],
                    ['key' => 'meta_description',  'label' => 'وصف الصفحة (Meta Description)', 'type' => 'textarea'],
                ]],
                ['number' => '4.1', 'title' => 'الخدمات الرقمية', 'fields' => [
                    ['key' => 's41_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's41_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '4.2', 'title' => 'الجدول الزمني لتسليم الخدمة', 'fields' => [
                    ['key' => 's42_title', 'label' => 'العنوان',         'type' => 'text'],
                    ['key' => 's42_intro', 'label' => 'المقدمة',         'type' => 'textarea'],
                    ['key' => 's42_items', 'label' => 'عناصر القائمة',   'type' => 'list'],
                ]],
                ['number' => '4.3', 'title' => 'ماذا يحدث في حالة التأخير من جانبنا', 'fields' => [
                    ['key' => 's43_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's43_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '4.4', 'title' => 'لا يوجد شحن مادي', 'fields' => [
                    ['key' => 's44_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's44_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
            ],

            'refund_policy' => [
                ['number' => null, 'title' => 'عنوان الصفحة (SEO)', 'fields' => [
                    ['key' => 'title',            'label' => 'عنوان الصفحة',                  'type' => 'text'],
                    ['key' => 'badge',             'label' => 'الشارة (Badge)',                'type' => 'text'],
                    ['key' => 'last_updated',      'label' => 'تاريخ آخر تحديث',               'type' => 'text'],
                    ['key' => 'meta_description',  'label' => 'وصف الصفحة (Meta Description)', 'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'المقدمة', 'fields' => [
                    ['key' => 'intro', 'label' => 'الفقرة التعريفية', 'type' => 'textarea'],
                ]],
                ['number' => '5.1', 'title' => 'فترة الاسترداد الكامل', 'fields' => [
                    ['key' => 's51_title', 'label' => 'العنوان',                'type' => 'text'],
                    ['key' => 's51_intro', 'label' => 'المقدمة',                'type' => 'textarea'],
                    ['key' => 's51_items', 'label' => 'عناصر القائمة (الشروط)',  'type' => 'list'],
                ]],
                ['number' => '5.2', 'title' => 'بعد تسليم برنامجك', 'fields' => [
                    ['key' => 's52_title', 'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 's52_intro', 'label' => 'المقدمة',       'type' => 'textarea'],
                    ['key' => 's52_items', 'label' => 'عناصر القائمة', 'type' => 'list'],
                ]],
                ['number' => '5.3', 'title' => 'مشاكل جودة الخدمة', 'fields' => [
                    ['key' => 's53_title',  'label' => 'العنوان',                                'type' => 'text'],
                    ['key' => 's53_before', 'label' => 'النص قبل رابط البريد الإلكتروني',          'type' => 'textarea'],
                    ['key' => 's53_after',  'label' => 'النص بعد رابط البريد الإلكتروني',          'type' => 'textarea'],
                ]],
                ['number' => '5.4', 'title' => 'تجميد/إيقاف الاشتراك مؤقتاً', 'fields' => [
                    ['key' => 's54_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's54_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '5.5', 'title' => 'العروض الترويجية', 'fields' => [
                    ['key' => 's55_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's55_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '5.6', 'title' => 'معالجة الاسترداد', 'fields' => [
                    ['key' => 's56_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's56_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '5.7', 'title' => 'القانون الحاكم والنزاعات', 'fields' => [
                    ['key' => 's57_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's57_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '6', 'title' => 'الشكاوى والاقتراحات', 'fields' => [
                    ['key' => 's6_title',  'label' => 'العنوان',                        'type' => 'text'],
                    ['key' => 's6_before', 'label' => 'النص قبل روابط التواصل',          'type' => 'textarea'],
                    ['key' => 's6_or',     'label' => 'كلمة الفصل (أو)',                 'type' => 'text'],
                    ['key' => 's6_after',  'label' => 'النص بعد روابط التواصل',          'type' => 'textarea'],
                ]],
            ],

            'privacy_policy' => [
                ['number' => null, 'title' => 'عنوان الصفحة', 'fields' => [
                    ['key' => 'title',        'label' => 'عنوان الصفحة',     'type' => 'text'],
                    ['key' => 'badge',        'label' => 'الشارة (Badge)',   'type' => 'text'],
                    ['key' => 'last_updated', 'label' => 'تاريخ آخر تحديث',  'type' => 'text'],
                    ['key' => 'intro',        'label' => 'الفقرة التعريفية', 'type' => 'textarea'],
                ]],
                ['number' => '3.1', 'title' => 'المعلومات التي نجمعها', 'fields' => [
                    ['key' => 's31_title',      'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's31_intro',      'label' => 'المقدمة', 'type' => 'textarea'],
                    ['key' => 's31_items_bold', 'label' => 'العناوين الفرعية (سطر لكل عنصر)', 'type' => 'list', 'paired_with' => 's31_items_text'],
                    ['key' => 's31_items_text', 'label' => 'النصوص المقابلة (بنفس الترتيب)',   'type' => 'list', 'paired_with' => 's31_items_bold'],
                ]],
                ['number' => '3.2', 'title' => 'كيفية استخدام معلوماتك', 'fields' => [
                    ['key' => 's32_title', 'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 's32_intro', 'label' => 'المقدمة',       'type' => 'textarea'],
                    ['key' => 's32_items', 'label' => 'عناصر القائمة', 'type' => 'list'],
                ]],
                ['number' => '3.3', 'title' => 'الأساس القانوني والبيانات الحساسة', 'fields' => [
                    ['key' => 's33_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's33_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '3.4', 'title' => 'مشاركة بياناتك', 'fields' => [
                    ['key' => 's34_title',   'label' => 'العنوان',                 'type' => 'text'],
                    ['key' => 's34_no_sell', 'label' => 'نص التأكيد (عدم البيع)',   'type' => 'textarea'],
                    ['key' => 's34_intro',   'label' => 'المقدمة',                 'type' => 'textarea'],
                    ['key' => 's34_items',   'label' => 'عناصر القائمة',           'type' => 'list'],
                ]],
                ['number' => '3.5', 'title' => 'نقل البيانات دوليًا', 'fields' => [
                    ['key' => 's35_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's35_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '3.6', 'title' => 'الاحتفاظ بالبيانات', 'fields' => [
                    ['key' => 's36_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's36_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '3.7', 'title' => 'حقوقك', 'fields' => [
                    ['key' => 's37_title', 'label' => 'العنوان',                                   'type' => 'text'],
                    ['key' => 's37_intro', 'label' => 'المقدمة',                                   'type' => 'textarea'],
                    ['key' => 's37_items', 'label' => 'عناصر القائمة',                             'type' => 'list'],
                    ['key' => 's37_gdpr',  'label' => 'نص حقوق سكان الاتحاد الأوروبي والمملكة المتحدة', 'type' => 'textarea'],
                    ['key' => 's37_contact_note', 'label' => 'نص قبل رابط البريد الإلكتروني',       'type' => 'textarea'],
                ]],
                ['number' => '3.8', 'title' => 'أمن البيانات', 'fields' => [
                    ['key' => 's38_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's38_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => '3.9', 'title' => 'ملفات تعريف الارتباط (Cookies)', 'fields' => [
                    ['key' => 's39_title',  'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 's39_intro',  'label' => 'المقدمة',       'type' => 'textarea'],
                    ['key' => 's39_items',  'label' => 'عناصر القائمة', 'type' => 'list'],
                    ['key' => 's39_footer', 'label' => 'نص ختامي',      'type' => 'textarea'],
                ]],
                ['number' => '3.10', 'title' => 'التعديلات على سياسة الخصوصية', 'fields' => [
                    ['key' => 's310_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 's310_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
            ],

            'terms_of_service' => [
                ['number' => null, 'title' => 'عنوان الصفحة', 'fields' => [
                    ['key' => 'title',        'label' => 'عنوان الصفحة',     'type' => 'text'],
                    ['key' => 'badge',        'label' => 'الشارة (Badge)',   'type' => 'text'],
                    ['key' => 'last_updated', 'label' => 'تاريخ آخر تحديث',  'type' => 'text'],
                    ['key' => 'intro',        'label' => 'الفقرة التعريفية', 'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'القبول بالشروط', 'fields' => [
                    ['key' => 'acceptance_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'acceptance_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'استخدام الخدمة', 'fields' => [
                    ['key' => 'use_title',      'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'use_intro',      'label' => 'المقدمة', 'type' => 'textarea'],
                    ['key' => 'use_items_bold', 'label' => 'العناوين الفرعية (سطر لكل عنصر)', 'type' => 'list', 'paired_with' => 'use_items_text'],
                    ['key' => 'use_items_text', 'label' => 'النصوص المقابلة (بنفس الترتيب)',   'type' => 'list', 'paired_with' => 'use_items_bold'],
                ]],
                ['number' => null, 'title' => 'الاشتراكات والمدفوعات', 'fields' => [
                    ['key' => 'payments_title', 'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 'payments_intro', 'label' => 'المقدمة',       'type' => 'textarea'],
                    ['key' => 'payments_items', 'label' => 'عناصر القائمة', 'type' => 'list'],
                ]],
                ['number' => null, 'title' => 'الملكية الفكرية', 'fields' => [
                    ['key' => 'ip_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'ip_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'الأنشطة المحظورة', 'fields' => [
                    ['key' => 'prohibited_title', 'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 'prohibited_items', 'label' => 'عناصر القائمة', 'type' => 'list'],
                ]],
                ['number' => null, 'title' => 'إنهاء الحساب', 'fields' => [
                    ['key' => 'termination_title', 'label' => 'العنوان',       'type' => 'text'],
                    ['key' => 'termination_items', 'label' => 'عناصر القائمة', 'type' => 'list'],
                ]],
                ['number' => null, 'title' => 'إخلاء المسؤولية', 'fields' => [
                    ['key' => 'disclaimer_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'disclaimer_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'القانون الحاكم', 'fields' => [
                    ['key' => 'law_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'law_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'التعديلات على الشروط', 'fields' => [
                    ['key' => 'changes_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'changes_body',  'label' => 'النص',    'type' => 'textarea'],
                ]],
                ['number' => null, 'title' => 'تواصل معانا', 'fields' => [
                    ['key' => 'contact_title', 'label' => 'العنوان', 'type' => 'text'],
                    ['key' => 'contact_intro', 'label' => 'النص',    'type' => 'textarea'],
                ]],
            ],

            default => [],
        };
    }

    public function index()
    {
        $pages = collect($this->pages())->map(fn ($meta, $key) => $meta + ['key' => $key])->values();

        return view('app.admin.pages.index', compact('pages'));
    }

    public function edit(string $page)
    {
        abort_unless(array_key_exists($page, $this->pages()), 404);

        $meta     = $this->pages()[$page];
        $sections = $this->schema($page);
        $content  = PageContent::forPage($page);

        return view('app.admin.pages.edit', compact('page', 'meta', 'sections', 'content'));
    }

    public function update(Request $request, string $page)
    {
        abort_unless(array_key_exists($page, $this->pages()), 404);

        $allFields = collect($this->schema($page))->flatMap(fn ($section) => $section['fields']);

        $pageContentFields = $allFields->whereIn('type', ['text', 'textarea', 'list']);
        $settingSingleFields = $allFields->where('type', 'setting_single');
        $settingBilingualFields = $allFields->where('type', 'setting_bilingual');

        $rules = [];
        foreach ($pageContentFields as $field) {
            $rules["fields.{$field['key']}.ar"] = 'nullable|string';
            $rules["fields.{$field['key']}.en"] = 'nullable|string';
        }
        foreach ($settingSingleFields as $field) {
            $rules["settings_single.{$field['key']}"] = 'nullable|string';
        }
        foreach ($settingBilingualFields as $field) {
            $rules["settings_bilingual.{$field['key']}.ar"] = 'nullable|string';
            $rules["settings_bilingual.{$field['key']}.en"] = 'nullable|string';
        }

        $data = $request->validate($rules);

        // A submitted empty value never overwrites existing non-empty content —
        // guards against a partial/broken form submission silently wiping real
        // content (e.g. a field missing from the request, or a display bug that
        // renders a textarea empty even though the DB has content).
        $keep = fn (string $incoming, ?string $existing) => ($incoming === '' && $existing !== null && $existing !== '')
            ? $existing
            : $incoming;

        $existingByKey = PageContent::forPage($page);

        foreach ($pageContentFields as $field) {
            $key      = $field['key'];
            $existing = $existingByKey->get($key);

            $newAr = strip_tags($data['fields'][$key]['ar'] ?? '');
            $newEn = strip_tags($data['fields'][$key]['en'] ?? '');

            PageContent::updateOrCreate(
                ['page' => $page, 'field_key' => $key],
                [
                    // Plain text only — no raw HTML from admin input.
                    'value_ar' => $keep($newAr, $existing?->value_ar),
                    'value_en' => $keep($newEn, $existing?->value_en),
                ]
            );
        }

        foreach ($settingSingleFields as $field) {
            $key = $field['key'];
            $new = strip_tags($data['settings_single'][$key] ?? '');
            Setting::set($key, $keep($new, Setting::get($key)), 'contact_region');
        }

        foreach ($settingBilingualFields as $field) {
            $key   = $field['key'];
            $newAr = strip_tags($data['settings_bilingual'][$key]['ar'] ?? '');
            $newEn = strip_tags($data['settings_bilingual'][$key]['en'] ?? '');

            Setting::set($key . '_ar', $keep($newAr, Setting::get($key . '_ar')), 'contact_region');
            Setting::set($key . '_en', $keep($newEn, Setting::get($key . '_en')), 'contact_region');
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', 'تم حفظ محتوى الصفحة بنجاح');
    }
}
