@extends('layouts.admin')
@section('title','لوحة التحكم الرئيسية')
@section('content')

<div class="stats-grid">
  <div class="stat-card" style="border-color:#c9a84c">
    <div class="stat-icon" style="background:rgba(201,168,76,.1);color:#c9a84c">
      <i class="fa-solid fa-newspaper"></i>
    </div>
    <div>
      <div class="stat-value">{{ number_format($stats['total_articles']) }}</div>
      <div class="stat-label">إجمالي المقالات</div>
      <div class="stat-change up">{{ $stats['published_articles'] }} منشور</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#27ae60">
    <div class="stat-icon" style="background:#eafaf1;color:#27ae60">
      <i class="fa-solid fa-eye"></i>
    </div>
    <div>
      <div class="stat-value">{{ number_format($stats['total_views']) }}</div>
      <div class="stat-label">إجمالي المشاهدات</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#e74c3c">
    <div class="stat-icon" style="background:#fdf0f0;color:#e74c3c">
      <i class="fa-solid fa-bolt"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['breaking_news'] }}</div>
      <div class="stat-label">أخبار عاجلة</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#3498db">
    <div class="stat-icon" style="background:#eaf4fd;color:#3498db">
      <i class="fa-solid fa-users"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['total_journalists'] }}</div>
      <div class="stat-label">الصحفيون</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#9b59b6">
    <div class="stat-icon" style="background:#f5f0fb;color:#9b59b6">
      <i class="fa-solid fa-comments"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['total_comments'] }}</div>
      <div class="stat-label">التعليقات</div>
      <div class="stat-change {{ $stats['pending_comments'] > 0 ? 'down' : 'up' }}">
        {{ $stats['pending_comments'] }} بانتظار المراجعة
      </div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#e67e22">
    <div class="stat-icon" style="background:#fef3e8;color:#e67e22">
      <i class="fa-solid fa-clock"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['pending_review'] }}</div>
      <div class="stat-label">بانتظار المراجعة</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#1abc9c">
    <div class="stat-icon" style="background:#e8f8f5;color:#1abc9c">
      <i class="fa-solid fa-bell"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['newsletter_subscribers'] }}</div>
      <div class="stat-label">مشتركو النشرة</div>
    </div>
  </div>
  <div class="stat-card" style="border-color:#f39c12">
    <div class="stat-icon" style="background:#fef9e7;color:#f39c12">
      <i class="fa-solid fa-rectangle-ad"></i>
    </div>
    <div>
      <div class="stat-value">{{ $stats['active_ads'] }}</div>
      <div class="stat-label">إعلانات نشطة</div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-chart-pie" style="color:#c9a84c"></i> توزيع حالات المقالات</span>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="statusChart"></canvas>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-chart-bar" style="color:#c9a84c"></i> أداء التصنيفات</span>
    </div>
    <div class="card-body">
      <div class="chart-container">
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;margin-bottom:20px">
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-fire" style="color:#e74c3c"></i> أكثر المقالات مشاهدة</span>
      <a href="{{ route('admin.articles.index') }}" class="btn btn-outline btn-sm">عرض الكل</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>العنوان</th><th>التصنيف</th><th>المشاهدات</th><th>الحالة</th></tr></thead>
        <tbody>
          @forelse($topArticles as $i => $a)
          <tr>
            <td><span style="font-weight:700;color:#c9a84c">{{ $i+1 }}</span></td>
            <td>
              <a href="{{ route('admin.articles.show',$a) }}" style="color:#1a1a2e;text-decoration:none;font-weight:600;font-size:13px">
                {{ Str::limit($a->title,55) }}
              </a>
              @if($a->is_breaking)<span class="badge badge-danger" style="font-size:10px;margin-right:4px">عاجل</span>@endif
            </td>
            <td><span class="badge badge-info">{{ $a->category?->name ?? '—' }}</span></td>
            <td style="font-weight:700">{{ number_format($a->views) }}</td>
            <td>
              @if($a->status=='published')<span class="badge badge-success">منشور</span>
              @elseif($a->status=='draft')<span class="badge badge-secondary">مسودة</span>
              @else<span class="badge badge-warning">{{ $a->status }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="empty-state">لا توجد مقالات بعد</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:#c9a84c"></i> آخر الأنشطة</span>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($recentActivity as $log)
      <div style="padding:12px 16px;border-bottom:1px solid #f0f0f0;display:flex;gap:10px;align-items:flex-start">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(201,168,76,.1);display:flex;align-items:center;justify-content:center;font-size:12px;color:#c9a84c;flex-shrink:0">
          {{ mb_substr($log->user?->name ?? 'م',0,1) }}
        </div>
        <div style="flex:1">
          <div style="font-size:12.5px;font-weight:600;color:#1a1a2e">{{ Str::limit($log->description,60) }}</div>
          <div style="font-size:11px;color:#999;margin-top:2px">
            {{ $log->user?->name ?? 'النظام' }} · {{ $log->created_at->diffForHumans() }}
          </div>
        </div>
      </div>
      @empty
      <div class="empty-state"><p>لا توجد أنشطة مسجلة</p></div>
      @endforelse
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-user-tie" style="color:#c9a84c"></i> أداء الصحفيين</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>الصحفي</th><th>المقالات</th><th>المشاهدات</th></tr></thead>
        <tbody>
          @forelse($journalistPerformance as $j)
          <tr>
            <td style="font-weight:600">{{ $j->name }}</td>
            <td><span class="badge badge-gold">{{ $j->articles_count }}</span></td>
            <td style="font-weight:700;color:#c9a84c">{{ number_format($j->articles_sum_views ?? 0) }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="empty-state">لا بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="fa-solid fa-folder-open" style="color:#c9a84c"></i> أداء التصنيفات</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>التصنيف</th><th>المقالات</th><th>المشاهدات</th></tr></thead>
        <tbody>
          @forelse($categoryPerformance as $cat)
          <tr>
            <td style="font-weight:600">{{ $cat->name }}</td>
            <td><span class="badge badge-info">{{ $cat->articles_count }}</span></td>
            <td style="font-weight:700;color:#27ae60">{{ number_format($cat->articles_sum_views ?? 0) }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="empty-state">لا بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const statusLabels = @json($statusLabels);
const statusData   = @json($statusCounts);
const catLabels    = @json($categoryPerformance->pluck('name'));
const catData      = @json($categoryPerformance->pluck('articles_count'));

new Chart(document.getElementById('statusChart'),{
  type:'doughnut',
  data:{labels:statusLabels,datasets:[{data:statusData,backgroundColor:['#95a5a6','#f39c12','#3498db','#27ae60','#9b59b6','#7f8c8d','#e74c3c'],borderWidth:0}]},
  options:{plugins:{legend:{position:'bottom',labels:{font:{family:'Cairo',size:11}}}},cutout:'65%'}
});
new Chart(document.getElementById('categoryChart'),{
  type:'bar',
  data:{labels:catLabels,datasets:[{label:'المقالات',data:catData,backgroundColor:'rgba(201,168,76,.8)',borderRadius:6}]},
  options:{indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{grid:{color:'#f0f0f0'}},y:{ticks:{font:{family:'Cairo',size:11}}}}}
});
</script>
@endpush
