<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Test</title>
</head>
<body>
    <h1>Dashboard Admin Test</h1>
    <p>Total Users: {{ $totalUsers ?? 'N/A' }}</p>
    <p>Total PBs: {{ $totalPbs ?? 'N/A' }}</p>
    <p>Total Templates: {{ $totalTemplates ?? 'N/A' }}</p>
    <p>Total Amount: {{ number_format($totalAmount ?? 0) }}</p>

    <hr>
    <p>Test variables:</p>
    <ul>
        <li>monthlyPbs: {{ is_object($monthlyPbs ?? null) ? 'Object' : (is_array($monthlyPbs ?? null) ? 'Array' : 'Not set') }}</li>
        <li>topUsers: {{ is_object($topUsers ?? null) ? 'Object' : (is_array($topUsers ?? null) ? 'Array' : 'Not set') }}</li>
        <li>weeklyTrend: {{ is_object($weeklyTrend ?? null) ? 'Object' : (is_array($weeklyTrend ?? null) ? 'Array' : 'Not set') }}</li>
    </ul>
</body>
</html>
