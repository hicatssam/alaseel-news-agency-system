<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Category;
use App\Models\Journalist;
use App\Models\Tag;
use App\Models\Article;
use App\Models\Video;
use App\Models\Setting;
use App\Models\Advertisement;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1) الأدوار
        // ============================================
        $superAdmin = Role::create(['name'=>'مدير عام','slug'=>'super-admin','description'=>'صلاحيات كاملة']);
        $editor     = Role::create(['name'=>'محرر','slug'=>'editor','description'=>'تحرير المقالات']);
        $journalist = Role::create(['name'=>'صحفي','slug'=>'journalist','description'=>'كتابة المقالات']);

        // ============================================
        // 2) الصلاحيات
        // ============================================
        $perms = [
            ['name'=>'إدارة المقالات','slug'=>'manage-articles','module'=>'articles'],
            ['name'=>'نشر المقالات','slug'=>'publish-articles','module'=>'articles'],
            ['name'=>'إدارة المستخدمين','slug'=>'manage-users','module'=>'users'],
            ['name'=>'إدارة التصنيفات','slug'=>'manage-categories','module'=>'categories'],
            ['name'=>'إدارة الإعلانات','slug'=>'manage-ads','module'=>'advertisements'],
            ['name'=>'إدارة الإعدادات','slug'=>'manage-settings','module'=>'settings'],
            ['name'=>'عرض التقارير','slug'=>'view-reports','module'=>'reports'],
            ['name'=>'إدارة الوسائط','slug'=>'manage-media','module'=>'media'],
        ];

        $permModels = [];
        foreach ($perms as $perm) {
            $permModels[$perm['slug']] = Permission::create($perm);
        }

        // ============================================
        // 3) ربط الصلاحيات بالأدوار (كانت ناقصة بالكامل)
        // ============================================

        // super-admin: كل الصلاحيات بدون استثناء
        $superAdmin->permissions()->attach(
            collect($permModels)->pluck('id')->all()
        );

        // editor: كل شي متعلق بالمحتوى، بدون إدارة المستخدمين والإعدادات
        $editor->permissions()->attach([
            $permModels['manage-articles']->id,
            $permModels['publish-articles']->id,
            $permModels['manage-categories']->id,
            $permModels['manage-media']->id,
            $permModels['view-reports']->id,
        ]);

        // journalist: بس إدارة المقالات (بدون نشر، بدون أي شي إداري)
        $journalist->permissions()->attach([
            $permModels['manage-articles']->id,
        ]);

        // ============================================
        // 4) المستخدمون (أضفنا مستخدم صحفي كان ناقص)
        // ============================================
        $adminUser = User::create([
            'name'=>'المدير العام','email'=>'admin@alaseel.news',
            'password'=>Hash::make('password'),'status'=>true,
        ]);
        $adminUser->roles()->attach($superAdmin);

        $editorUser = User::create([
            'name'=>'محمد الأمين','email'=>'editor@alaseel.news',
            'password'=>Hash::make('password'),'status'=>true,
        ]);
        $editorUser->roles()->attach($editor);

        $journalistUser = User::create([
            'name'=>'أحمد الرشيدي','email'=>'journalist@alaseel.news',
            'password'=>Hash::make('password'),'status'=>true,
        ]);
        $journalistUser->roles()->attach($journalist);

        // ============================================
        // 5) التصنيفات
        // ============================================
        $categoriesData = [
            ['name'=>'السياسة',    'slug'=>'politics',    'sort_order'=>1],
            ['name'=>'الاقتصاد',   'slug'=>'economy',     'sort_order'=>2],
            ['name'=>'الرياضة',    'slug'=>'sports',      'sort_order'=>3],
            ['name'=>'التكنولوجيا','slug'=>'technology',  'sort_order'=>4],
            ['name'=>'الثقافة',    'slug'=>'culture',     'sort_order'=>5],
            ['name'=>'الصحة',      'slug'=>'health',      'sort_order'=>6],
            ['name'=>'المجتمع',    'slug'=>'society',     'sort_order'=>7],
            ['name'=>'العالم',     'slug'=>'world',       'sort_order'=>8],
            ['name'=>'محليات',     'slug'=>'local',       'sort_order'=>9],
            ['name'=>'علوم',       'slug'=>'science',     'sort_order'=>10],
        ];
        $catModels = [];
        foreach ($categoriesData as $cat) {
            $catModels[$cat['slug']] = Category::create(array_merge($cat,['status'=>true]));
        }

        // ============================================
        // 6) الصحفيون (Journalist profiles - جدول منفصل عن users)
        // ============================================
        $journalistData = [
            ['name'=>'أحمد الرشيدي','email'=>'ahmed@alaseel.news','job_title'=>'مراسل سياسي',
             'bio'=>'صحفي متمرس متخصص في الشؤون السياسية.','status'=>true],
            ['name'=>'فاطمة الزهراء','email'=>'fatima@alaseel.news','job_title'=>'محررة اقتصادية',
             'bio'=>'متخصصة في الأسواق المالية والاستثمار.','status'=>true],
            ['name'=>'خالد المنصور','email'=>'khalid@alaseel.news','job_title'=>'مراسل رياضي',
             'bio'=>'يغطي بطولات الخليج والمنافسات الدولية.','status'=>true],
            ['name'=>'سارة العتيبي','email'=>'sara@alaseel.news','job_title'=>'محررة ثقافية',
             'bio'=>'مهتمة بالتراث العربي والفنون.','status'=>true],
            ['name'=>'عمر البلوشي','email'=>'omar@alaseel.news','job_title'=>'مراسل تقني',
             'bio'=>'متخصص في التكنولوجيا والذكاء الاصطناعي.','status'=>true],
        ];
        $jrnModels = [];
        foreach ($journalistData as $j) {
            $jrnModels[] = Journalist::create($j);
        }

        // ============================================
        // 7) الوسوم
        // ============================================
        $tags = ['السياسة الخارجية','الاقتصاد الكلي','كأس العالم','الذكاء الاصطناعي',
                 'التراث','الصحة العامة','التعليم','البيئة','الطاقة','الأمن',
                 'الاستثمار','ريادة الأعمال','الشباب','المرأة','التنمية'];
        $tagModels = [];
        foreach ($tags as $t) {
            $tagModels[] = Tag::create(['name'=>$t,'slug'=>Str::slug($t),'status'=>true]);
        }

        // ============================================
        // 8) المقالات
        // ============================================
        $articlesData = [
            ['title'=>'قمة عربية استثنائية لبحث التطورات الإقليمية',
             'summary'=>'انعقدت قمة عربية طارئة لمناقشة المستجدات السياسية والأمنية',
             'content'=>'<p>عقد القادة العرب قمة استثنائية ناقشوا فيها القضايا الإقليمية الملحة. وأكد المجتمعون أهمية التضامن العربي ودعوا إلى تعزيز آليات التعاون.</p><p>وأصدر المجتمعون بياناً ختامياً أكدوا فيه دعم جهود الاستقرار الإقليمي.</p>',
             'cat'=>'politics','jrn'=>0,'breaking'=>true,'featured'=>true,'pick'=>false,'views'=>4250],
            ['title'=>'ارتفاع ملحوظ في أسواق الأسهم الخليجية مع تحسن النفط',
             'summary'=>'شهدت أسواق الأسهم الخليجية ارتفاعاً بدعم من بيانات اقتصادية إيجابية',
             'content'=>'<p>أغلقت الأسواق المالية الخليجية مرتفعة، مستفيدةً من ارتفاع أسعار النفط وتحسن توقعات النمو الاقتصادي العالمي.</p><p>وسجّل مؤشر تداول السعودي ارتفاعاً بنسبة 2.3% في ختام جلسة تداول نشطة.</p>',
             'cat'=>'economy','jrn'=>1,'breaking'=>false,'featured'=>true,'pick'=>false,'views'=>3100],
            ['title'=>'المنتخب الوطني يتأهل للنهائيات الآسيوية بفوز تاريخي',
             'summary'=>'حقق المنتخب تأهلاً تاريخياً للنهائيات بعد فوز مثير بهدفين مقابل هدف',
             'content'=>'<p>كتب المنتخب الوطني صفحة مشرقة في تاريخه إذ تأهل للنهائيات الآسيوية بعد انتصار درامي. وأبدى اللاعبون أداءً متميزاً طوال مباريات التصفيات.</p>',
             'cat'=>'sports','jrn'=>2,'breaking'=>false,'featured'=>true,'pick'=>false,'views'=>5800],
            ['title'=>'إطلاق مبادرة وطنية لتعزيز الذكاء الاصطناعي وتقنيات المستقبل',
             'summary'=>'أطلقت الحكومة مبادرة شاملة لتطوير قطاع التكنولوجيا والذكاء الاصطناعي',
             'content'=>'<p>كشفت الحكومة عن مبادرة وطنية طموحة تتضمن تأهيل مليون متخصص في الذكاء الاصطناعي بحلول 2030 وإنشاء مراكز ابتكار متطورة.</p>',
             'cat'=>'technology','jrn'=>4,'breaking'=>false,'featured'=>false,'pick'=>true,'views'=>2900],
            ['title'=>'مهرجان الفنون التراثية يحتفي بالموروث الشعبي الأصيل',
             'summary'=>'انطلقت فعاليات مهرجان الفنون التراثية بحضور واسع وتنوع ثقافي',
             'content'=>'<p>انطلقت فعاليات مهرجان الفنون التراثية بمشاركة فرق من مختلف المناطق في احتفاء بالموروث الشعبي الأصيل وعروض الحرف اليدوية.</p>',
             'cat'=>'culture','jrn'=>3,'breaking'=>false,'featured'=>false,'pick'=>true,'views'=>1700],
            ['title'=>'اختراق طبي في علاج السرطان يُبشّر بنتائج واعدة',
             'summary'=>'أعلن فريق طبي عن اختراق علمي في مجال علاج بعض أنواع السرطان',
             'content'=>'<p>أعلن فريق من الأطباء والباحثين عن نتائج واعدة في تجارب علاجية جديدة تعتمد الطب الدقيق والعلاج المناعي لمكافحة السرطان.</p>',
             'cat'=>'health','jrn'=>1,'breaking'=>false,'featured'=>false,'pick'=>false,'views'=>3400],
            ['title'=>'مشروع الطاقة الشمسية الأضخم في المنطقة يدخل مرحلة التشغيل',
             'summary'=>'بدأ المشروع الشمسي العملاق العمليات التجريبية لتزويد نصف مليون منزل',
             'content'=>'<p>دشّن المسؤولون أكبر محطة للطاقة الشمسية في المنطقة باستثمارات تتجاوز ملياري دولار لتوفير كهرباء نظيفة لمئات الآلاف من المنازل.</p>',
             'cat'=>'economy','jrn'=>0,'breaking'=>false,'featured'=>false,'pick'=>false,'views'=>2200],
            ['title'=>'دراسة تكشف تأثيرات وسائل التواصل الاجتماعي على صحة الشباب',
             'summary'=>'رصدت دراسة أكاديمية تأثيرات إيجابية وسلبية لمنصات التواصل الاجتماعي',
             'content'=>'<p>كشفت دراسة شاملة عن تأثيرات متباينة للتواصل الاجتماعي، حيث يرتبط الاستخدام المفرط بمؤشرات القلق فيما يُسهم المعتدل في تطوير مهارات التواصل.</p>',
             'cat'=>'society','jrn'=>3,'breaking'=>false,'featured'=>false,'pick'=>false,'views'=>1900],
            ['title'=>'الاكتشافات الأثرية تُعيد رسم خريطة الحضارات القديمة',
             'summary'=>'كشفت حفريات أثرية عن مواقع جديدة تُغيّر الفهم الراهن للتاريخ',
             'content'=>'<p>أسفرت حملات التنقيب الأثرية الأخيرة عن اكتشافات مثيرة تُعيد قراءة صفحات التاريخ وتوثيق الحضارات العريقة التي قطنت المنطقة.</p>',
             'cat'=>'science','jrn'=>4,'breaking'=>false,'featured'=>false,'pick'=>false,'views'=>1500],
            ['title'=>'مبادرات التحول الرقمي تُحدث نقلة في قطاع التعليم',
             'summary'=>'أطلقت وزارة التعليم حزمة من المبادرات الرقمية لتطوير المناهج',
             'content'=>'<p>أطلقت وزارة التعليم حزمة مبادرات رقمية تهدف إلى تحديث المناهج الدراسية وتوظيف التكنولوجيا في تعزيز جودة التعلم.</p>',
             'cat'=>'local','jrn'=>2,'breaking'=>false,'featured'=>false,'pick'=>false,'views'=>2100],
        ];

        foreach ($articlesData as $i => $a) {
            $slug = Str::slug($a['title']) . '-' . Str::random(5);
            $article = Article::create([
                'title'              => $a['title'],
                'slug'               => $slug,
                'summary'            => $a['summary'],
                'content'            => $a['content'],
                'category_id'        => $catModels[$a['cat']]->id,
                'journalist_id'      => $jrnModels[$a['jrn']]->id,
                'user_id'            => $adminUser->id,
                'status'             => 'published',
                'is_breaking'        => $a['breaking'],
                'is_featured'        => $a['featured'],
                'is_editor_pick'     => $a['pick'],
                'published_at'       => now()->subHours($i * 4),
                'views'              => $a['views'],
                'verification_status'=> 'verified',
            ]);
            $randomTags = collect($tagModels)->random(rand(2,4))->pluck('id')->toArray();
            $article->tags()->sync($randomTags);
        }

        // ============================================
        // 9) الفيديوهات
        // ============================================
        $videosData = [
            ['title'=>'تقرير خاص: مستقبل الطاقة في الخليج','cat'=>'economy','featured'=>true],
            ['title'=>'ملف الأسبوع: التحولات الجيوسياسية في المنطقة','cat'=>'politics','featured'=>false],
            ['title'=>'حوار مع أبطال الرياضة الخليجية','cat'=>'sports','featured'=>true],
        ];
        foreach ($videosData as $v) {
            $slug = Str::slug($v['title']) . '-' . Str::random(5);
            Video::create([
                'title'       => $v['title'],
                'slug'        => $slug,
                'category_id' => $catModels[$v['cat']]->id,
                'user_id'     => $adminUser->id,
                'status'      => 'published',
                'is_featured' => $v['featured'],
                'published_at'=> now()->subDays(rand(1,10)),
                'views'       => rand(200,3000),
                'embed_url'   => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            ]);
        }

        // ============================================
        // 10) الإعدادات
        // ============================================
        $settingsData = [
            ['key'=>'site_name',       'value'=>'وكالة الأصيل الإخبارية','group'=>'general'],
            ['key'=>'site_tagline',    'value'=>'أخبار موثوقة وتحليل عميق','group'=>'general'],
            ['key'=>'site_email',      'value'=>'info@alaseel.news','group'=>'contact'],
            ['key'=>'site_phone',      'value'=>'+966 11 000 0000','group'=>'contact'],
            ['key'=>'site_address',    'value'=>'الرياض، المملكة العربية السعودية','group'=>'contact'],
            ['key'=>'facebook_url',    'value'=>'https://facebook.com/alaseel','group'=>'social'],
            ['key'=>'twitter_url',     'value'=>'https://twitter.com/alaseel','group'=>'social'],
            ['key'=>'youtube_url',     'value'=>'https://youtube.com/alaseel','group'=>'social'],
            ['key'=>'instagram_url',   'value'=>'https://instagram.com/alaseel','group'=>'social'],
            ['key'=>'footer_text',     'value'=>'© 2026 وكالة الأصيل الإخبارية. جميع الحقوق محفوظة.','group'=>'general'],
            ['key'=>'articles_per_page','value'=>'12','group'=>'display'],
            ['key'=>'comments_enabled','value'=>'1','group'=>'features'],
        ];
        foreach ($settingsData as $s) {
            Setting::create(array_merge($s,['type'=>'text']));
        }

        // ============================================
        // 11) الإعلانات
        // ============================================
        Advertisement::create([
            'title'=>'إعلان الصفحة الرئيسية','position'=>'homepage',
            'type'=>'banner','status'=>true,'user_id'=>$adminUser->id,
        ]);

        // ============================================
        // 12) الإشعارات
        // ============================================
        $notificationsData = [
            ['title'=>'مقال جديد: قمة عربية استثنائية لبحث التطورات الإقليمية',
             'message'=>'تمت إضافة مقال جديد بحالة: published','type'=>'article',
             'created_at'=>now()->subMinutes(5),'updated_at'=>now()->subMinutes(5)],
            ['title'=>'تعليق جديد بانتظار المراجعة',
             'message'=>'علّق محمد الأحمد على: ارتفاع ملحوظ في أسواق الأسهم الخليجية',
             'type'=>'comment','created_at'=>now()->subMinutes(18),'updated_at'=>now()->subMinutes(18)],
            ['title'=>'رسالة تواصل جديدة من: سارة خالد',
             'message'=>'الموضوع: استفسار عن الإعلانات','type'=>'contact',
             'created_at'=>now()->subHour(),'updated_at'=>now()->subHour()],
            ['title'=>'مشترك جديد في النشرة البريدية',
             'message'=>'news_fan@example.com اشترك في النشرة البريدية.','type'=>'newsletter',
             'read_at'=>now()->subMinutes(30),
             'created_at'=>now()->subHours(2),'updated_at'=>now()->subHours(2)],
            ['title'=>'مستخدم جديد: فيصل الغامدي',
             'message'=>'تم تسجيل مستخدم جديد بالبريد: faisal@example.com','type'=>'user',
             'read_at'=>now()->subHour(),
             'created_at'=>now()->subHours(3),'updated_at'=>now()->subHours(3)],
            ['title'=>'مقال جديد: مبادرات التحول الرقمي تُحدث نقلة في قطاع التعليم',
             'message'=>'تمت إضافة مقال جديد بحالة: draft','type'=>'article',
             'read_at'=>now()->subHours(2),
             'created_at'=>now()->subHours(5),'updated_at'=>now()->subHours(5)],
            ['title'=>'رسالة تواصل جديدة من: عبدالله المطيري',
             'message'=>'الموضوع: شكر وتقدير على التغطية الإعلامية','type'=>'contact',
             'read_at'=>now()->subHours(3),
             'created_at'=>now()->subHours(8),'updated_at'=>now()->subHours(8)],
        ];
        foreach ($notificationsData as $n) {
            Notification::create($n);
        }

        $this->command->info('');
        $this->command->info('✅ Al-Aseel News Platform seeded successfully!');
        $this->command->info('📧 Admin: admin@alaseel.news | Password: password');
        $this->command->info('📧 Editor: editor@alaseel.news | Password: password');
        $this->command->info('📧 Journalist: journalist@alaseel.news | Password: password');
    }
}