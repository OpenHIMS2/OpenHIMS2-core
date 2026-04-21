<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Confirmation Letter</title>
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, Georgia, serif;
            font-size: 12pt;
            color: #1a1a1a;
            background: #f0f0f0;
            padding: 20px;
        }

        /* ── Page / Paper ── */
        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 20mm 22mm;
            box-shadow: 0 4px 32px rgba(0,0,0,.18);
            position: relative;
        }

        /* ── Header ── */
        .letter-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding-bottom: 14px;
            border-bottom: 3px solid #1c3561;
            margin-bottom: 6px;
        }
        .letter-logo {
            width: 90px;
            height: 90px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .letter-logo img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
        }
        .logo-placeholder {
            width: 90px;
            height: 90px;
            border: 2px solid #1c3561;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
        }
        .logo-placeholder svg {
            width: 50px;
            height: 50px;
            fill: #1c3561;
            opacity: .6;
        }
        .letter-org {
            flex: 1;
        }
        .letter-org-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1c3561;
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .letter-org-detail {
            font-size: 9.5pt;
            color: #444;
            line-height: 1.7;
        }
        .letter-org-detail span {
            display: inline-block;
            min-width: 60px;
        }
        .sub-rule {
            height: 1px;
            background: #c7d2fe;
            margin-bottom: 24px;
        }

        /* ── Document title ── */
        .doc-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 22px;
        }
        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1c3561;
            letter-spacing: .04em;
            text-transform: uppercase;
            border-bottom: 2px solid #1c3561;
            padding-bottom: 3px;
        }
        .doc-date {
            font-size: 10pt;
            color: #555;
        }

        /* ── Body text ── */
        .salutation {
            font-size: 11.5pt;
            margin-bottom: 16px;
        }
        .body-text {
            font-size: 11.5pt;
            line-height: 1.75;
            margin-bottom: 18px;
            text-align: justify;
        }

        /* ── Patient Details Table ── */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #1c3561;
            border-bottom: 1px solid #c7d2fe;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 5px 8px;
            font-size: 11pt;
            vertical-align: top;
        }
        .details-table td.label {
            font-weight: bold;
            color: #1c3561;
            width: 30%;
            white-space: nowrap;
        }
        .details-table td.colon {
            width: 4%;
            color: #555;
        }
        .details-table tr:nth-child(even) td {
            background: #f8f9ff;
        }

        /* ── Conditions list ── */
        .conditions-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px;
        }
        .conditions-list li {
            padding: 6px 10px;
            font-size: 11pt;
            border-left: 3px solid #1c3561;
            background: #f8f9ff;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .conditions-list li .cond-year {
            font-size: 9.5pt;
            color: #666;
            font-style: italic;
        }
        .no-conditions {
            font-size: 11pt;
            color: #888;
            font-style: italic;
            margin-bottom: 20px;
        }

        /* ── Closing ── */
        .closing-text {
            font-size: 11.5pt;
            line-height: 1.75;
            margin-bottom: 40px;
            text-align: justify;
        }

        /* ── Signature block ── */
        .signature-block {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-left, .sig-right {
            text-align: center;
        }
        .sig-line {
            display: block;
            width: 200px;
            border-bottom: 1.5px solid #1a1a1a;
            margin-bottom: 6px;
        }
        .sig-label {
            font-size: 10.5pt;
            font-weight: bold;
            line-height: 1.5;
        }
        .sig-sub {
            font-size: 9.5pt;
            color: #555;
            line-height: 1.5;
        }

        /* ── Footer ── */
        .letter-footer {
            position: absolute;
            bottom: 12mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 8.5pt;
            color: #888;
            border-top: 1px solid #e0e0e0;
            padding-top: 6px;
        }

        /* ── Print styles ── */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 15mm 18mm 20mm;
                width: 100%;
                min-height: 100vh;
            }
            .no-print { display: none !important; }
        }

        /* ── Screen-only print bar ── */
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

    {{-- Screen-only toolbar --}}
    <div class="print-bar no-print">
        <button class="btn-print" onclick="window.print()">&#128438; Print / Save as PDF</button>
        <button class="btn-close-tab" onclick="window.close()">&#215; Close</button>
        <span style="margin-left:auto; font-size:11px; color:#888;">Preview — print or save as PDF</span>
    </div>

    <div class="page">

        {{-- ══ LETTERHEAD ══ --}}
        <div class="letter-header">
            <div class="letter-logo">
                @if($institution && $institution->logoUrl())
                    <img src="{{ $institution->logoUrl() }}" alt="Logo">
                @else
                    <div class="logo-placeholder">
                        {{-- Hospital icon SVG --}}
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 2H5C3.9 2 3 2.9 3 4v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 12 12 12s-3.5-1.57-3.5-3.5S10.07 5 12 5zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V18z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="letter-org">
                <div class="letter-org-name">
                    {{ $institution?->name ?? 'Health Institution' }}
                </div>
                <div class="letter-org-detail">
                    @if($institution?->address)
                        <div>{{ $institution->address }}</div>
                    @endif
                    @if($institution?->phone || $institution?->email)
                        <div>
                            @if($institution?->phone)
                                <span>Tel:</span> {{ $institution->phone }}
                                @if($institution?->email) &nbsp;&nbsp; @endif
                            @endif
                            @if($institution?->email)
                                <span>Email:</span> {{ $institution->email }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="sub-rule"></div>

        {{-- ══ DOCUMENT TITLE ══ --}}
        <div class="doc-title-row">
            <div class="doc-title">Clinic Confirmation Letter</div>
            <div class="doc-date">Date: {{ $printDate }}</div>
        </div>

        {{-- ══ SALUTATION ══ --}}
        <div class="salutation">To Whom It May Concern,</div>

        {{-- ══ OPENING PARAGRAPH ══ --}}
        <div class="body-text">
            This is to certify that the following patient is duly registered and is currently under
            active medical supervision and care at
            <strong>{{ $institution?->name ?? 'this institution' }}</strong>.
            All information herein is provided for official purposes and is accurate to the best
            of our knowledge at the time of this letter.
        </div>

        {{-- ══ PATIENT DETAILS ══ --}}
        <div class="section-title">Patient Details</div>
        <table class="details-table">
            @if(!empty($patientData['name']))
            <tr>
                <td class="label">Full Name</td>
                <td class="colon">:</td>
                <td>{{ $patientData['name'] }}</td>
            </tr>
            @endif
            @if(!empty($patientData['age']) || !empty($patientData['gender']))
            <tr>
                <td class="label">Age / Sex</td>
                <td class="colon">:</td>
                <td>{{ $patientData['age'] ? $patientData['age'] . ' years' : '—' }} &nbsp;/&nbsp; {{ $patientData['gender'] ?: '—' }}</td>
            </tr>
            @endif
            @if(!empty($patientData['nic']))
            <tr>
                <td class="label">NIC / ID</td>
                <td class="colon">:</td>
                <td>{{ $patientData['nic'] }}</td>
            </tr>
            @endif
            @if(!empty($patientData['phn']))
            <tr>
                <td class="label">Patient Health No.</td>
                <td class="colon">:</td>
                <td>{{ $patientData['phn'] }}</td>
            </tr>
            @endif
            @if(!empty($patientData['address']))
            <tr>
                <td class="label">Address</td>
                <td class="colon">:</td>
                <td>{{ $patientData['address'] }}</td>
            </tr>
            @endif
            @if(!empty($patientData['mobile']))
            <tr>
                <td class="label">Contact No.</td>
                <td class="colon">:</td>
                <td>{{ $patientData['mobile'] }}</td>
            </tr>
            @endif
        </table>

        {{-- ══ MEDICAL CONDITIONS ══ --}}
        @if(!empty($conditions))
            <div class="section-title">Known Medical Conditions</div>
            <ul class="conditions-list">
                @foreach($conditions as $c)
                    <li>
                        <span>{{ $c['condition'] }}</span>
                        @if(!empty($c['year']))
                            <span class="cond-year">Since {{ $c['year'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- ══ CLOSING PARAGRAPH ══ --}}
        <div class="closing-text">
            The above-mentioned patient is under our regular medical supervision.
            This letter is issued on request for official purposes only and should be treated with the
            appropriate confidentiality.
        </div>

        {{-- ══ SIGNATURE BLOCK ══ --}}
        <div class="signature-block">
            <div class="sig-left">
                <span class="sig-line"></span>
                <div class="sig-label">Medical Officer In Charge</div>
                <div class="sig-sub">{{ $institution?->name ?? '' }}</div>
                <div class="sig-sub">Date: {{ $printDate }}</div>
            </div>
            <div class="sig-right">
                <span class="sig-line" style="width:160px;"></span>
                <div class="sig-label">Official Stamp</div>
            </div>
        </div>

        {{-- ══ FOOTER ══ --}}
        <div class="letter-footer">
            {{ $institution?->name ?? 'Health Institution' }}
            @if($institution?->phone) &nbsp;|&nbsp; Tel: {{ $institution->phone }} @endif
            @if($institution?->email) &nbsp;|&nbsp; {{ $institution->email }} @endif
            &nbsp;|&nbsp; Generated on {{ $printDate }}
        </div>

    </div>

    <script>
        // Auto-print when opened in new tab
        window.addEventListener('load', () => {
            // Small delay so fonts/images load
            setTimeout(() => window.print(), 600);
        });
    </script>
</body>
</html>
