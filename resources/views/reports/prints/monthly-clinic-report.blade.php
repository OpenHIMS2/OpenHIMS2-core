<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Clinic Report — {{ $monthName }} {{ $year }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #1a1a1a;
            background: #f0f0f0;
            padding: 20px;
        }

        /* ── Paper ── */
        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 14mm 18mm 20mm;
            box-shadow: 0 4px 32px rgba(0,0,0,.18);
            position: relative;
        }

        /* ── Header ── */
        .report-header {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            border-bottom: 4px solid #1c3561;
            padding-bottom: 12px;
            margin-bottom: 4px;
        }
        .logo-box {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-box img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }
        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: 2px solid #1c3561;
            border-radius: 8px;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-placeholder svg {
            width: 44px;
            height: 44px;
            fill: #1c3561;
            opacity: .6;
        }
        .header-text { flex: 1; }
        .inst-name {
            font-size: 17pt;
            font-weight: bold;
            color: #1c3561;
            line-height: 1.15;
            margin-bottom: 3px;
        }
        .report-type {
            font-size: 12pt;
            font-weight: bold;
            color: #e65100;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }
        .header-meta {
            font-size: 9pt;
            color: #555;
            line-height: 1.8;
        }
        .sub-stripe {
            height: 4px;
            background: linear-gradient(90deg, #1c3561 0%, #e65100 60%, #f0f0f0 100%);
            margin-bottom: 16px;
        }

        /* ── Report period badge ── */
        .period-badge {
            display: inline-block;
            background: #1c3561;
            color: #fff;
            font-size: 10.5pt;
            font-weight: bold;
            padding: 4px 14px;
            border-radius: 4px;
            margin-bottom: 16px;
            letter-spacing: .04em;
        }

        /* ── Summary cards ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
            background: #fafafa;
        }
        .summary-card.highlight {
            background: #1c3561;
            border-color: #1c3561;
            color: #fff;
        }
        .summary-card .big-num {
            font-size: 22pt;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 4px;
        }
        .summary-card.highlight .big-num { color: #fff; }
        .summary-card .card-label {
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: inherit;
            opacity: .8;
        }
        .opd-card   { border-color: #1565c0; background: #e3f2fd; color: #0d47a1; }
        .clinic-card{ border-color: #2e7d32; background: #e8f5e9; color: #1b5e20; }
        .other-card { border-color: #827717; background: #f9fbe7; color: #558b2f; }

        /* ── Section title ── */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1c3561;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 2px solid #1c3561;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }

        /* ── Daily attendance table ── */
        .attend-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 20px;
        }
        .attend-table th {
            background: #1c3561;
            color: #fff;
            padding: 6px 8px;
            text-align: center;
            font-size: 9pt;
            letter-spacing: .04em;
        }
        .attend-table th:first-child { text-align: left; }
        .attend-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
        }
        .attend-table td:first-child { text-align: left; font-weight: 500; }
        .attend-table tr:nth-child(even) td { background: #f8f9fa; }
        .attend-table tr.has-data td { font-weight: 600; }
        .attend-table tr.total-row td {
            background: #e8eaf6;
            font-weight: bold;
            border-top: 2px solid #1c3561;
            font-size: 10pt;
        }
        .attend-table tr.total-row td:first-child { color: #1c3561; }

        /* ── Day of week badge ── */
        .dow-badge {
            display: inline-block;
            font-size: 7.5pt;
            background: #f0f0f0;
            border-radius: 3px;
            padding: 1px 4px;
            color: #888;
            margin-left: 4px;
        }
        .dow-badge.sun, .dow-badge.sat { background: #fff3e0; color: #e65100; }

        /* ── Peak day ── */
        .peak-row td { background: #fffde7 !important; }

        /* ── Observations ── */
        .obs-box {
            border: 1px dashed #1c3561;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            background: #f8f9ff;
        }
        .obs-box p {
            font-size: 10pt;
            line-height: 1.7;
            color: #333;
        }

        /* ── Signature ── */
        .sig-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
        }
        .sig-area { text-align: center; }
        .sig-line {
            display: block;
            width: 200px;
            border-bottom: 1.5px solid #222;
            margin-bottom: 5px;
        }
        .sig-label { font-size: 10.5pt; font-weight: bold; }
        .sig-sub   { font-size: 9pt; color: #555; line-height: 1.6; }

        /* ── Footer ── */
        .page-footer {
            position: absolute;
            bottom: 10mm;
            left: 18mm;
            right: 18mm;
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 5px;
        }

        /* ── Print ── */
        @media print {
            body { background: none; padding: 0; }
            .page { box-shadow: none; margin: 0; width: 100%; padding: 12mm 16mm 18mm; }
            .no-print { display: none !important; }
        }

        /* ── Screen toolbar ── */
        .print-bar {
            max-width: 210mm;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: Arial, sans-serif;
        }
        .print-bar button {
            font-family: Arial, sans-serif;
            font-size: 13px;
            padding: 7px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-print { background: #1c3561; color: #fff; }
        .btn-close-tab { background: #e9ecef; color: #333; }
    </style>
</head>
<body>

    <div class="print-bar no-print">
        <button class="btn-print" onclick="window.print()">&#128438; Print / Save as PDF</button>
        <button class="btn-close-tab" onclick="window.close()">&#215; Close</button>
        <span style="margin-left:auto; font-size:11px; color:#888;">Preview — print or save as PDF</span>
    </div>

    <div class="page">

        {{-- ══ HEADER ══ --}}
        <div class="report-header">
            <div class="logo-box">
                @if($institution && $institution->logoUrl())
                    <img src="{{ $institution->logoUrl() }}" alt="Logo">
                @else
                    <div class="logo-placeholder">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 2H5C3.9 2 3 2.9 3 4v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 12 12 12s-3.5-1.57-3.5-3.5S10.07 5 12 5zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V18z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="header-text">
                <div class="inst-name">{{ $institution?->name ?? ($unit?->institution?->name ?? 'Health Institution') }}</div>
                <div class="report-type">Monthly Clinic Report</div>
                <div class="header-meta">
                    @if($unit) Unit: <strong>{{ $unit->name }}</strong> &nbsp;|&nbsp; @endif
                    Period: <strong>{{ $monthName }} {{ $year }}</strong>
                    @if($institution?->address) &nbsp;|&nbsp; {{ $institution->address }} @endif
                    @if($institution?->phone)
                        <br>Tel: {{ $institution->phone }}
                        @if($institution?->email) &nbsp;|&nbsp; Email: {{ $institution->email }} @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="sub-stripe"></div>

        {{-- ══ REPORT PERIOD ══ --}}
        <div class="period-badge">
            Report Period: {{ $monthName }} {{ $year }}
            &nbsp;&mdash;&nbsp;
            {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
        </div>

        {{-- ══ SUMMARY CARDS ══ --}}
        <div class="section-title">Summary</div>
        <div class="summary-grid">
            <div class="summary-card highlight">
                <div class="big-num">{{ $totalVisits }}</div>
                <div class="card-label">Total Visits</div>
            </div>
            <div class="summary-card opd-card">
                <div class="big-num">{{ $totalOpd }}</div>
                <div class="card-label">OPD Visits</div>
            </div>
            <div class="summary-card clinic-card">
                <div class="big-num">{{ $totalClinic }}</div>
                <div class="card-label">Clinic Visits</div>
            </div>
            <div class="summary-card other-card">
                <div class="big-num">{{ $totalOther }}</div>
                <div class="card-label">Other</div>
            </div>
        </div>

        {{-- Key metrics row --}}
        <div style="display:flex; gap:20px; font-size:9.5pt; color:#444; margin-bottom:18px; flex-wrap:wrap;">
            <div><strong>Unique Patients:</strong> {{ $uniquePatients }}</div>
            <div><strong>Working Days (with activity):</strong>
                {{ collect($dailyData)->where('total', '>', 0)->count() }}</div>
            @if($peakDay && $peakDay['total'] > 0)
                <div><strong>Peak Day:</strong>
                    {{ date('d M Y', strtotime($peakDay['date'])) }}
                    ({{ $peakDay['total'] }} visits)</div>
            @endif
            @if($totalVisits > 0)
                @php
                    $activeDays = collect($dailyData)->where('total', '>', 0)->count();
                    $avg = $activeDays > 0 ? round($totalVisits / $activeDays, 1) : 0;
                @endphp
                <div><strong>Avg. per Active Day:</strong> {{ $avg }}</div>
            @endif
        </div>

        {{-- ══ DAILY ATTENDANCE TABLE ══ --}}
        <div class="section-title">Daily Attendance</div>
        <table class="attend-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>OPD</th>
                    <th>Clinic</th>
                    <th>Other</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyData as $day)
                    @php
                        $dow     = date('D', strtotime($day['date']));
                        $isPeak  = $peakDay && $day['date'] === $peakDay['date'] && $day['total'] > 0;
                        $hasData = $day['total'] > 0;
                        $isWeekend = in_array($dow, ['Sat', 'Sun']);
                    @endphp
                    <tr class="{{ $isPeak ? 'peak-row' : '' }} {{ $hasData ? 'has-data' : '' }}">
                        <td>
                            {{ date('d M', strtotime($day['date'])) }}
                            <span class="dow-badge {{ strtolower($dow) }}">{{ $dow }}</span>
                            @if($isPeak)
                                <span style="font-size:7pt; color:#e65100; font-weight:bold; margin-left:3px;">&#9733; Peak</span>
                            @endif
                        </td>
                        <td>{{ $day['opd']    ?: '—' }}</td>
                        <td>{{ $day['clinic']  ?: '—' }}</td>
                        <td>{{ $day['other']   ?: '—' }}</td>
                        <td style="{{ $hasData ? 'font-weight:bold;color:#1c3561;' : 'color:#ccc;' }}">
                            {{ $hasData ? $day['total'] : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td>{{ $totalOpd }}</td>
                    <td>{{ $totalClinic }}</td>
                    <td>{{ $totalOther }}</td>
                    <td>{{ $totalVisits }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ══ OBSERVATIONS ══ --}}
        <div class="section-title">Observations &amp; Remarks</div>
        <div class="obs-box">
            <p>
                @if($totalVisits === 0)
                    No clinic activity was recorded for {{ $monthName }} {{ $year }}.
                @else
                    During {{ $monthName }} {{ $year }}, a total of <strong>{{ $totalVisits }}</strong>
                    patient visit{{ $totalVisits != 1 ? 's' : '' }} were recorded
                    @if($unit) at <strong>{{ $unit->name }}</strong> @endif.
                    @if($totalOpd > 0) OPD accounted for {{ $totalOpd }} visit{{ $totalOpd != 1 ? 's' : '' }} ({{ $totalVisits > 0 ? round($totalOpd/$totalVisits*100) : 0 }}%). @endif
                    @if($totalClinic > 0) Clinic visits: {{ $totalClinic }} ({{ $totalVisits > 0 ? round($totalClinic/$totalVisits*100) : 0 }}%). @endif
                    @if($uniquePatients > 0) A total of <strong>{{ $uniquePatients }}</strong> unique patient{{ $uniquePatients != 1 ? 's' : '' }} were seen. @endif
                    @if($peakDay && $peakDay['total'] > 0)
                        The highest daily attendance was recorded on
                        <strong>{{ date('d F Y', strtotime($peakDay['date'])) }}</strong>
                        with {{ $peakDay['total'] }} visits.
                    @endif
                @endif
            </p>
        </div>

        {{-- ══ SIGNATURE ══ --}}
        <div class="sig-block">
            <div class="sig-area">
                <span class="sig-line"></span>
                <div class="sig-label">Medical Officer In Charge</div>
                <div class="sig-sub">{{ $institution?->name ?? '' }}</div>
                <div class="sig-sub">Date: {{ $printDate }}</div>
            </div>
            <div class="sig-area">
                <span class="sig-line" style="width:180px;"></span>
                <div class="sig-label">Unit / Clinic In Charge</div>
                <div class="sig-sub">{{ $unit?->name ?? '' }}</div>
            </div>
            <div class="sig-area">
                <span class="sig-line" style="width:150px;"></span>
                <div class="sig-label">Official Stamp</div>
            </div>
        </div>

        {{-- ══ FOOTER ══ --}}
        <div class="page-footer">
            <span>{{ $institution?->name ?? 'Health Institution' }}
                @if($institution?->phone) &nbsp;|&nbsp; Tel: {{ $institution->phone }} @endif
            </span>
            <span>Report generated: {{ $printDate }}</span>
        </div>

    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 600);
        });
    </script>
</body>
</html>
