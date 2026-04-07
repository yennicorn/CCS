@php
    $chartId = $chartId ?? 'gradeEnrollmentChart';
    $byGrade = $stats['by_grade'] ?? [];
    $byGradeGender = $stats['by_grade_gender'] ?? [];

    $labels = array_keys($byGrade);
    $totals = array_map(static fn ($v) => (int) $v, array_values($byGrade));

    $maleCounts = [];
    $femaleCounts = [];
    $unspecifiedCounts = [];

    foreach ($byGrade as $grade => $total) {
        $male = (int) data_get($byGradeGender, $grade . '.male', 0);
        $female = (int) data_get($byGradeGender, $grade . '.female', 0);
        $unspecified = max(0, (int) $total - $male - $female);

        $maleCounts[] = $male;
        $femaleCounts[] = $female;
        $unspecifiedCounts[] = $unspecified;
    }

    $gradePalette = [
        ['#2f7ebd', '#63a6d9'],
        ['#d1732f', '#e4a45f'],
        ['#2f9d67', '#62be8f'],
        ['#7d63c7', '#a188dc'],
        ['#c05252', '#da7d7d'],
        ['#2f9aa4', '#67c1c9'],
        ['#b08a2f', '#d4b363'],
    ];

    $barColors = [];
    foreach ($labels as $index => $label) {
        [$barStart] = $gradePalette[$index % count($gradePalette)];
        $barColors[] = $barStart;
    }
@endphp

<div class="chart-canvas-wrap" role="img" aria-label="Enrollment distribution per grade level">
    <canvas id="{{ $chartId }}">Enrollment bar chart</canvas>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
@endonce

<script>
(() => {
    const canvas = document.getElementById(@json($chartId));
    if (!canvas || !window.Chart) {
        return;
    }

    const labels = @json($labels);
    const totals = @json($totals);
    const maleCounts = @json($maleCounts);
    const femaleCounts = @json($femaleCounts);
    const unspecifiedCounts = @json($unspecifiedCounts);
    const barColors = @json($barColors);
    const fmt = new Intl.NumberFormat();

    const existing = Chart.getChart(canvas);
    if (existing) {
        existing.destroy();
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Total Students',
                    data: totals,
                    backgroundColor: barColors,
                    borderColor: barColors,
                    borderWidth: 1,
                    borderRadius: 12,
                    hoverBorderWidth: 2,
                    maxBarThickness: 64,
                    categoryPercentage: 0.72,
                    barPercentage: 0.9,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 650 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title(items) {
                            return items?.[0]?.label ?? '';
                        },
                        label(ctx) {
                            const index = ctx.dataIndex ?? 0;
                            const total = totals[index] ?? 0;
                            const male = maleCounts[index] ?? 0;
                            const female = femaleCounts[index] ?? 0;
                            const unspecified = unspecifiedCounts[index] ?? 0;

                            const lines = [
                                `Total: ${fmt.format(total)}`,
                                `Male: ${fmt.format(male)}`,
                                `Female: ${fmt.format(female)}`,
                            ];

                            if (unspecified > 0) {
                                lines.push(`Unspecified: ${fmt.format(unspecified)}`);
                            }

                            return lines;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#13466f',
                        font: { weight: '700' },
                        maxRotation: 0,
                        autoSkip: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#1e4f79',
                        callback(value) {
                            return fmt.format(value);
                        },
                    },
                    grid: {
                        color: 'rgba(19, 70, 111, 0.12)',
                    },
                },
            },
        },
    });
})();
</script>

