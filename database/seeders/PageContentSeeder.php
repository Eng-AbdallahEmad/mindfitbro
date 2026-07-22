<?php

namespace Database\Seeders;

use App\Models\PageContent;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Verbatim import of the current lang-file content for the 4 legal/info
     * pages, plus the dedicated contact_page_* settings rows. Idempotent —
     * uses firstOrCreate so it never duplicates rows or overwrites content
     * an admin has since edited via the dashboard.
     */
    public function run(): void
    {
        $pages = [

            'about_us' => [
                'title' => [
                    'ar' => 'من نحن',
                    'en' => 'About Us',
                ],
                'badge' => [
                    'ar' => 'تعرف على MindFitBro',
                    'en' => 'About MindFitBro',
                ],
                'meta_description' => [
                    'ar' => 'تعرف على MindFitBro، خدمة تدريب رياضي وتغذية أونلاين أسسها مدرب شخصي ومدرب تغذية معتمد، وتعمل تحت مظلة القانون المصري من القاهرة، مصر.',
                    'en' => 'Learn about MindFitBro, an online fitness and nutrition coaching service founded by a certified personal trainer and nutrition coach, operated under Egyptian law from Cairo, Egypt.',
                ],
                'intro' => [
                    'ar' => 'MindFitBro هي خدمة تدريب رياضي وتغذية أونلاين، بتساعد عملاءها حول العالم يبنوا عادات صحية مستدامة ومخصصة ليهم.',
                    'en' => 'MindFitBro is an online fitness and nutrition coaching service helping clients worldwide build sustainable, personalized health habits.',
                ],
                'approach_title' => [
                    'ar' => 'منهجنا',
                    'en' => 'Our Approach',
                ],
                'approach_body' => [
                    'ar' => 'بنجمع بين مدربين شخصيين معتمدين وأخصائيين تغذية معتمدين، مع متابعة منظمة، عشان نصمم خطط تمرين وتغذية مخصصة لهدف كل عميل وأسلوب حياته وحالته الصحية.',
                    'en' => 'We combine certified personal trainers and certified nutrition specialists with structured follow-up to design workout and nutrition plans tailored to each client\'s goals, lifestyle, and health background.',
                ],
                'founder_title' => [
                    'ar' => 'مؤسس المنصة',
                    'en' => 'Our Founder',
                ],
                'founder_body' => [
                    'ar' => 'MindFitBro اتأسست على إيد مدرب شخصي معتمد ومدرب تغذية رياضية بخبرة أكتر من 10 سنين في التدريب المباشر، وشهادات معتمدة عالمياً في التدريب الشخصي وتغذية الرياضيين. المنصة اتبنت عشان توصّل تدريب منظم ومبني على أسس علمية لعملاء المنطقة وخارجها.',
                    'en' => 'MindFitBro was founded by a certified personal trainer and nutrition coach with 10+ years of hands-on coaching experience and internationally recognized certifications in personal training and sports nutrition. The platform was built to bring structured, evidence-based coaching to clients across the region and beyond.',
                ],
                'legal_title' => [
                    'ar' => 'المعلومات القانونية',
                    'en' => 'Legal Information',
                ],
                'legal_law' => [
                    'ar' => 'MindFitBro بتعمل تحت مظلة القانون المصري.',
                    'en' => 'MindFitBro is operated under Egyptian law.',
                ],
                'legal_address_label' => [
                    'ar' => 'العنوان التجاري:',
                    'en' => 'Business Address:',
                ],
                'legal_address' => [
                    'ar' => 'القاهرة، مصر',
                    'en' => 'Cairo, Egypt',
                ],
            ],

            'contact_us' => [
                'title' => [
                    'ar' => 'تواصل معنا',
                    'en' => 'Contact Us',
                ],
                'badge' => [
                    'ar' => 'ابقَ على تواصل',
                    'en' => 'Get In Touch',
                ],
                'meta_description' => [
                    'ar' => 'تواصل مع MindFitBro عبر البريد الإلكتروني أو الهاتف أو واتساب. اطّلع على ساعات العمل والعنوان في القاهرة، مصر — بنرد على كل الاستفسارات خلال 24 ساعة.',
                    'en' => 'Contact MindFitBro by email, phone, or WhatsApp. Find our business hours and Cairo, Egypt address — we respond to all inquiries within 24 hours.',
                ],
                'lead' => [
                    'ar' => 'عندك سؤال أو محتاج مساعدة في اشتراكك؟ تقدر تتواصل معانا من خلال أي قناة من القنوات دي.',
                    'en' => 'Have a question or need help with your subscription? Reach us through any of the channels below.',
                ],
                'response_note' => [
                    'ar' => 'بنستهدف الرد على كل الاستفسارات خلال 24 ساعة.',
                    'en' => 'We aim to respond to all inquiries within 24 hours.',
                ],
            ],

            'delivery_policy' => [
                'title' => [
                    'ar' => 'سياسة التسليم',
                    'en' => 'Delivery Policy',
                ],
                'badge' => [
                    'ar' => 'المستندات القانونية',
                    'en' => 'Legal Documents',
                ],
                'last_updated' => [
                    'ar' => 'آخر تحديث: يوليو 2026',
                    'en' => 'Last Updated: July 2026',
                ],
                'meta_description' => [
                    'ar' => 'سياسة التسليم الخاصة بـ MindFitBro: كيفية تسليم خطط التدريب الرياضي والتغذية الرقمية، الجدول الزمني، وماذا يحدث في حالة التأخير.',
                    'en' => 'MindFitBro Delivery Policy: how our digital fitness and nutrition coaching plans are delivered, timelines, and what happens if a delivery is late.',
                ],
                's41_title' => [
                    'ar' => '4.1 الخدمات الرقمية',
                    'en' => '4.1 Digital Services',
                ],
                's41_body' => [
                    'ar' => 'تقدم MindFitBro خدمات تدريب رياضي رقمية وخدمات شخصية أونلاين فقط. إننا لا نبيع أو نشحن أي منتجات مادية.',
                    'en' => 'MindFitBro provides digital fitness coaching and personalized online services only. We do not sell or ship physical products.',
                ],
                's42_title' => [
                    'ar' => '4.2 الجدول الزمني لتسليم الخدمة',
                    'en' => '4.2 Service Delivery Timeline',
                ],
                's42_intro' => [
                    'ar' => 'بعد تأكيد الدفع، سيتواصل معك فريقنا خلال 24 ساعة عبر البريد الإلكتروني أو واتساب لجمع المعلومات اللازمة لبناء برنامجك (مثل التاريخ الصحي، الأهداف، القياسات، وتفاصيل نمط الحياة).',
                    'en' => 'After payment is confirmed, our team will contact you within 24 hours via email or WhatsApp to collect the information needed to build your program (e.g., health history, goals, measurements, lifestyle details).',
                ],
                's42_items' => [
                    'ar' => implode("\n", [
                        'سيتم تسليم خطة التمرين والتغذية الشخصية الخاصة بك خلال 2–5 أيام عمل من استلام جميع المعلومات المطلوبة، وذلك حسب الباقة التي اخترتها.',
                        '"أيام العمل" تعني من الأحد إلى الخميس، باستثناء الإجازات الرسمية المعلنة في دولة تشغيلنا.',
                        'قد يحدث تأخير إذا كانت المعلومات المطلوبة غير مكتملة، أو خلال فترات مُعلن عنها صراحةً في صفحة تواصل معنا (مثل الإجازات الممتدة).',
                    ]),
                    'en' => implode("\n", [
                        'Your personalized workout and nutrition plan will be delivered within 2–5 business days of receiving all required information, depending on your selected package.',
                        '"Business days" means Sunday through Thursday, excluding official public holidays in our country of operation.',
                        'Delays may occur if required information is incomplete, or during periods explicitly announced on our Contact Us page (e.g., extended holidays).',
                    ]),
                ],
                's43_title' => [
                    'ar' => '4.3 ماذا يحدث في حالة التأخير من جانبنا',
                    'en' => '4.3 What Happens If We\'re Late',
                ],
                's43_body' => [
                    'ar' => 'إذا تأخر التسليم عن الفترة المحددة لأسباب تقع ضمن نطاق تحكمنا، يحق لك طلب إما تمديد اشتراكك بمقدار فترة التأخير، أو — إذا لم يكن برنامجك قد بدأ بعد — استرداد كامل وفقاً للقسم رقم 5.',
                    'en' => 'If delivery is delayed beyond the stated window due to reasons within our control, you may request either an extension of your subscription equal to the delay period, or — if your plan has not yet been started — a full refund under Section 5.',
                ],
                's44_title' => [
                    'ar' => '4.4 لا يوجد شحن مادي',
                    'en' => '4.4 No Physical Shipping',
                ],
                's44_body' => [
                    'ar' => 'بما أن جميع خدماتنا تُسلَّم رقمياً، فلا تُطبَّق أي رسوم شحن أو تكاليف توصيل.',
                    'en' => 'As all services are delivered digitally, no shipping fees or delivery charges apply.',
                ],
            ],

            'refund_policy' => [
                'title' => [
                    'ar' => 'سياسة الاسترداد والإلغاء',
                    'en' => 'Refund & Cancellation Policy',
                ],
                'badge' => [
                    'ar' => 'المستندات القانونية',
                    'en' => 'Legal Documents',
                ],
                'last_updated' => [
                    'ar' => 'آخر تحديث: يوليو 2026',
                    'en' => 'Last Updated: July 2026',
                ],
                'meta_description' => [
                    'ar' => 'سياسة الاسترداد والإلغاء الخاصة بـ MindFitBro: فترة الاسترداد الكامل 14 يوماً، شروط عدم الاسترداد بعد التسليم، شكاوى جودة الخدمة، ومدة معالجة الاسترداد.',
                    'en' => 'MindFitBro Refund & Cancellation Policy: 14-day full refund window, non-refundable conditions after plan delivery, service-quality complaints, and refund processing times.',
                ],
                'intro' => [
                    'ar' => 'تلتزم MindFitBro بتقديم تدريب عالي الجودة ومخصص لكل عميل. ونظراً لأن برامجنا تُبنى خصيصاً لكل عميل، فإن أهلية الاسترداد تعتمد على مدى تقدم برنامجك.',
                    'en' => 'MindFitBro is committed to providing high-quality, personalized coaching. Because our programs are custom-built for each client, refund eligibility depends on how far your program has progressed.',
                ],
                's51_title' => [
                    'ar' => '5.1 فترة الاسترداد الكامل',
                    'en' => '5.1 Full Refund Window',
                ],
                's51_intro' => [
                    'ar' => 'يحق لك طلب استرداد كامل خلال 14 يوماً تقويمياً من تاريخ الشراء، بشرط:',
                    'en' => 'You may request a full refund within 14 calendar days of purchase, provided that:',
                ],
                's51_items' => [
                    'ar' => implode("\n", [
                        'ألا تكون قد تمت أي مكالمة استشارة، و',
                        'ألا تكون قد تم إنشاء أو إرسال أي خطة تمرين أو تغذية شخصية إليك.',
                    ]),
                    'en' => implode("\n", [
                        'No consultation call has taken place, and',
                        'No personalized workout or nutrition plan has been created or sent to you.',
                    ]),
                ],
                's52_title' => [
                    'ar' => '5.2 بعد تسليم برنامجك',
                    'en' => '5.2 After Your Plan Has Been Delivered',
                ],
                's52_intro' => [
                    'ar' => 'بمجرد أن نرسل إليك أول خطة شخصية أو نُجري أول استشارة تدريب، تُعتبر الخدمة قد "بدأت". ومن تلك اللحظة:',
                    'en' => 'Once we have sent you your first personalized plan or conducted your first coaching consultation, the service is considered "started." From that point:',
                ],
                's52_items' => [
                    'ar' => implode("\n", [
                        'يصبح المبلغ المدفوع غير قابل للاسترداد، نظراً للطبيعة المخصصة وغير القابلة لإعادة الاستخدام للعمل المُنجز بالفعل.',
                        'لا يزال بإمكانك طلب تجميد/إيقاف اشتراكك مؤقتاً (راجع القسم 5.4).',
                    ]),
                    'en' => implode("\n", [
                        'The purchase becomes non-refundable, due to the customized, non-reusable nature of the work already completed.',
                        'You may still request to pause/freeze your subscription (see Section 5.4).',
                    ]),
                ],
                's53_title' => [
                    'ar' => '5.3 مشاكل جودة الخدمة',
                    'en' => '5.3 Service-Quality Issues',
                ],
                's53_before' => [
                    'ar' => 'إذا كنت تعتقد أن برنامجك أو التدريب المقدم لا يطابق ما تم الوعد به (مثل عدم كونه شخصياً، أو احتوائه على معلومات غير آمنة واقعياً، أو اختلافه جوهرياً عن وصف الباقة)، تواصل معنا على',
                    'en' => 'If you believe your plan or coaching does not match what was promised (e.g., not personalized, factually unsafe, or materially different from your package description), contact us at',
                ],
                's53_after' => [
                    'ar' => 'خلال 7 أيام من تاريخ التسليم. سنراجع الشكوى، وبناءً على تقديرنا، سنقدم تصحيحاً، أو ترقية، أو استرداداً جزئياً، أو استرداداً كاملاً وفقاً لنتيجة المراجعة.',
                    'en' => 'within 7 days of delivery. We will review the complaint and, at our discretion, offer a correction, an upgrade, a partial refund, or a full refund depending on the finding.',
                ],
                's54_title' => [
                    'ar' => '5.4 تجميد/إيقاف الاشتراك مؤقتاً',
                    'en' => '5.4 Freezing/Pausing a Subscription',
                ],
                's54_body' => [
                    'ar' => 'الاشتراكات عموماً غير مؤهلة للتجميد أو الإيقاف المؤقت، نظراً لأن خدماتنا تُقدَّم أونلاين ويمكن الوصول إليها من أي مكان. في ظروف استثنائية (مثل حالة طبية خطيرة)، يجوز لـ MindFitBro، وفقاً لتقديرها الخاص، الموافقة على تجميد مؤقت بعد مراجعة الطلب.',
                    'en' => 'Subscriptions are generally not eligible for freezing or pausing, as our services are delivered online and accessible from anywhere. In exceptional circumstances (e.g., a serious medical condition), MindFitBro may, at its sole discretion, approve a temporary freeze after reviewing the request.',
                ],
                's55_title' => [
                    'ar' => '5.5 العروض الترويجية',
                    'en' => '5.5 Promotional Offers',
                ],
                's55_body' => [
                    'ar' => 'تخضع المشتريات التي تتم خلال العروض الترويجية أو الحملات المخفضة لنفس شروط الاسترداد الموضحة في الأقسام 5.1–5.3، ما لم ينص العرض أو الحملة صراحةً على شروط استرداد أو إلغاء مختلفة قبل الشراء. وبإتمامك عملية الشراء، فإنك تقر وتوافق على أي شروط خاصة بذلك العرض.',
                    'en' => 'Purchases made during promotional offers or discounted campaigns are subject to the same refund terms set out in Sections 5.1–5.3, unless the specific promotion or offer expressly states different refund or cancellation conditions before purchase. By completing your purchase, you acknowledge and accept any such offer-specific terms.',
                ],
                's56_title' => [
                    'ar' => '5.6 معالجة الاسترداد',
                    'en' => '5.6 Refund Processing',
                ],
                's56_body' => [
                    'ar' => 'تتم معالجة المبالغ المستردة المعتمدة عبر وسيلة الدفع الأصلية نفسها خلال 7–14 يوم عمل، حسب البنك أو مزود الدفع الخاص بك. وتقع فروقات تحويل العملة أو رسوم البنك، إن وُجدت، خارج نطاق سيطرتنا.',
                    'en' => 'Approved refunds are processed via the original payment method within 7–14 business days, depending on your bank/payment provider. Currency conversion differences or bank fees, if any, are outside our control.',
                ],
                's57_title' => [
                    'ar' => '5.7 القانون الحاكم والنزاعات',
                    'en' => '5.7 Governing Law & Disputes',
                ],
                's57_body' => [
                    'ar' => 'تخضع هذه السياسة، وأي نزاع ينشأ عن استخدامك لخدمات MindFitBro، لقوانين جمهورية مصر العربية. وتُحال أي نزاعات لا يمكن حلها ودياً إلى المحاكم المختصة في القاهرة، دون الإخلال بأي حقوق حماية للمستهلك ذات طابع إلزامي قد تتمتع بها بموجب قانون دولة إقامتك.',
                    'en' => 'This policy, and any dispute arising from your use of MindFitBro\'s services, is governed by the laws of the Arab Republic of Egypt. Any dispute that cannot be resolved amicably shall be submitted to the competent courts of Cairo, without prejudice to any mandatory consumer-protection rights you may have under the law of your country of residence.',
                ],
                's6_title' => [
                    'ar' => '6. الشكاوى والاقتراحات',
                    'en' => '6. Complaints & Suggestions',
                ],
                's6_before' => [
                    'ar' => 'غير راضٍ تماماً، أو لديك ملاحظات؟ تواصل معنا مباشرة على',
                    'en' => 'Not fully satisfied, or have feedback? Contact us directly at',
                ],
                's6_or' => [
                    'ar' => 'أو',
                    'en' => 'or',
                ],
                's6_after' => [
                    'ar' => 'وسنرد عليك خلال 48 ساعة. نحتفظ بسجلات جميع تفاعلات الدعم لضمان الجودة وحل أي نزاعات بشكل عادل.',
                    'en' => 'and we will respond within 48 hours. We keep records of all support interactions to ensure quality and to resolve disputes fairly.',
                ],
            ],

            'privacy_policy' => [
                'title' => [
                    'ar' => 'سياسة الخصوصية',
                    'en' => 'Privacy Policy',
                ],
                'badge' => [
                    'ar' => 'المستندات القانونية',
                    'en' => 'Legal Documents',
                ],
                'last_updated' => [
                    'ar' => 'آخر تحديث: يوليو 2026',
                    'en' => 'Last Updated: July 2026',
                ],
                'intro' => [
                    'ar' => 'تحترم MindFitBro ("نحن" أو "لنا") خصوصيتك. توضح هذه السياسة البيانات التي نجمعها، وأسباب جمعها، وكيفية استخدامها وحمايتها.',
                    'en' => 'MindFitBro ("we," "us," or "our") respects your privacy. This policy explains what data we collect, why we collect it, and how we use and protect it.',
                ],

                's31_title' => [
                    'ar' => '3.1 المعلومات التي نجمعها',
                    'en' => '3.1 Information We Collect',
                ],
                's31_intro' => [
                    'ar' => 'قد نقوم بجمع المعلومات التالية:',
                    'en' => 'We may collect the following information:',
                ],
                's31_items_bold' => [
                    'ar' => implode("\n", [
                        'بيانات الهوية والتواصل:',
                        'بيانات الصحة واللياقة البدنية:',
                        'بيانات الدفع:',
                    ]),
                    'en' => implode("\n", [
                        'Identity & Contact Data:',
                        'Health & Fitness Data:',
                        'Payment Data:',
                    ]),
                ],
                's31_items_text' => [
                    'ar' => implode("\n", [
                        'مثل الاسم، البريد الإلكتروني، رقم الهاتف أو الواتساب، ودولة الإقامة.',
                        'مثل العمر، الوزن، الطول، قياسات الجسم، التاريخ الطبي، الإصابات، التفضيلات الغذائية، وأهداف اللياقة البدنية، وذلك بهدف إعداد برنامج تدريبي وغذائي مخصص لك.',
                        'تتم معالجة المدفوعات بشكل آمن من خلال مزود خدمة الدفع المعتمد لدينا، ولا نقوم بتخزين بيانات بطاقتك البنكية الكاملة على خوادمنا.',
                    ]),
                    'en' => implode("\n", [
                        'Such as your name, email address, phone or WhatsApp number, and country of residence.',
                        'Such as age, weight, height, body measurements, medical history, injuries, dietary preferences, and fitness goals, in order to prepare a personalized training and nutrition program for you.',
                        'Payments are processed securely through our approved payment service provider, and we do not store your full bank card details on our servers.',
                    ]),
                ],

                's32_title' => [
                    'ar' => '3.2 كيفية استخدام معلوماتك',
                    'en' => '3.2 How We Use Your Information',
                ],
                's32_intro' => [
                    'ar' => 'نستخدم معلوماتك للأغراض التالية:',
                    'en' => 'We use your information for the following purposes:',
                ],
                's32_items' => [
                    'ar' => implode("\n", [
                        'إعداد وتحديث برنامجك التدريبي والغذائي المخصص.',
                        'اختيار المدرب أو أخصائي التغذية المناسب لك.',
                        'التواصل معك بشأن برنامجك، والفواتير، وخدمات الدعم.',
                        'تحسين خدماتنا، وإرسال العروض أو التحديثات التسويقية في حال موافقتك على ذلك.',
                    ]),
                    'en' => implode("\n", [
                        'Preparing and updating your personalized training and nutrition program.',
                        'Selecting the trainer or nutrition specialist best suited to you.',
                        'Communicating with you about your program, billing, and support services.',
                        'Improving our services, and sending marketing offers or updates where you have consented to this.',
                    ]),
                ],

                's33_title' => [
                    'ar' => '3.3 الأساس القانوني والبيانات الحساسة',
                    'en' => '3.3 Legal Basis & Sensitive Data',
                ],
                's33_body' => [
                    'ar' => 'نظرًا لأن المعلومات الصحية والبدنية تُعد بيانات شخصية حساسة، فإننا لا نجمع إلا المعلومات الضرورية لتقديم خدماتنا، ولن نستخدمها لأي غرض آخر إلا بعد الحصول على موافقتك الصريحة.',
                    'en' => 'Because health and physical information is considered sensitive personal data, we only collect the information necessary to provide our services, and will not use it for any other purpose without your explicit consent.',
                ],

                's34_title' => [
                    'ar' => '3.4 مشاركة بياناتك',
                    'en' => '3.4 Sharing Your Data',
                ],
                's34_no_sell' => [
                    'ar' => 'نحن لا نبيع بياناتك لأي طرف.',
                    'en' => 'We do not sell your data to any party.',
                ],
                's34_intro' => [
                    'ar' => 'قد نشارك بياناتك فقط مع:',
                    'en' => 'We may only share your data with:',
                ],
                's34_items' => [
                    'ar' => implode("\n", [
                        'المدرب أو أخصائي التغذية المسؤول عن برنامجك (داخل فريق العمل).',
                        'مزود خدمة الدفع المعتمد لدينا، وذلك فقط لمعالجة عمليات الدفع.',
                        'مزودي الخدمات مثل خدمات الاستضافة أو البريد الإلكتروني، مع التزامهم بالحفاظ على سرية البيانات.',
                        'الجهات الحكومية أو القانونية إذا كان ذلك مطلوبًا بموجب القانون.',
                    ]),
                    'en' => implode("\n", [
                        'The trainer or nutrition specialist responsible for your program (within our team).',
                        'Our approved payment service provider, solely for the purpose of processing payments.',
                        'Service providers such as hosting or email services, who are bound to keep data confidential.',
                        'Government or legal authorities, where required to do so by law.',
                    ]),
                ],

                's35_title' => [
                    'ar' => '3.5 نقل البيانات دوليًا',
                    'en' => '3.5 International Data Transfer',
                ],
                's35_body' => [
                    'ar' => 'قد يتم نقل بياناتك أو معالجتها في دولة أخرى غير دولة إقامتك، بما في ذلك الدولة التي تعمل منها MindFitBro. وباستخدامك لخدماتنا، فإنك توافق على هذا النقل، مع التزامنا باتخاذ التدابير المناسبة لحماية بياناتك.',
                    'en' => 'Your data may be transferred to, and processed in, a country other than your country of residence, including the country in which MindFitBro operates. By using our services, you consent to this transfer, and we are committed to taking appropriate measures to protect your data.',
                ],

                's36_title' => [
                    'ar' => '3.6 الاحتفاظ بالبيانات',
                    'en' => '3.6 Data Retention',
                ],
                's36_body' => [
                    'ar' => 'نحتفظ ببياناتك طوال فترة نشاط حسابك، ولمدة سنتين بعد إغلاقه لأغراض قانونية وحفظ السجلات، ما لم تطلب حذفها قبل ذلك، وفقًا لما يسمح به القانون.',
                    'en' => 'We retain your data for as long as your account remains active, and for two years after its closure for legal and record-keeping purposes, unless you request its deletion earlier, as permitted by law.',
                ],

                's37_title' => [
                    'ar' => '3.7 حقوقك',
                    'en' => '3.7 Your Rights',
                ],
                's37_intro' => [
                    'ar' => 'يحق لك في أي وقت:',
                    'en' => 'You have the right, at any time, to:',
                ],
                's37_items' => [
                    'ar' => implode("\n", [
                        'طلب الوصول إلى بياناتك الشخصية.',
                        'طلب تصحيح أي بيانات غير دقيقة.',
                        'طلب حذف بياناتك الشخصية، وفقًا للقوانين المعمول بها.',
                    ]),
                    'en' => implode("\n", [
                        'Request access to your personal data.',
                        'Request correction of any inaccurate data.',
                        'Request deletion of your personal data, in accordance with applicable laws.',
                    ]),
                ],
                's37_gdpr' => [
                    'ar' => 'كما يتمتع المقيمون في دول الاتحاد الأوروبي أو المملكة المتحدة بحقوق إضافية بموجب قوانين حماية البيانات، مثل حق نقل البيانات والاعتراض على معالجتها.',
                    'en' => 'Residents of the European Union or the United Kingdom also have additional rights under data protection law, such as the right to data portability and the right to object to processing.',
                ],
                's37_contact_note' => [
                    'ar' => 'للاستفسارات أو لممارسة أي من هذه الحقوق، يرجى التواصل معنا عبر البريد الإلكتروني:',
                    'en' => 'For inquiries or to exercise any of these rights, please contact us by email:',
                ],

                's38_title' => [
                    'ar' => '3.8 أمن البيانات',
                    'en' => '3.8 Data Security',
                ],
                's38_body' => [
                    'ar' => 'نستخدم إجراءات تقنية وتنظيمية تتوافق مع المعايير المتعارف عليها في المجال لحماية بياناتك من الوصول غير المصرح به أو الفقدان أو سوء الاستخدام.',
                    'en' => 'We use technical and organizational measures consistent with industry-recognized standards to protect your data from unauthorized access, loss, or misuse.',
                ],

                's39_title' => [
                    'ar' => '3.9 ملفات تعريف الارتباط (Cookies)',
                    'en' => '3.9 Cookies',
                ],
                's39_intro' => [
                    'ar' => 'نستخدم ملفات تعريف الارتباط (Cookies) والتقنيات المشابهة على موقعنا الإلكتروني من أجل:',
                    'en' => 'We use cookies and similar technologies on our website in order to:',
                ],
                's39_items' => [
                    'ar' => implode("\n", [
                        'الحفاظ على تسجيل دخولك.',
                        'تذكر تفضيلاتك.',
                        'فهم كيفية استخدام الموقع لتحسين خدماتنا.',
                    ]),
                    'en' => implode("\n", [
                        'Keep you signed in.',
                        'Remember your preferences.',
                        'Understand how the site is used so we can improve our services.',
                    ]),
                ],
                's39_footer' => [
                    'ar' => 'يمكنك التحكم في ملفات تعريف الارتباط أو تعطيلها من خلال إعدادات المتصفح الخاص بك، مع العلم أن تعطيلها قد يؤثر في عمل بعض وظائف الموقع.',
                    'en' => 'You can control or disable cookies through your browser settings, noting that disabling them may affect the functioning of some site features.',
                ],

                's310_title' => [
                    'ar' => '3.10 التعديلات على سياسة الخصوصية',
                    'en' => '3.10 Changes to This Privacy Policy',
                ],
                's310_body' => [
                    'ar' => 'قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. ويُعد استمرارك في استخدام خدماتنا بعد نشر أي تحديث موافقةً منك على سياسة الخصوصية بصيغتها المعدلة.',
                    'en' => 'We may update this Privacy Policy from time to time. Your continued use of our services after any update is posted constitutes your acceptance of the Privacy Policy as amended.',
                ],
            ],

            'terms_of_service' => [
                'title' => [
                    'ar' => 'الشروط والأحكام',
                    'en' => 'Terms & Conditions',
                ],
                'badge' => [
                    'ar' => 'المستندات القانونية',
                    'en' => 'Legal Documents',
                ],
                'last_updated' => [
                    'ar' => 'آخر تحديث: 15 يوليو 2026',
                    'en' => 'Last updated: September 22, 2025',
                ],
                'intro' => [
                    'ar' => 'أهلاً بيك في MindFitBro. باستخدامك لموقعنا أو خدماتنا، بتوافق على الشروط والأحكام دي. برجاء قراءتها بعناية قبل ما تبدأ في استخدام منصتنا.',
                    'en' => 'Welcome to MindFitBro. By using our website or services, you agree to these Terms & Conditions. Please read them carefully before using our platform.',
                ],
                'acceptance_title' => [
                    'ar' => 'القبول بالشروط',
                    'en' => 'Acceptance of Terms',
                ],
                'acceptance_body' => [
                    'ar' => 'بمجرد دخولك على الموقع أو استخدامك لأي من خدماتنا، بتوافق تلقائياً على الالتزام بشروط الخدمة دي. لو مش موافق على أي جزء من الشروط، برجاء عدم استخدام خدماتنا.',
                    'en' => 'By accessing the website or using any of our services, you automatically agree to be bound by these Terms of Service. If you do not agree to any part of these terms, please do not use our services.',
                ],
                'use_title' => [
                    'ar' => 'استخدام الخدمة',
                    'en' => 'Use of Service',
                ],
                'use_intro' => [
                    'ar' => 'بتوافق على الالتزام بالآتي عند استخدام خدماتنا:',
                    'en' => 'You agree to the following when using our services:',
                ],
                'use_items_bold' => [
                    'ar' => implode("\n", [
                        'الاستخدام القانوني فقط:',
                        'صحة المعلومات:',
                        'حماية الحساب:',
                    ]),
                    'en' => implode("\n", [
                        'Legal Use Only:',
                        'Accurate Information:',
                        'Account Security:',
                    ]),
                ],
                'use_items_text' => [
                    'ar' => implode("\n", [
                        'عدم استخدام الخدمة لأي أغراض غير قانونية أو مخالفة لهذه الشروط.',
                        'تقديم بيانات دقيقة وصحيحة عند التسجيل أو إجراء أي عملية على المنصة.',
                        'الحفاظ على سرية بيانات تسجيل الدخول الخاصة بيك وعدم مشاركتها مع أي طرف آخر.',
                    ]),
                    'en' => implode("\n", [
                        'Do not use the service for any illegal purposes or in violation of these terms.',
                        'Provide accurate and truthful data when registering or performing any action on the platform.',
                        'Keep your login credentials confidential and do not share them with any third party.',
                    ]),
                ],
                'payments_title' => [
                    'ar' => 'الاشتراكات والمدفوعات',
                    'en' => 'Subscriptions & Payments',
                ],
                'payments_intro' => [
                    'ar' => 'بخصوص الاشتراكات والمدفوعات، يرجى العلم بالتالي:',
                    'en' => 'Regarding subscriptions and payments, please be aware of the following:',
                ],
                'payments_items' => [
                    'ar' => implode("\n", [
                        'بعض الخدمات مدفوعة وتتطلب اشتراكاً نشطاً للوصول إليها.',
                        'الأسعار قابلة للتغيير مع إشعار مسبق للمستخدمين.',
                        'يتم تجديد الاشتراكات تلقائياً ما لم تقم بإلغائها قبل انتهاء الفترة الحالية.',
                        'المبالغ المدفوعة غير قابلة للاسترداد إلا في حالات استثنائية وفق سياسة الاسترداد الخاصة بنا.',
                    ]),
                    'en' => implode("\n", [
                        'Some services are paid and require an active subscription to access.',
                        'Prices are subject to change with prior notice to users.',
                        'Subscriptions renew automatically unless cancelled before the current period ends.',
                        'Payments are non-refundable except in exceptional cases per our refund policy.',
                    ]),
                ],
                'ip_title' => [
                    'ar' => 'الملكية الفكرية',
                    'en' => 'Intellectual Property',
                ],
                'ip_body' => [
                    'ar' => 'جميع المحتويات المنشورة على المنصة من نصوص وصور وشعارات وتصميمات هي ملك حصري لـ MindFitBro ومحمية بموجب قوانين الملكية الفكرية. لا يُسمح بنسخ أي محتوى أو إعادة نشره أو استخدامه تجارياً بدون إذن كتابي مسبق منا.',
                    'en' => 'All content published on the platform — including text, images, logos, and designs — is the exclusive property of MindFitBro and is protected by intellectual property laws. No content may be copied, republished, or used commercially without prior written permission from us.',
                ],
                'prohibited_title' => [
                    'ar' => 'الأنشطة المحظورة',
                    'en' => 'Prohibited Activities',
                ],
                'prohibited_items' => [
                    'ar' => implode("\n", [
                        'نشر محتوى مسيء أو مضلل أو غير قانوني.',
                        'محاولة اختراق أو تعطيل المنصة.',
                        'انتحال شخصية أي مستخدم أو موظف.',
                    ]),
                    'en' => implode("\n", [
                        'Posting offensive, misleading, or unlawful content.',
                        'Attempting to hack or disrupt the platform.',
                        'Impersonating any user or employee.',
                    ]),
                ],
                'termination_title' => [
                    'ar' => 'إنهاء الحساب',
                    'en' => 'Account Termination',
                ],
                'termination_items' => [
                    'ar' => implode("\n", [
                        'يحق لنا إيقاف أو إنهاء حسابك عند مخالفة هذه الشروط.',
                        'يمكنك إلغاء حسابك في أي وقت من إعدادات الحساب.',
                        'بعض البيانات قد تُحتفظ بها وفق سياسة الخصوصية.',
                    ]),
                    'en' => implode("\n", [
                        'We reserve the right to suspend or terminate your account for violating these terms.',
                        'You may cancel your account at any time from account settings.',
                        'Some data may be retained in accordance with our Privacy Policy.',
                    ]),
                ],
                'disclaimer_title' => [
                    'ar' => 'إخلاء المسؤولية',
                    'en' => 'Disclaimer of Warranties',
                ],
                'disclaimer_body' => [
                    'ar' => 'نسعى دائماً لتقديم أفضل خدمة ممكنة، لكننا لا نضمن أن الخدمة ستكون خالية تماماً من الأخطاء أو الانقطاع. إحنا مش مسؤولين عن أي خسائر مباشرة أو غير مباشرة ناتجة عن استخدام أو عدم القدرة على استخدام الخدمة.',
                    'en' => 'We always strive to provide the best service possible, but we do not guarantee that the service will be entirely error-free or uninterrupted. We are not liable for any direct or indirect losses resulting from using or being unable to use the service.',
                ],
                'law_title' => [
                    'ar' => 'القانون الحاكم',
                    'en' => 'Governing Law',
                ],
                'law_body' => [
                    'ar' => 'تخضع هذه الشروط وتُفسَّر وفقاً للقوانين المعمول بها في المملكة العربية السعودية. أي نزاعات تنشأ عن استخدام الخدمة تخضع للاختصاص القضائي للمحاكم السعودية المختصة.',
                    'en' => 'These terms are governed by and construed in accordance with the laws applicable in the Kingdom of Saudi Arabia. Any disputes arising from use of the service are subject to the jurisdiction of the competent Saudi courts.',
                ],
                'changes_title' => [
                    'ar' => 'التعديلات على الشروط',
                    'en' => 'Changes to Terms',
                ],
                'changes_body' => [
                    'ar' => 'محتفظين بحقنا في تعديل شروط الخدمة دي في أي وقت. هنعلمك بأي تغييرات جوهرية عبر البريد الإلكتروني أو بإشعار واضح على الموقع. استمرارك في استخدام الخدمة بعد نشر التعديلات يعني موافقتك عليها.',
                    'en' => 'We reserve the right to modify these Terms of Service at any time. We will notify you of any significant changes via email or a clear notice on the website. Continued use of the service after changes are posted constitutes your acceptance of the updated terms.',
                ],
                'contact_title' => [
                    'ar' => 'تواصل معانا',
                    'en' => 'Contact Us',
                ],
                'contact_intro' => [
                    'ar' => 'لو عندك أي أسئلة بخصوص شروط الخدمة دي، تقدر تتواصل معانا:',
                    'en' => 'If you have any questions about these Terms of Service, feel free to reach out:',
                ],
            ],

        ];

        // ── One-time cleanup: the Privacy Policy was restructured from the
        //    old 3-collect/how_use/sharing/... field set to the new 3.1–3.10
        //    numbered structure (client-approved content). Delete the 30
        //    obsolete keys by name so firstOrCreate below inserts the new
        //    content fresh (targets old key names only, so it's a no-op on
        //    repeat runs and never touches an admin's later edits to the
        //    new keys). ────────────────────────────────────────────────
        $obsoletePrivacyKeys = [
            'title', 'badge', 'last_updated', 'intro',
            'collect_title', 'collect_intro', 'collect_items_bold', 'collect_items_text',
            'how_use_title', 'how_use_intro', 'how_use_items',
            'sharing_title', 'no_sell', 'sharing_intro', 'sharing_items',
            'security_title', 'security_body',
            'cookies_title', 'cookies_body',
            'rights_title', 'rights_items', 'rights_contact',
            'third_party_title', 'third_party_body',
            'children_title', 'children_body',
            'changes_title', 'changes_body',
            'contact_title', 'contact_intro',
        ];

        PageContent::where('page', 'privacy_policy')->whereIn('field_key', $obsoletePrivacyKeys)->delete();

        foreach ($pages as $page => $fields) {
            foreach ($fields as $fieldKey => $values) {
                PageContent::firstOrCreate(
                    ['page' => $page, 'field_key' => $fieldKey],
                    ['value_ar' => $values['ar'], 'value_en' => $values['en']]
                );
            }
        }

        // ── Region-scoped contact settings (kept separate from the
        //    footer's existing contact_phone/contact_email/whatsapp_number
        //    keys so the footer is not affected) ──────────────────────
        $regionContactSettings = [
            // Egypt
            'contact_eg_email'             => 'info@mindfitbro.com',
            'contact_eg_phone'             => '+201141483981',
            'contact_eg_whatsapp'          => '201141483981',
            'contact_eg_phone_placeholder' => '+20 1xx xxx xxxx',
            'contact_eg_hours_ar'          => 'يومياً، من 10 صباحاً – 8 مساءً (بتوقيت القاهرة)',
            'contact_eg_hours_en'          => 'Every day, 10 AM – 8 PM (Cairo time)',
            'contact_eg_address_ar'        => 'القاهرة، مصر',
            'contact_eg_address_en'        => 'Cairo, Egypt',

            // International (Saudi Arabia & rest of world)
            'contact_intl_email'             => 'info@mindfitbro.com',
            'contact_intl_phone'             => '+966593035979',
            'contact_intl_whatsapp'          => '966593035979',
            'contact_intl_phone_placeholder' => '+966 5xx xxx xxx',
            'contact_intl_hours_ar'          => 'يومياً، من 10 صباحاً – 8 مساءً (بتوقيت الرياض)',
            'contact_intl_hours_en'          => 'Every day, 10 AM – 8 PM (Riyadh time)',
            'contact_intl_address_ar'        => 'الرياض، المملكة العربية السعودية',
            'contact_intl_address_en'        => 'Riyadh, Saudi Arabia',
        ];

        foreach ($regionContactSettings as $key => $value) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'contact_region']
            );
        }

        // Superseded by the region-scoped keys above — safe/idempotent to
        // run repeatedly, removes them once and is a no-op after that.
        Setting::where('group', 'contact_page')->delete();
    }
}
