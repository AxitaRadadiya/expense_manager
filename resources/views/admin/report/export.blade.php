<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 18px;
    }
    th, td {
      border: 1px solid #cfd8dc;
      padding: 8px;
      text-align: left;
    }
    th {
      background: #eaf7f7;
      font-weight: 700;
    }
    h2, h3 {
      margin: 0 0 10px;
    }
    .meta {
      margin-bottom: 14px;
    }
  </style>
</head>
<body>
  <h2>Expense Manager Report</h2>
  <div class="meta">
    <strong>From Date:</strong> {{ $filters['from_date'] ?? 'All' }}<br>
    <strong>To Date:</strong> {{ $filters['to_date'] ?? 'All' }}<br>
    <strong>Timeline Type:</strong> {{ ucfirst($filters['entry_type'] ?? 'all') }}
  </div>

  <h3>Project Wise Summary</h3>
  <table>
    <thead>
      <tr>
        <th>Project</th>
        <th>Expense</th>
        <th>Credit</th>
        <th>Current Balance</th>
      </tr>
    </thead>
    <tbody>
      @forelse($projectSummary as $item)
        <tr>
          <td>{{ $item->project_name }}</td>
          <td>{{ number_format((float) $item->total_expense, 2) }}</td>
          <td>{{ number_format((float) $item->total_credit, 2) }}</td>
          <td>{{ number_format((float) $item->current_balance, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4">No project data found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <h3>User Wise Summary</h3>
  <table>
    <thead>
      <tr>
        <th>User</th>
        <th>Expense</th>
        <th>Credit</th>
        <th>Current Balance</th>
      </tr>
    </thead>
    <tbody>
      @forelse($userSummary as $item)
        <tr>
          <td>{{ $item->user_name }}</td>
          <td>{{ number_format((float) $item->total_expense, 2) }}</td>
          <td>{{ number_format((float) $item->total_credit, 2) }}</td>
          <td>{{ number_format((float) $item->current_balance, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4">No user data found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <h3>Timeline</h3>
  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Date</th>
        <th>Time</th>
        <th>Project</th>
        <th>User</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse($timeline as $item)
        <tr>
          <td>{{ $item->label }}</td>
          <td>{{ optional($item->timeline_at)?->format('d M Y') ?? '-' }}</td>
          <td>{{ optional($item->timeline_at)?->format('h:i A') ?? '-' }}</td>
          <td>{{ $item->project_name }}</td>
          <td>{{ $item->user_name }}</td>
          <td>{{ number_format((float) $item->amount, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6">No timeline data found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
