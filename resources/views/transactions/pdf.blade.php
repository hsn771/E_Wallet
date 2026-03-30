<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .income { color: #10b981; }
        .expense { color: #ef4444; }
        .balance { color: #4f46e5; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 12px 10px;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Virtual Passbook</h1>
        <p>Monthly Transactions Report: <strong>{{ $monthName }} {{ $year }}</strong></p>
        <p>User: {{ $user->name }} ({{ $user->email }})</p>
    </div>

    <div class="summary">
        <table class="summary-grid">
            <tr>
                <td class="summary-item" style="border:none">
                    <div class="summary-label">Total Income</div>
                    <div class="summary-value income">+{{ number_format($totalIncome, 2) }}</div>
                </td>
                <td class="summary-item" style="border:none">
                    <div class="summary-label">Total Expense</div>
                    <div class="summary-value expense">-{{ number_format($totalExpense, 2) }}</div>
                </td>
                <td class="summary-item" style="border:none">
                    <div class="summary-label">Net Balance</div>
                    <div class="summary-value balance">{{ number_format($netBalance, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Wallet</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->date->format('M d, Y') }}</td>
                <td>{{ $tx->category ? $tx->category->name : 'N/A' }}</td>
                <td>{{ $tx->description ?: '-' }}</td>
                <td>{{ $tx->wallet ? $tx->wallet->name : 'Asset' }}</td>
                <td class="text-right {{ $tx->type == 'income' ? 'income' : 'expense' }}">
                    {{ $tx->type == 'income' ? '+' : '-' }}{{ number_format($tx->amount, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }} | E-Wallet Application
    </div>
</body>
</html>
