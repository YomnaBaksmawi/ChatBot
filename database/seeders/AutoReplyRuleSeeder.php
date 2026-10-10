<?php

namespace Database\Seeders;

use App\Models\AutoReplyRule;
use Illuminate\Database\Seeder;

class AutoReplyRuleSeeder extends Seeder
{
    public function run(): void
    {
        AutoReplyRule::create([
            'keywords' => [
                'مرحبا',
                'اهلا',
                'أهلا',
                'السلام عليكم',
            ],
            'response' => 'أهلاً وسهلاً! كيف يمكنني مساعدتك؟',
            'is_active' => true,
        ]);

        AutoReplyRule::create([
            'keywords' => [
                'مع السلامة',
                'باي',
                'وداعا',
                'وداعًا',
            ],
            'response' => 'مع السلامة! نتمنى لك يومًا سعيدًا.',
            'is_active' => true,
        ]);

        AutoReplyRule::create([
            'keywords' => [
                'السعر',
                'الأسعار',
                'التكلفة',
                'كم السعر',
            ],
            'response' => 'يمكنك معرفة الأسعار والتكاليف الخاصة بخدماتنا.',
            'is_active' => true,
        ]);

        AutoReplyRule::create([
            'keywords' => [
                'مساعدة',
                'ساعدني',
                'الدعم',
                'خدمة العملاء',
            ],
            'response' => 'بالتأكيد! أخبرني كيف يمكنني مساعدتك.',
            'is_active' => true,
        ]);

        AutoReplyRule::create([
            'keywords' => [
                'ساعات العمل',
                'متى نعمل',
                'مواعيد العمل',
                'أوقات العمل',
            ],
            'response' => 'نعمل من الساعة 9 صباحًا حتى 6 مساءً.',
            'is_active' => true,
        ]);
        AutoReplyRule::create([
    'keywords' => [],
    'response' => 'شكرًا لتواصلك معنا. لم أفهم طلبك بشكل كامل، وسيتم تحويل المحادثة إلى موظف الدعم.',
    'is_active' => true,
]);
    }
}